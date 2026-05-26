<?php
// ════════════════════════════════════════════════════════════════════════════
// HorarioController.php  — con filtro por centro_medico_id
// ════════════════════════════════════════════════════════════════════════════

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Horario;

class HorarioController extends Controller
{
    public function index(Request $request)
    {
        $cargo    = session('cargo');
        $userId   = session('user_id');
        $centroId = session('centro_medico_id');

        $semanaParam  = $request->query('semana');
        $inicioSemana = $semanaParam
            ? \Carbon\Carbon::parse($semanaParam)->startOfWeek(\Carbon\Carbon::MONDAY)
            : \Carbon\Carbon::now()->startOfWeek(\Carbon\Carbon::MONDAY);
        $finSemana = $inicioSemana->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);

        if ($cargo === 'Medico') {
            $medico  = User::with(['horario', 'especialidades', 'medicoPrestaciones.prestacion'])->find($userId);
            $medicos = collect([$medico]);
        } else {
            // Admin: solo médicos ACTIVOS de su propio centro
            $medicos = User::with(['horario', 'especialidades', 'medicoPrestaciones.prestacion'])
                ->whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Medico'))
                ->where('activo', 1)
                ->when($centroId, fn($q) => $q->where('centro_medico_id', $centroId))
                ->get();
        }

        return view('Horario', [
            'medicos'      => $medicos,
            'esAdmin'      => session('admin') === 1,
            'inicioSemana' => $inicioSemana,
            'finSemana'    => $finSemana,
        ]);
    }

    public function store(Request $request)
    {
        if (session('admin') !== 1) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $request->validate([
            'medico_id'      => 'required|exists:users,id',
            'hora_inicio'    => 'required',
            'hora_fin'       => 'required',
            'almuerzo_inicio'=> 'nullable',
            'almuerzo_fin'   => 'nullable',
        ]);

        // Verificar que el médico pertenece al centro del admin
        $this->verificarMedicoDelCentro($request->medico_id);

        $diasPermitidos = ['lunes','martes','miercoles','jueves','viernes','sabado','domingo'];
        $diasSemana = array_values(array_unique(
            array_filter(
                (array) ($request->dias_semana ?? ['lunes','martes','miercoles','jueves','viernes']),
                fn($d) => in_array($d, $diasPermitidos)
            )
        ));

        $horarioExistente = Horario::where('medico_id', $request->medico_id)->first();

        if ($horarioExistente) {
            $horarioExistente->update([
                'hora_inicio'     => $request->hora_inicio,
                'hora_fin'        => $request->hora_fin,
                'almuerzo_inicio' => $request->almuerzo_inicio,
                'almuerzo_fin'    => $request->almuerzo_fin,
                'dias_semana'     => $diasSemana,
            ]);
        } else {
            Horario::create([
                'medico_id'       => $request->medico_id,
                'hora_inicio'     => $request->hora_inicio,
                'hora_fin'        => $request->hora_fin,
                'almuerzo_inicio' => $request->almuerzo_inicio,
                'almuerzo_fin'    => $request->almuerzo_fin,
                'dias_semana'     => $diasSemana,
            ]);
        }

        return response()->json(['ok' => true]);
    }

    public function update(Request $request, Horario $horario)
    {
        if (session('admin') !== 1) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        if ($request->has('medico_id')) {
            $this->verificarMedicoDelCentro($request->medico_id);
            $horario = Horario::where('medico_id', $request->medico_id)->orderBy('id', 'desc')->firstOrFail();
        }

        $request->validate([
            'hora_inicio'    => 'required',
            'hora_fin'       => 'required',
            'almuerzo_inicio'=> 'nullable',
            'almuerzo_fin'   => 'nullable',
        ]);

        $diasPermitidos = ['lunes','martes','miercoles','jueves','viernes','sabado','domingo'];
        $diasSemana = array_values(array_unique(
            array_filter(
                (array) ($request->dias_semana ?? ['lunes','martes','miercoles','jueves','viernes']),
                fn($d) => in_array($d, $diasPermitidos)
            )
        ));

        $horario->update([
            'hora_inicio'    => $request->hora_inicio,
            'hora_fin'       => $request->hora_fin,
            'almuerzo_inicio'=> $request->almuerzo_inicio,
            'almuerzo_fin'   => $request->almuerzo_fin,
            'dias_semana'    => $diasSemana,
        ]);

        return response()->json(['ok' => true]);
    }

    private function verificarMedicoDelCentro(int $medicoId): void
    {
        $centroId = session('centro_medico_id');
        if (!$centroId) return; // Si no hay centro en sesión, no restringir

        $medico = User::findOrFail($medicoId);
        if ($medico->centro_medico_id !== $centroId) {
            abort(403, 'El médico no pertenece a tu centro médico.');
        }
    }
}