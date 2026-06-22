<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Cita;
use App\Models\MedicoPrestacion;
use Carbon\Carbon;
use App\Models\Horario;
use App\Helpers\NotificacionHelper;
use App\Helpers\CorreoHelper;

class CitaController extends Controller
{
    /**
     * Devuelve los IDs de médicos del mismo centro que el admin actual.
     * Si no hay centro en sesión, devuelve array vacío (sin acceso cruzado).
     */
    private function medicoIdsDeCentro(): array
    {
        $centroId = session('centro_medico_id');
        if (!$centroId) return [];

        return User::where('centro_medico_id', $centroId)
            ->whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Medico'))
            ->pluck('id')
            ->toArray();
    }

    public function index(\Illuminate\Http\Request $request)
    {
        $userId  = session('user_id');
        $cargo   = session('cargo');
        $perPage = 10;

        if ($cargo === 'Admin') {
            // Admin solo ve citas de los médicos de su centro
            $medicoIds = $this->medicoIdsDeCentro();

            $query = Cita::with(['medico', 'paciente', 'enfermedad', 'tratamiento', 'prestacion'])
            ->whereIn('medico_id', $medicoIds);

        if ($request->filled('medico_id')) {
            $query->where('medico_id', $request->medico_id);
        }

        if ($request->filled('paciente_id')) {
            $query->where('paciente_id', $request->paciente_id);
        }

        $Citas = $query->orderBy('Fecha_y_hora', 'asc')->paginate($perPage)->withQueryString();

        } elseif ($cargo === 'Medico') {
            $Citas = Cita::with(['medico', 'paciente', 'enfermedad', 'tratamiento', 'prestacion'])
                ->where('medico_id', $userId)
                ->orderBy('Fecha_y_hora', 'asc')
                ->paginate($perPage);

        } elseif ($cargo === 'Paciente') {
            $Citas = Cita::with(['medico', 'paciente', 'enfermedad', 'tratamiento', 'prestacion'])
                ->where('paciente_id', $userId)
                ->orderBy('Fecha_y_hora', 'asc')
                ->paginate($perPage);

        } else {
            $Citas = collect();
        }

        // Médicos ACTIVOS del mismo centro para el select de nueva cita
        $centroId = session('centro_medico_id');

        $medicos = User::whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Medico'))
            ->where('activo', 1)
            ->when($centroId, fn($q) => $q->where('centro_medico_id', $centroId))
            ->with('medicoPrestaciones.prestacion')
            ->get();

        $pacientes = User::whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Paciente'))
            ->when($centroId, fn($q) => $q->where('centro_medico_id', $centroId))
            ->get();

        $todosMedicos = User::whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Medico'))
            ->when($centroId, fn($q) => $q->where('centro_medico_id', $centroId))
            ->get();

        return view('citas', compact('Citas', 'medicos', 'pacientes', 'todosMedicos'));
    }

    public function edit($id)
    {
        $cita = Cita::with(['medico', 'paciente', 'prestacion'])->find($id);
        if (!$cita) {
            return response()->json(['error' => 'Cita no encontrada'], 404);
        }

        // Admin solo puede editar citas de su centro
        if (session('cargo') === 'Admin') {
            $medicoIds = $this->medicoIdsDeCentro();
            if (!in_array($cita->medico_id, $medicoIds)) {
                return response()->json(['error' => 'No autorizado'], 403);
            }
        }

        return response()->json($cita);
    }

    public function destroy($id)
    {
        $cargo = session('cargo');

        if (session('admin') !== 1 && $cargo !== 'Paciente') {
            abort(403, 'No autorizado');
        }

        $cita = Cita::find($id);
        if (!$cita) {
            return redirect()->route('citas')->with('error', 'Cita no encontrada.');
        }

        // Admin: solo puede eliminar citas de su centro
        if ($cargo === 'Admin') {
            $medicoIds = $this->medicoIdsDeCentro();
            if (!in_array($cita->medico_id, $medicoIds)) {
                abort(403, 'No autorizado');
            }
        }

        if ($cargo === 'Paciente' && $cita->paciente_id !== session('user_id')) {
            abort(403, 'No autorizado');
        }

        $cita->delete();
        return redirect()->route('citas')->with('success', 'Cita eliminada/cancelada correctamente.');
    }

    public function update(Request $request, $id)
    {
        if (session('admin') !== 1) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $request->validate([
            'Fecha_y_hora' => 'required',
            'estado'       => 'required|in:Pendiente,Programada,Finalizada,Cancelada',
        ]);

        $cita = Cita::findOrFail($id);

        // Admin: solo puede editar citas de su centro
        $medicoIds = $this->medicoIdsDeCentro();
        if (!empty($medicoIds) && !in_array($cita->medico_id, $medicoIds)) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $medico    = $cita->medico;
        $fechaHora = Carbon::parse($request->Fecha_y_hora);

        $error = $this->validarHorarioMedico($medico, $fechaHora);
        if ($error) {
            return response()->json(['message' => $error], 422);
        }

        $estadoAnterior = $cita->estado;

        $cita->update([
            'Fecha_y_hora' => $fechaHora,
            'estado'       => $request->estado,
        ]);

        $cita->load(['medico', 'paciente']);
        $urlCita         = '/citas';
        $fechaFormateada = $fechaHora->format('d/m/Y H:i');
        $nombreMedico    = $cita->medico->name . ' ' . $cita->medico->Apellidos;

        if ($request->estado === 'Programada' && $estadoAnterior !== 'Programada') {
            NotificacionHelper::enviar($cita, $cita->medico_id, 'Cita programada',
                "Tu cita del {$fechaFormateada} ha sido programada exitosamente", 'success', $urlCita);
            NotificacionHelper::enviar($cita, $cita->paciente_id, 'Cita programada',
                "Tu cita con el Dr. {$nombreMedico} del {$fechaFormateada} fue programada exitosamente", 'success', $urlCita);
            CorreoHelper::citaProgramada($cita);
        }

        if ($request->estado === 'Cancelada' && $estadoAnterior !== 'Cancelada') {
            CorreoHelper::citaCancelada($cita);
        }

        return response()->json(['ok' => true]);
    }

    public function citasPendientesPaciente(\App\Models\User $paciente)
    {
        if (session('admin') !== 1) abort(403);

        // Verificar que el paciente pertenezca al centro del admin
        $centroId = session('centro_medico_id');
        if ($centroId && $paciente->centro_medico_id !== $centroId) {
            abort(403);
        }

        $citas = Cita::with(['medico', 'prestacion'])
            ->where('paciente_id', $paciente->id)
            ->where('estado', 'Pendiente')
            ->orderBy('Fecha_y_hora', 'asc')
            ->get()
            ->map(fn($c) => [
                'id'        => $c->id,
                'codigo'    => $c->codigo_cita ?? 'CIT-' . $c->id,
                'fecha'     => \Carbon\Carbon::parse($c->Fecha_y_hora)->format('d/m/Y H:i'),
                'medico'    => $c->medico->name . ' ' . $c->medico->Apellidos,
                'prestacion'=> $c->prestacion?->nombre,
                'estado'    => $c->estado,
            ]);

        return response()->json($citas);
    }

    public function store(Request $request)
    {
        $cargo  = session('cargo');
        $userId = session('user_id');

        if ($cargo === 'Medico') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $request->validate([
            'medico_id'      => 'required|exists:users,id',
            'paciente_id'    => 'required|exists:users,id',
            'prestacion_id'  => 'required|exists:prestaciones,id',
            'Fecha_y_hora'   => 'required|date_format:Y-m-d H:i',
            'estado'         => 'required|in:Pendiente,Programada,Finalizada,Cancelada',
        ]);

        if ($cargo === 'Paciente' && (int) $request->paciente_id !== (int) $userId) {
            return response()->json(['message' => 'Solo puedes crear citas para ti mismo'], 403);
        }

        // Admin: verificar que el médico sea de su centro
        if ($cargo === 'Admin') {
            $medicoIds = $this->medicoIdsDeCentro();
            if (!in_array((int) $request->medico_id, $medicoIds)) {
                return response()->json(['message' => 'El médico no pertenece a tu centro'], 403);
            }
        }

        $medico = User::with('horario', 'cargo')->findOrFail($request->medico_id);

        if ($medico->cargo->Nombre_cargo !== 'Medico') {
            return response()->json(['message' => 'El usuario seleccionado no es médico'], 422);
        }

        $fechaHora = Carbon::parse($request->Fecha_y_hora);

        $error = $this->validarHorarioMedico($medico, $fechaHora);
        if ($error) {
            return response()->json(['message' => $error], 422);
        }

        $mp = MedicoPrestacion::where('id_medico', $request->medico_id)
            ->where('id_prestacion', $request->prestacion_id)
            ->first();

        if (!$mp) {
            return response()->json(['message' => 'El médico no ofrece esa prestación'], 422);
        }

        $horaStr = $fechaHora->format('H:i');
        $slotsOk = $mp->slotsDisponibles();

        if (!in_array($horaStr, $slotsOk)) {
            return response()->json(['message' => 'Hora fuera del rango de la prestación'], 422);
        }

        $tomadas = Cita::where('medico_id', $request->medico_id)
            ->where('prestacion_id', $request->prestacion_id)
            ->where('Fecha_y_hora', $fechaHora->format('Y-m-d H:i:s'))
            ->count();

        if ($tomadas >= $mp->cant_online) {
            return response()->json(['message' => 'No hay cupos disponibles para esa hora'], 422);
        }

        $cita = Cita::create([
            'medico_id'     => $request->medico_id,
            'paciente_id'   => $request->paciente_id,
            'prestacion_id' => $request->prestacion_id,
            'Fecha_y_hora'  => $fechaHora,
            'estado'        => $request->estado,
        ]);

        $cita->load(['medico', 'paciente']);
        $urlCita         = '/citas';
        $fechaFormateada = $fechaHora->format('d/m/Y H:i');
        $nombrePaciente  = $cita->paciente->name . ' ' . $cita->paciente->Apellidos;
        $nombreMedico    = $cita->medico->name . ' ' . $cita->medico->Apellidos;

        NotificacionHelper::enviar($cita, $cita->medico_id, 'Nueva cita asignada',
            "Se agendó una cita con {$nombrePaciente} el {$fechaFormateada}", 'info', $urlCita);

        foreach (NotificacionHelper::getAdmins() as $admin) {
            // Solo notificar admins del mismo centro
            if ($admin->centro_medico_id === $cita->medico->centro_medico_id) {
                NotificacionHelper::enviar($cita, $admin->id, 'Nueva cita creada',
                    "El Dr. {$nombreMedico} tiene una cita con {$nombrePaciente} el {$fechaFormateada}", 'info', $urlCita);
            }
        }

        CorreoHelper::citaCreada($cita);
        return response()->json(['success' => true]);
    }

    public function cancelarPaciente($id)
    {
        if (session('cargo') !== 'Paciente') {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $cita = Cita::findOrFail($id);

        if ($cita->paciente_id !== session('user_id')) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        if (!in_array($cita->estado, ['Pendiente', 'Programada'])) {
            return response()->json(['message' => 'Solo puedes cancelar citas pendientes o programadas'], 422);
        }

        $cita->update(['estado' => 'Cancelada']);
        CorreoHelper::citaCancelada($cita);

        return response()->json(['ok' => true]);
    }

    private function validarHorarioMedico($medico, Carbon $fechaHora)
    {
        $horario = $medico->horario;
        if (!$horario) {
            return 'El médico no tiene horario asignado';
        }

        $diasMap = [
            1 => 'lunes', 2 => 'martes', 3 => 'miercoles',
            4 => 'jueves', 5 => 'viernes', 6 => 'sabado', 0 => 'domingo',
        ];

        $diaSemana      = $diasMap[$fechaHora->dayOfWeek];
        $diasPermitidos = $horario->dias_semana ?? [];

        if (!in_array($diaSemana, $diasPermitidos)) {
            $dias = implode(', ', $diasPermitidos);
            return "El médico no atiende los días {$diaSemana}. Días disponibles: {$dias}";
        }

        $hora = $fechaHora->format('H:i');

        if ($hora < $horario->hora_inicio || $hora >= $horario->hora_fin) {
            return "La hora seleccionada está fuera del horario de atención ({$horario->hora_inicio} - {$horario->hora_fin})";
        }

        if ($horario->almuerzo_inicio && $hora >= $horario->almuerzo_inicio && $hora < $horario->almuerzo_fin) {
            return "El médico se encuentra en horario de almuerzo ({$horario->almuerzo_inicio} - {$horario->almuerzo_fin})";
        }

        return null;
    }

    public function horasDisponibles(Request $request)
    {
        $request->validate([
            'medico_id'     => 'required|exists:users,id',
            'prestacion_id' => 'required|exists:prestaciones,id',
            'fecha'         => 'required|date',
        ]);

        $medico  = User::with('horario')->findOrFail($request->medico_id);
        $horario = $medico->horario;

        if (!$horario) return response()->json([]);

        $diasMap = [
            1 => 'lunes', 2 => 'martes', 3 => 'miercoles',
            4 => 'jueves', 5 => 'viernes', 6 => 'sabado', 0 => 'domingo',
        ];
        $fecha     = Carbon::parse($request->fecha);
        $diaSemana = $diasMap[$fecha->dayOfWeek];

        if (!in_array($diaSemana, $horario->dias_semana ?? [])) return response()->json([]);

        $mp = MedicoPrestacion::where('id_medico', $request->medico_id)
            ->where('id_prestacion', $request->prestacion_id)
            ->first();

        if (!$mp) return response()->json([]);

        $slots   = $mp->slotsDisponibles();
        $cantMax = (int) $mp->cant_online;

        $tomadas = Cita::where('medico_id', $request->medico_id)
            ->where('prestacion_id', $request->prestacion_id)
            ->whereDate('Fecha_y_hora', $request->fecha)
            ->pluck('Fecha_y_hora')
            ->map(fn($f) => Carbon::parse($f)->format('H:i'))
            ->toArray();

        $contadorPorSlot = array_count_values($tomadas);

        $disponibles = array_filter($slots, function ($slot) use ($contadorPorSlot, $cantMax) {
            return ($contadorPorSlot[$slot] ?? 0) < $cantMax;
        });

        return response()->json(array_values($disponibles));
    }
}