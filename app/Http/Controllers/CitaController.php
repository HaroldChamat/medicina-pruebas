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
    public function index()
    {
        $userId  = session('user_id');
        $cargo   = session('cargo');
        $perPage = 10;

        if ($cargo === 'Admin') {
            $Citas = Cita::with(['medico', 'paciente', 'enfermedad', 'tratamiento', 'prestacion'])
                ->orderBy('Fecha_y_hora', 'asc')
                ->paginate($perPage);
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

        // Solo médicos ACTIVOS para el select de nueva cita
        $medicos = User::whereHas('cargo', fn($q) =>
            $q->where('Nombre_cargo', 'Medico')
        )->where('activo', 1)->with('medicoPrestaciones.prestacion')->get();

        $pacientes = User::whereHas('cargo', fn($q) =>
            $q->where('Nombre_cargo', 'Paciente')
        )->get();

        $todosMedicos = User::whereHas('cargo', fn($q) =>
            $q->where('Nombre_cargo', 'Medico')
        )->get();

        return view('citas', compact('Citas', 'medicos', 'pacientes', 'todosMedicos'));
    }

    public function edit($id)
    {
        $cita = Cita::with(['medico', 'paciente', 'prestacion'])->find($id);
        if (!$cita) {
            return response()->json(['error' => 'Cita no encontrada'], 404);
        }
        return response()->json($cita);
    }

    public function destroy($id)
    {
        $cargo = session('cargo');

        // Solo Admin puede eliminar; paciente solo puede cancelar las suyas
        if (session('admin') !== 1 && $cargo !== 'Paciente') {
            abort(403, 'No autorizado');
        }

        $cita = Cita::find($id);
        if (!$cita) {
            return redirect()->route('citas')->with('error', 'Cita no encontrada.');
        }

        // Paciente solo puede eliminar sus propias citas
        if ($cargo === 'Paciente' && $cita->paciente_id !== session('user_id')) {
            abort(403, 'No autorizado');
        }

        $cita->delete();
        return redirect()->route('citas')->with('success', 'Cita eliminada/cancelada correctamente.');
    }

    public function update(Request $request, $id)
    {
        // Solo Admin puede editar estado/fecha
        if (session('admin') !== 1) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $request->validate([
            'Fecha_y_hora' => 'required',
            'estado'       => 'required|in:Pendiente,Programada,Finalizada,Cancelada',
        ]);

        $cita      = Cita::findOrFail($id);
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
            NotificacionHelper::enviar(
                $cita, $cita->medico_id,
                'Cita programada',
                "Tu cita del {$fechaFormateada} ha sido programada exitosamente",
                'success', $urlCita
            );
            NotificacionHelper::enviar(
                $cita, $cita->paciente_id,
                'Cita programada',
                "Tu cita con el Dr. {$nombreMedico} del {$fechaFormateada} fue programada exitosamente",
                'success', $urlCita
            );
            CorreoHelper::citaProgramada($cita);
        }

        if ($request->estado === 'Cancelada' && $estadoAnterior !== 'Cancelada') {
            CorreoHelper::citaCancelada($cita);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Crear cita — SOLO Admin o el propio Paciente.
     */
    public function store(Request $request)
    {
        $cargo  = session('cargo');
        $userId = session('user_id');

        // Médicos no pueden crear citas
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

        // Paciente solo puede crear citas para sí mismo
        if ($cargo === 'Paciente' && (int) $request->paciente_id !== (int) $userId) {
            return response()->json(['message' => 'Solo puedes crear citas para ti mismo'], 403);
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

        // Validar disponibilidad según prestación y cant_online
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

        // Verificar cupo online
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

        NotificacionHelper::enviar(
            $cita, $cita->medico_id,
            'Nueva cita asignada',
            "Se agendó una cita con {$nombrePaciente} el {$fechaFormateada}",
            'info', $urlCita
        );

        foreach (NotificacionHelper::getAdmins() as $admin) {
            NotificacionHelper::enviar(
                $cita, $admin->id,
                'Nueva cita creada',
                "El Dr. {$nombreMedico} tiene una cita con {$nombrePaciente} el {$fechaFormateada}",
                'info', $urlCita
            );
        }

        CorreoHelper::citaCreada($cita);
        return response()->json(['success' => true]);
    }

    // ── Cancelar cita — Solo el paciente dueño de la cita ────────────────
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

        if (
            $horario->almuerzo_inicio &&
            $hora >= $horario->almuerzo_inicio &&
            $hora < $horario->almuerzo_fin
        ) {
            return "El médico se encuentra en horario de almuerzo ({$horario->almuerzo_inicio} - {$horario->almuerzo_fin})";
        }

        return null;
    }

    /**
     * Horas disponibles para una prestación específica de un médico en una fecha.
     */
    public function horasDisponibles(Request $request)
    {
        $request->validate([
            'medico_id'     => 'required|exists:users,id',
            'prestacion_id' => 'required|exists:prestaciones,id',
            'fecha'         => 'required|date',
        ]);

        $medico  = User::with('horario')->findOrFail($request->medico_id);
        $horario = $medico->horario;

        if (!$horario) {
            return response()->json([]);
        }

        $diasMap = [
            1 => 'lunes', 2 => 'martes', 3 => 'miercoles',
            4 => 'jueves', 5 => 'viernes', 6 => 'sabado', 0 => 'domingo',
        ];
        $fecha     = Carbon::parse($request->fecha);
        $diaSemana = $diasMap[$fecha->dayOfWeek];

        if (!in_array($diaSemana, $horario->dias_semana ?? [])) {
            return response()->json([]);
        }

        $mp = MedicoPrestacion::where('id_medico', $request->medico_id)
            ->where('id_prestacion', $request->prestacion_id)
            ->first();

        if (!$mp) {
            return response()->json([]);
        }

        $slots   = $mp->slotsDisponibles();
        $cantMax = (int) $mp->cant_online;

        // Citas ya tomadas ese día para esa prestación
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