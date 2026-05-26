<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prestacion;
use App\Models\MedicoPrestacion;
use App\Models\User;

class PrestacionController extends Controller
{
    // ── Listar todas las prestaciones (Admin) ─────────────────────────────
    public function index()
    {
        if (session('admin') !== 1) abort(403);

        $centroId = session('centro_medico_id');

        $prestaciones = Prestacion::all();

        $medicos = User::whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Medico'))
            ->where('activo', 1)
            ->when($centroId, fn($q) => $q->where('centro_medico_id', $centroId))
            ->with(['medicoPrestaciones.prestacion'])
            ->get();

        return view('admin.prestaciones', compact('prestaciones', 'medicos'));
    }

    // ── Crear prestación ──────────────────────────────────────────────────
    public function store(Request $request)
    {
        if (session('admin') !== 1) abort(403);

        $request->validate([
            'nombre' => 'required|string|max:150|unique:prestaciones,nombre',
        ]);

        $prestacion = Prestacion::create([
            'nombre' => ucwords(strtolower(trim($request->nombre))),
        ]);

        return response()->json([
            'ok'     => true,
            'id'     => $prestacion->id,
            'nombre' => $prestacion->nombre,
        ]);
    }

    // ── Actualizar prestación ─────────────────────────────────────────────
    public function update(Request $request, $id)
    {
        if (session('admin') !== 1) abort(403);

        $request->validate([
            'nombre' => 'required|string|max:150|unique:prestaciones,nombre,' . $id,
        ]);

        $prestacion = Prestacion::findOrFail($id);
        $prestacion->update([
            'nombre' => ucwords(strtolower(trim($request->nombre))),
        ]);

        return response()->json(['ok' => true, 'nombre' => $prestacion->nombre]);
    }

    // ── Eliminar prestación ───────────────────────────────────────────────
    public function destroy($id)
    {
        if (session('admin') !== 1) abort(403);

        $prestacion = Prestacion::findOrFail($id);

        // Desasociar de médicos
        MedicoPrestacion::where('id_prestacion', $id)->delete();

        $prestacion->delete();

        return response()->json(['ok' => true]);
    }

    // ── Asignar / actualizar prestaciones de un médico ────────────────────
    public function asignarMedico(Request $request)
    {
        if (session('admin') !== 1) abort(403);

        $request->validate([
            'medico_id'                    => 'required|exists:users,id',
            'prestaciones'                 => 'required|array|min:1',
            'prestaciones.*.id_prestacion' => 'required|exists:prestaciones,id',
            'prestaciones.*.cantidad_horas'=> 'required|integer|min:1|max:24',
            'prestaciones.*.hora_atencion' => 'required|integer|min:5|max:120',
            'prestaciones.*.hora_entrada'  => 'required|date_format:H:i',
            'prestaciones.*.hora_salida'   => 'required|date_format:H:i',
            'prestaciones.*.cant_online'   => 'required|integer|min:0',
        ]);

        $centroId = session('centro_medico_id');

        $medico = User::findOrFail($request->medico_id);

        // Seguridad: médico debe pertenecer al mismo centro
        if ($centroId && $medico->centro_medico_id != $centroId) {
            return response()->json([
                'message' => 'No autorizado. El médico no pertenece a tu centro.'
            ], 403);
        }

        MedicoPrestacion::where('id_medico', $medico->id)->delete();

        foreach ($request->prestaciones as $p) {
            MedicoPrestacion::create([
                'id_medico'      => $medico->id,
                'id_prestacion'  => $p['id_prestacion'],
                'cantidad_horas' => $p['cantidad_horas'],
                'hora_atencion'  => $p['hora_atencion'],
                'hora_entrada'   => $p['hora_entrada'],
                'hora_salida'    => $p['hora_salida'],
                'cant_online'    => $p['cant_online'],
            ]);
        }

        return response()->json(['ok' => true]);
    }

    // ── Eliminar una prestación específica de un médico ───────────────────
    public function eliminarDeMedico($id)
    {
        if (session('admin') !== 1) abort(403);

        $mp = MedicoPrestacion::findOrFail($id);
        $mp->delete();

        return response()->json(['ok' => true]);
    }

    // ── Obtener slots disponibles para toma de hora online ────────────────
    // GET /horas-disponibles-prestacion?medico_id=X&prestacion_id=Y&fecha=YYYY-MM-DD
    public function horasDisponibles(Request $request)
    {
        $request->validate([
            'medico_id'     => 'required|exists:users,id',
            'prestacion_id' => 'required|exists:prestaciones,id',
            'fecha'         => 'required|date',
        ]);

        $medico = User::with('horario')->findOrFail($request->medico_id);
        $horario = $medico->horario;

        if (!$horario) {
            return response()->json([]);
        }

        // Validar que la fecha sea un día que atiende el médico
        $diasMap = [
            1 => 'lunes', 2 => 'martes', 3 => 'miercoles',
            4 => 'jueves', 5 => 'viernes', 6 => 'sabado', 0 => 'domingo',
        ];
        $fecha      = \Carbon\Carbon::parse($request->fecha);
        $diaSemana  = $diasMap[$fecha->dayOfWeek];
        $diasPermi  = $horario->dias_semana ?? [];

        if (!in_array($diaSemana, $diasPermi)) {
            return response()->json([]);
        }

        // Obtener la prestación del médico
        $mp = MedicoPrestacion::where('id_medico', $request->medico_id)
            ->where('id_prestacion', $request->prestacion_id)
            ->first();

        if (!$mp) {
            return response()->json([]);
        }

        // Citas ya tomadas en esa fecha/prestación
        $citasTomadas = \App\Models\Cita::where('medico_id', $request->medico_id)
            ->where('prestacion_id', $request->prestacion_id)
            ->whereDate('Fecha_y_hora', $request->fecha)
            ->pluck('Fecha_y_hora')
            ->map(fn($f) => \Carbon\Carbon::parse($f)->format('H:i'))
            ->toArray();

        // Contar cuántas citas hay por slot (para cant_online)
        $contadorPorSlot = array_count_values($citasTomadas);

        $slots    = $mp->slotsDisponibles();
        $cantMax  = (int) $mp->cant_online;

        $disponibles = array_filter($slots, function ($slot) use ($contadorPorSlot, $cantMax) {
            $tomadas = $contadorPorSlot[$slot] ?? 0;
            return $tomadas < $cantMax;
        });

        return response()->json(array_values($disponibles));
    }
}