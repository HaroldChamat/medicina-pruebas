<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Horario;

class HorarioController extends Controller
{
    public function index()
    {
        $cargo  = session('cargo');
        $userId = session('user_id');

        // Médico: solo ve su propio horario
        if ($cargo === 'Medico') {
            $medico  = User::with('horario')->find($userId);
            $medicos = collect([$medico]); // colección de un solo elemento
        } else {
            // Admin: ve todos los médicos y sus horarios
            $medicos = User::with('horario')
                ->whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Medico'))
                ->get();
        }

        return view('Horario', [
            'medicos' => $medicos,
            'esAdmin' => session('admin') === 1,
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

        if (Horario::where('medico_id', $request->medico_id)->exists()) {
            return response()->json([
                'message' => 'Este médico ya tiene un horario definido'
            ], 422);
        }

        Horario::create($request->all());

        return response()->json(['ok' => true]);
    }

    public function update(Request $request, Horario $horario)
    {
        // Solo Admin puede editar horarios
        if (session('admin') !== 1) {
            return response()->json(['message' => 'No autorizado'], 403);
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
        ]);

        return response()->json(['ok' => true]);
    }
}