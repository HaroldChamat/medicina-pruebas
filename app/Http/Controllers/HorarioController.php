<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Horario;

class HorarioController extends Controller
{
    public function index(Request $request)
    {
        $cargo  = session('cargo');
        $userId = session('user_id');

        // Calcular semana según parámetro ?semana=YYYY-MM-DD
        $semanaParam  = $request->query('semana');
        $inicioSemana = $semanaParam
            ? \Carbon\Carbon::parse($semanaParam)->startOfWeek(\Carbon\Carbon::MONDAY)
            : \Carbon\Carbon::now()->startOfWeek(\Carbon\Carbon::MONDAY);
        $finSemana = $inicioSemana->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);

        if ($cargo === 'Medico') {
            $medico  = User::with(['horario', 'especialidades'])->find($userId);
            $medicos = collect([$medico]);
        } else {
            $medicos = User::with(['horario', 'especialidades'])
                ->whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Medico'))
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
        // Solo Admin puede crear horarios
        if (session('admin') !== 1) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $request->validate([
            'medico_id'      => 'required|exists:users,id',
            'hora_inicio'    => 'required',
            'hora_fin'       => 'required',
            'almuerzo_inicio'=> 'nullable',
            'almuerzo_fin'   => 'nullable',
            'hora_atencion'  => 'required|integer|min:10|max:120',
        ]);

        // Si ya tiene horario, actualizar en lugar de crear
        $horarioExistente = Horario::where('medico_id', $request->medico_id)->first();

        if ($horarioExistente) {
            $horarioExistente->update([
                'hora_inicio'     => $request->hora_inicio,
                'hora_fin'        => $request->hora_fin,
                'almuerzo_inicio' => $request->almuerzo_inicio,
                'almuerzo_fin'    => $request->almuerzo_fin,
                'hora_atencion'   => $request->hora_atencion,
                'dias_semana'     => $request->dias_semana ?? ['lunes','martes','miercoles','jueves','viernes'],
            ]);
        } else {
            Horario::create(array_merge($request->all(), [
                'dias_semana' => $request->dias_semana ?? ['lunes','martes','miercoles','jueves','viernes'],
            ]));
        }

        return response()->json(['ok' => true]);
    }

    public function update(Request $request, Horario $horario)
    {
        // Solo Admin puede editar horarios
        if (session('admin') !== 1) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Si por alguna razón el horario no coincide, buscar por medico_id
        if ($request->has('medico_id')) {
            $horario = Horario::where('medico_id', $request->medico_id)
                ->orderBy('id', 'desc')
                ->firstOrFail();
        }
        
        $request->validate([
            'hora_inicio'    => 'required',
            'hora_fin'       => 'required',
            'almuerzo_inicio'=> 'nullable',
            'almuerzo_fin'   => 'nullable',
            'hora_atencion'  => 'required|integer|min:10|max:120',
        ]);

        $horario->update([
            'hora_inicio'    => $request->hora_inicio,
            'hora_fin'       => $request->hora_fin,
            'almuerzo_inicio'=> $request->almuerzo_inicio,
            'almuerzo_fin'   => $request->almuerzo_fin,
            'hora_atencion'  => $request->hora_atencion,
            'dias_semana'    => $request->dias_semana ?? ['lunes','martes','miercoles','jueves','viernes'],
        ]);

        return response()->json(['ok' => true]);
    }
}