<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Cita;
use Carbon\Carbon;
use App\Models\Horario;

class CitaController extends Controller
{
    public function index()
{
    $userId = session('user_id');
    $cargo  = session('cargo');
    $perPage = 10; 

    if ($cargo === 'Admin') {
        $Citas = Cita::with(['medico', 'paciente', 'enfermedad', 'tratamiento'])
            ->orderBy('Fecha_y_hora', 'asc') 
            ->paginate($perPage);
    } elseif ($cargo === 'Medico') {
        $Citas = Cita::with(['medico', 'paciente', 'enfermedad', 'tratamiento'])
            ->where('medico_id', $userId)
            ->orderBy('Fecha_y_hora', 'asc') 
            ->paginate($perPage);
    } elseif ($cargo === 'Paciente') {
        $Citas = Cita::with(['medico', 'paciente', 'enfermedad', 'tratamiento'])
            ->where('paciente_id', $userId)
            ->orderBy('Fecha_y_hora', 'asc') 
            ->paginate($perPage);
    } else {
        $Citas = collect();
    }

    $medicos = User::whereHas('cargo', fn($q) =>
        $q->where('Nombre_cargo', 'Medico')
    )->get();

    $pacientes = User::whereHas('cargo', fn($q) =>
        $q->where('Nombre_cargo', 'Paciente')
    )->get();

    return view('citas', compact('Citas', 'medicos', 'pacientes'));
}

    public function edit($id)
    {
        $cita = Cita::with(['medico', 'paciente'])->find($id);

        if (!$cita) {
            return response()->json(['error' => 'Cita no encontrada'], 404);
        }

        return response()->json($cita);
    }

    public function destroy($id)
    {
        if (session('cargo') !== 'Admin') {
            abort(403, 'No autorizado');
        }

        $cita = Cita::find($id);
        if ($cita) {
            $cita->delete();
            return redirect()->route('citas')->with('success', 'Cita eliminada correctamente.');
        } else {
            return redirect()->route('citas')->with('error', 'Cita no encontrada.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'Fecha_y_hora' => 'required',
            'estado'       => 'required|in:Pendiente,Programada,Finalizada,Cancelada', // ← actualizado
        ]);

        $cita = Cita::findOrFail($id);

        $medico    = $cita->medico;
        $fechaHora = Carbon::parse($request->Fecha_y_hora);

        $error = $this->validarSeparacionCitas($medico->id, $fechaHora, $cita->id);
        if ($error) {
            return response()->json(['message' => $error], 422);
        }

        $error = $this->validarHorarioMedico($medico, $fechaHora);
        if ($error) {
            return response()->json(['message' => $error], 422);
        }

        $cita->update([
            'Fecha_y_hora' => Carbon::parse($request->Fecha_y_hora),
            'estado'       => $request->estado,
        ]);

        return response()->json(['ok' => true]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'medico_id'    => 'required|exists:users,id',
            'paciente_id'  => 'required|exists:users,id',
            'Fecha_y_hora' => 'required|DATE_FORMAT:Y-m-d H:i',
            'estado'       => 'required|in:Pendiente,Programada,Finalizada,Cancelada', // ← actualizado
        ]);

        $medico = User::with('horario', 'cargo')->findOrFail($request->medico_id);

        if ($medico->cargo->Nombre_cargo !== 'Medico') {
            return response()->json(['message' => 'El usuario seleccionado no es médico'], 422);
        }

        $fechaHora = Carbon::parse($request->Fecha_y_hora);

        $error = $this->validarHorarioMedico($medico, $fechaHora);
        if ($error) {
            return response()->json(['message' => $error], 422);
        }

        $error = $this->validarSeparacionCitas($medico->id, $fechaHora);
        if ($error) {
            return response()->json(['message' => $error], 422);
        }

        Cita::create([
            'medico_id'    => $request->medico_id,
            'paciente_id'  => $request->paciente_id,
            'Fecha_y_hora' => $fechaHora,
            'estado'       => $request->estado,
        ]);

        return response()->json(['success' => true]);
    }

    private function validarHorarioMedico($medico, Carbon $fechaHora)
    {
        $horario = $medico->horario;

        if (!$horario) {
            return 'El medico no tiene horario asignado';
        }

        $hora = $fechaHora->format('H:i');

        if ($hora < $horario->hora_inicio || $hora > $horario->hora_fin) {
            return 'La hora seleccionada esta fuera del horario de atencion';
        }

        if (
            $horario->almuerzo_inicio &&
            $hora >= $horario->almuerzo_inicio &&
            $hora <= $horario->almuerzo_fin
        ) {
            return 'El medico se encuentra en horario de almuerzo';
        }

        return null;
    }

    private function validarSeparacionCitas(int $idMedico, Carbon $fechaHora, ?int $excluirCitaId = null)
    {
        $medico   = User::with('horario')->findOrFail($idMedico);
        $duracion = $medico->horario->hora_atencion;

        $query = Cita::where('medico_id', $idMedico)
            ->whereBetween('Fecha_y_hora', [
                $fechaHora->copy()->subMinutes($duracion),
                $fechaHora->copy()->addMinutes($duracion),
            ]);

        if ($excluirCitaId) {
            $query->where('id', '!=', $excluirCitaId);
        }

        if ($query->exists()) {
            return "Ya existe una cita dentro de los {$duracion} minutos permitidos";
        }

        return null;
    }

    public function horasDisponibles(Request $request)
    {
        $request->validate([
            'medico_id' => 'required|exists:users,id',
            'fecha'     => 'required|date',
        ]);

        $medico  = User::with('horario')->findOrFail($request->medico_id);
        $horario = $medico->horario;

        if (!$horario) {
            return response()->json([]);
        }

        $duracion = $horario->hora_atencion;
        $inicio   = Carbon::parse($request->fecha . ' ' . $horario->hora_inicio);
        $fin      = Carbon::parse($request->fecha . ' ' . $horario->hora_fin);
        $horas    = [];

        while ($inicio->copy()->addMinutes($duracion)->lte($fin)) {
            $bloqueFin = $inicio->copy()->addMinutes($duracion);

            if ($horario->almuerzo_inicio && $horario->almuerzo_fin) {
                $almuerzoInicio = Carbon::parse($request->fecha . ' ' . $horario->almuerzo_inicio);
                $almuerzoFin    = Carbon::parse($request->fecha . ' ' . $horario->almuerzo_fin);

                if ($inicio < $almuerzoFin && $bloqueFin > $almuerzoInicio) {
                    $inicio->addMinutes($duracion);
                    continue;
                }
            }

            $ocupada = Cita::where('medico_id', $medico->id)
                ->where('Fecha_y_hora', $inicio->format('Y-m-d H:i:s'))
                ->exists();

            if (!$ocupada) {
                $horas[] = $inicio->format('H:i');
            }

            $inicio->addMinutes($duracion);
        }

        return response()->json($horas);
    }
}