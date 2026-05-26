<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\CentroMedico;
use App\Models\User;
use App\Models\Cita;
use App\Models\Ticket;

class SuperadminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'centros'   => CentroMedico::count(),
            'admins'    => User::where('admin', 1)->count(),
            'medicos'   => User::whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Medico'))->count(),
            'pacientes' => User::whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Paciente'))->count(),
            'citas'     => Cita::count(),
            'tickets'   => Ticket::count(),
        ];

        $centros = CentroMedico::withCount([
            'users as total_usuarios',
            'users as total_medicos' => fn($q) =>
                $q->whereHas('cargo', fn($q2) => $q2->where('Nombre_cargo', 'Medico')),
            'users as total_pacientes' => fn($q) =>
                $q->whereHas('cargo', fn($q2) => $q2->where('Nombre_cargo', 'Paciente')),
        ])->latest()->get();

        return view('superadmin.dashboard', compact('stats', 'centros'));
    }
}