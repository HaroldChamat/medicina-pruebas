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
 
        $semanaParam  = $request->query('semana');
        $inicioSemana = $semanaParam
            ? \Carbon\Carbon::parse($semanaParam)->startOfWeek(\Carbon\Carbon::MONDAY)
            : \Carbon\Carbon::now()->startOfWeek(\Carbon\Carbon::MONDAY);
        $finSemana = $inicioSemana->copy()->endOfWeek(\Carbon\Carbon::SUNDAY);
 
        if ($cargo === 'Medico') {
            $medico  = User::with(['horario', 'especialidades'])->find($userId);
            $medicos = collect([$medico]);
        } else {
            // Admin: solo médicos ACTIVOS para gestionar horarios
            $medicos = User::with(['horario', 'especialidades'])
                ->whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Medico'))
                ->where('activo', 1)
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

        // Sanitizar días: asegurar que sean únicos y válidos
        $diasPermitidos = ['lunes','martes','miercoles','jueves','viernes','sabado','domingo'];
        $diasSemana = array_values(array_unique(
            array_filter(
                (array) ($request->dias_semana ?? ['lunes','martes','miercoles','jueves','viernes']),
                fn($d) => in_array($d, $diasPermitidos)
            )
        ));

        // Si ya tiene horario, actualizar en lugar de crear
        $horarioExistente = Horario::where('medico_id', $request->medico_id)->first();

        if ($horarioExistente) {
            $horarioExistente->update([
                'hora_inicio'     => $request->hora_inicio,
                'hora_fin'        => $request->hora_fin,
                'almuerzo_inicio' => $request->almuerzo_inicio,
                'almuerzo_fin'    => $request->almuerzo_fin,
                'hora_atencion'   => $request->hora_atencion,
                'dias_semana'     => $diasSemana,
            ]);
        } else {
            Horario::create([
                'medico_id'       => $request->medico_id,
                'hora_inicio'     => $request->hora_inicio,
                'hora_fin'        => $request->hora_fin,
                'almuerzo_inicio' => $request->almuerzo_inicio,
                'almuerzo_fin'    => $request->almuerzo_fin,
                'hora_atencion'   => $request->hora_atencion,
                'dias_semana'     => $diasSemana,
            ]);
        }

        return response()->json(['ok' => true]);
    }

    public function update(Request $request, Horario $horario)
    {
        // Solo Admin puede editar horarios
        if (session('admin') !== 1) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Si se envía medico_id, buscar el horario correcto por médico
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

        // Sanitizar días: asegurar que sean únicos y válidos
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
            'hora_atencion'  => $request->hora_atencion,
            'dias_semana'    => $diasSemana,
        ]);

        return response()->json(['ok' => true]);
    }
}