<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Cita;
use Carbon\Carbon;
use App\Models\Horario;
use App\Helpers\NotificacionHelper;
use App\Helpers\CorreoHelper;

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

        $estadoAnterior = $cita->estado;

        $cita->update([
            'Fecha_y_hora' => Carbon::parse($request->Fecha_y_hora),
            'estado'       => $request->estado,
        ]);

        $cita->load(['medico', 'paciente']);
        $urlCita = '/citas';
        $fechaFormateada = Carbon::parse($request->Fecha_y_hora)->format('d/m/Y H:i');
        $nombreMedico    = $cita->medico->name . ' ' . $cita->medico->Apellidos;

        // Si la cita fue programada, notificar al médico y paciente
        if ($request->estado === 'Programada' && $estadoAnterior !== 'Programada') {
            NotificacionHelper::enviar(
                $cita,
                $cita->medico_id,
                'Cita programada',
                "Tu cita del {$fechaFormateada} ha sido programada exitosamente",
                'success',
                $urlCita
            );

            NotificacionHelper::enviar(
                $cita,
                $cita->paciente_id,
                'Cita programada',
                "Tu cita con el Dr. {$nombreMedico} del {$fechaFormateada} fue programada exitosamente",
                'success',
                $urlCita
            );
        }
        
        if ($request->estado === 'Programada' && $estadoAnterior !== 'Programada') {
            CorreoHelper::citaProgramada($cita);
        }

        if ($request->estado === 'Cancelada' && $estadoAnterior !== 'Cancelada') {
            CorreoHelper::citaCancelada($cita);
        }

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

        $cita = Cita::create([
            'medico_id'    => $request->medico_id,
            'paciente_id'  => $request->paciente_id,
            'Fecha_y_hora' => $fechaHora,
            'estado'       => $request->estado,
        ]);

        $cita->load(['medico', 'paciente']);
        $urlCita = '/citas';
        $fechaFormateada = $fechaHora->format('d/m/Y H:i');
        $nombrePaciente  = $cita->paciente->name . ' ' . $cita->paciente->Apellidos;
        $nombreMedico    = $cita->medico->name . ' ' . $cita->medico->Apellidos;

        // Notificar al médico
        NotificacionHelper::enviar(
            $cita,
            $cita->medico_id,
            'Nueva cita asignada',
            "Se agendó una cita con {$nombrePaciente} el {$fechaFormateada}",
            'info',
            $urlCita
        );

        // Notificar a todos los admins
        foreach (NotificacionHelper::getAdmins() as $admin) {
            NotificacionHelper::enviar(
                $cita,
                $admin->id,
                'Nueva cita creada',
                "El Dr. {$nombreMedico} tiene una cita con {$nombrePaciente} el {$fechaFormateada}",
                'info',
                $urlCita
            );
        }

        CorreoHelper::citaCreada($cita);
        return response()->json(['success' => true]);
    }

    private function validarHorarioMedico($medico, Carbon $fechaHora)
    {
        $horario = $medico->horario;

        if (!$horario) {
            return 'El médico no tiene horario asignado';
        }

        // Validar día de semana
        $diasMap = [
            1 => 'lunes',
            2 => 'martes',
            3 => 'miercoles',
            4 => 'jueves',
            5 => 'viernes',
            6 => 'sabado',
            0 => 'domingo',
        ];

        $diaSemana = $diasMap[$fechaHora->dayOfWeek];
        $diasPermitidos = $horario->dias_semana ?? [];

        if (!in_array($diaSemana, $diasPermitidos)) {
            $dias = implode(', ', $diasPermitidos);
            return "El médico no atiende los días {$diaSemana}. Días disponibles: {$dias}";
        }

        // Validar hora
        $hora = $fechaHora->format('H:i');

        if ($hora < $horario->hora_inicio || $hora >= $horario->hora_fin) {
            return "La hora seleccionada está fuera del horario de atención ({$horario->hora_inicio} - {$horario->hora_fin})";
        }

        // Validar almuerzo
        if (
            $horario->almuerzo_inicio &&
            $hora >= $horario->almuerzo_inicio &&
            $hora < $horario->almuerzo_fin
        ) {
            return "El médico se encuentra en horario de almuerzo ({$horario->almuerzo_inicio} - {$horario->almuerzo_fin})";
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

        // Validar que la fecha sea un día que atiende el médico
        $diasMap = [
            1 => 'lunes', 2 => 'martes', 3 => 'miercoles',
            4 => 'jueves', 5 => 'viernes', 6 => 'sabado', 0 => 'domingo',
        ];

        $fecha     = Carbon::parse($request->fecha);
        $diaSemana = $diasMap[$fecha->dayOfWeek];
        $diasPermitidos = $horario->dias_semana ?? [];

        if (!in_array($diaSemana, $diasPermitidos)) {
            return response()->json([]);
        }

        $duracion = $horario->hora_atencion;
        $inicio   = Carbon::parse($request->fecha . ' ' . $horario->hora_inicio);
        $fin      = Carbon::parse($request->fecha . ' ' . $horario->hora_fin);
        $horas    = [];

        while ($inicio->copy()->addMinutes($duracion)->lte($fin)) {
            $bloqueFin = $inicio->copy()->addMinutes($duracion);

            // Saltar almuerzo
            if ($horario->almuerzo_inicio && $horario->almuerzo_fin) {
                $almuerzoInicio = Carbon::parse($request->fecha . ' ' . $horario->almuerzo_inicio);
                $almuerzoFin    = Carbon::parse($request->fecha . ' ' . $horario->almuerzo_fin);

                if ($inicio < $almuerzoFin && $bloqueFin > $almuerzoInicio) {
                    $inicio = $almuerzoFin->copy();
                    continue;
                }
            }

            // Verificar si ya está ocupada
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