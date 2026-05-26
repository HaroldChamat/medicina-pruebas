<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\CentroMedico;
use App\Models\Cita;

class SuperadminUsuarioController extends Controller
{
    /**
     * Lista todos los médicos del sistema (filtrable por centro).
     */
    public function medicos(Request $request)
    {
        $filtroCentro = $request->query('centro_id', '');
        $filtroEstado = $request->query('activo', '');

        $query = User::whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Medico'))
            ->with(['cargo', 'especialidades', 'centroMedico', 'horario', 'medicoPrestaciones.prestacion']);

        if ($filtroCentro) {
            $query->where('centro_medico_id', $filtroCentro);
        }

        if ($filtroEstado !== '') {
            $query->where('activo', $filtroEstado);
        }

        $medicos = $query->get();
        $centros = CentroMedico::orderBy('nombre')->get();

        return view('superadmin.usuarios.medicos', compact('medicos', 'centros', 'filtroCentro', 'filtroEstado'));
    }

    /**
     * Lista todos los pacientes del sistema (filtrable por centro).
     */
    public function pacientes(Request $request)
    {
        $filtroCentro = $request->query('centro_id', '');

        $query = User::whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Paciente'))
            ->with(['cargo', 'centroMedico']);

        if ($filtroCentro) {
            $query->where('centro_medico_id', $filtroCentro);
        }

        $pacientes = $query->get();
        $centros   = CentroMedico::orderBy('nombre')->get();

        return view('superadmin.usuarios.pacientes', compact('pacientes', 'centros', 'filtroCentro'));
    }

    /**
     * Lista todas las citas (filtrable por centro).
     */
    public function citas(Request $request)
    {
        $filtroCentro = $request->query('centro_id', '');
        $filtroEstado = $request->query('estado', '');

        $query = Cita::with(['medico.centroMedico', 'paciente', 'prestacion']);

        if ($filtroCentro) {
            $medicoIds = User::where('centro_medico_id', $filtroCentro)->pluck('id');
            $query->whereIn('medico_id', $medicoIds);
        }

        if ($filtroEstado) {
            $query->where('estado', $filtroEstado);
        }

        $citas   = $query->orderByDesc('Fecha_y_hora')->paginate(25);
        $centros = CentroMedico::orderBy('nombre')->get();

        return view('superadmin.usuarios.citas', compact('citas', 'centros', 'filtroCentro', 'filtroEstado'));
    }
}