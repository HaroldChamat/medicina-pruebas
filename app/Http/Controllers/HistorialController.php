<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\User;

class HistorialController extends Controller
{
    public function index(User $paciente)
    {
        $historial = Cita::with([
                'medico',
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
