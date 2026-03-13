<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\User;

class HistorialController extends Controller
{
    public function index(User $paciente)
    {
        // Seguridad: paciente solo puede ver su propio historial
        if (session('cargo') === 'Paciente' && session('user_id') != $paciente->id) {
            abort(403, 'No autorizado');
        }

            $historial = Cita::with([
                'medico.especialidades',
                'medico.cargo',
                'enfermedad',
                'tratamiento'
            ])
            ->where('paciente_id', $paciente->id)
            ->where('estado', 'Finalizada')
            ->orderByDesc('Fecha_y_hora')
            ->get();

        return view('Historial', compact('paciente', 'historial'));
    }
}
