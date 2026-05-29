<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudCambioCentro;
use App\Models\CentroMedico;
use App\Models\User;
use App\Models\Superadmin;

class SuperadminSolicitudController extends Controller
{
    /**
     * Lista todas las solicitudes para el superadmin.
     */
    public function index(Request $request)
    {
        $query = SolicitudCambioCentro::with([
            'solicitante',
            'paciente',
            'centroActual',
            'centroSolicitado',
        ]);

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtro por prioridad
        if ($request->filled('prioridad')) {
            $query->where('prioridad', $request->prioridad);
        }

        // Búsqueda por nombre de paciente o asunto
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('asunto', 'like', "%{$buscar}%")
                  ->orWhereHas('paciente', fn($q2) => $q2->where('name', 'like', "%{$buscar}%"));
            });
        }

        $solicitudes = $query->orderByRaw("FIELD(estado, 'pendiente', 'aceptada', 'rechazada')")
                             ->orderByRaw("FIELD(prioridad, 'alta', 'media', 'baja') ASC")
                             ->orderByDesc('created_at')
                             ->paginate(15)
                             ->withQueryString();

        $totalPendientes = SolicitudCambioCentro::where('estado', 'pendiente')->count();

        return view('superadmin.solicitudes.index', compact('solicitudes', 'totalPendientes'));
    }

    /**
     * Detalle de una solicitud.
     */
    public function show(SolicitudCambioCentro $solicitud)
    {
        $solicitud->load([
            'solicitante',
            'paciente',
            'centroActual',
            'centroSolicitado',
            'archivos.emisor',
            'gestorSuperadmin',
        ]);

        $centros = CentroMedico::orderBy('nombre')->get();

        return view('superadmin.solicitudes.show', compact('solicitud', 'centros'));
    }

    /**
     * Gestionar (aceptar / rechazar) una solicitud.
     */
    public function gestionar(Request $request, SolicitudCambioCentro $solicitud)
    {
        $request->validate([
            'accion'          => 'required|in:aceptada,rechazada',
            'nota_superadmin' => 'nullable|string|max:2000',
        ], [
            'accion.required' => 'Debes seleccionar una acción.',
            'accion.in'       => 'Acción no válida.',
        ]);

        if ($solicitud->estado !== 'pendiente') {
            return response()->json([
                'message' => 'Esta solicitud ya fue gestionada.',
            ], 422);
        }

        $superadminId = session('superadmin_id');

        // Si se acepta, cambiar el centro médico del paciente
        if ($request->accion === 'aceptada') {
            $paciente = User::findOrFail($solicitud->paciente_id);
            $paciente->update(['centro_medico_id' => $solicitud->centro_solicitado_id]);
        }

        $solicitud->update([
            'estado'          => $request->accion,
            'nota_superadmin' => $request->nota_superadmin,
            'gestionado_por'  => $superadminId,
            'gestionado_en'   => now(),
        ]);

        return response()->json([
            'ok'     => true,
            'estado' => $solicitud->estado,
        ]);
    }

    /**
     * Cambio directo de centro médico desde la vista de pacientes (superadmin).
     */
    public function cambiarCentroDirecto(Request $request, User $paciente)
    {
        $request->validate([
            'centro_medico_id' => 'required|exists:centro_medico,id',
        ], [
            'centro_medico_id.required' => 'Debes seleccionar un centro médico.',
            'centro_medico_id.exists'   => 'El centro médico seleccionado no existe.',
        ]);

        if ($paciente->centro_medico_id == $request->centro_medico_id) {
            return response()->json([
                'message' => 'El paciente ya pertenece a ese centro médico.',
            ], 422);
        }

        $centroAnterior = $paciente->centro_medico_id;

        $paciente->update(['centro_medico_id' => $request->centro_medico_id]);

        return response()->json([
            'ok'               => true,
            'centro_anterior'  => $centroAnterior,
            'centro_nuevo'     => $request->centro_medico_id,
        ]);
    }
}