<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SolicitudCambioCentro;
use App\Models\SolicitudArchivo;
use App\Models\CentroMedico;
use App\Models\User;
use App\Models\Cita;

class SolicitudCambioCentroController extends Controller
{
    /**
     * Lista de solicitudes del usuario autenticado.
     */
    public function index()
    {
        $userId = session('user_id');
        $cargo  = session('cargo');

        if ($cargo === 'Paciente') {
            $solicitudes = SolicitudCambioCentro::with(['centroActual', 'centroSolicitado'])
                ->where('paciente_id', $userId)
                ->orderByDesc('created_at')
                ->paginate(10);
        } else {
            $solicitudes = SolicitudCambioCentro::with(['paciente', 'centroActual', 'centroSolicitado'])
                ->where('solicitante_id', $userId)
                ->orderByDesc('created_at')
                ->paginate(10);
        }

        return view('solicitudes.index', compact('solicitudes'));
    }

    /**
     * Detalle de una solicitud del usuario.
     */
    public function show(SolicitudCambioCentro $solicitud)
    {
        $userId = session('user_id');
        $cargo  = session('cargo');

        // Paciente solo ve sus propias solicitudes; médico/admin las que crearon
        $authorized = ($cargo === 'Paciente')
            ? $solicitud->paciente_id == $userId
            : $solicitud->solicitante_id == $userId;

        if (! $authorized) {
            abort(403, 'No tienes permiso para ver esta solicitud.');
        }

        $solicitud->load(['solicitante', 'paciente', 'centroActual', 'centroSolicitado', 'archivos.emisor']);

        return view('solicitudes.show', compact('solicitud'));
    }

    /**
     * Formulario para crear una nueva solicitud.
     */
    public function create()
    {
        $cargo    = session('cargo');
        $userId   = session('user_id');
        $centroId = session('centro_medico_id');

        $centros   = CentroMedico::orderBy('nombre')->get();
        $pacientes = collect();

        if ($cargo === 'Medico') {
            $pacienteIds = Cita::where('medico_id', $userId)
                ->pluck('paciente_id')
                ->unique();

            $pacientes = User::whereIn('id', $pacienteIds)
                ->whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Paciente'))
                ->orderBy('name')
                ->get();

        } elseif (session('admin') === 1) {
            $pacientes = User::whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Paciente'))
                ->when($centroId, fn($q) => $q->where('centro_medico_id', $centroId))
                ->orderBy('name')
                ->get();
        }

        return view('solicitudes.crear', compact('centros', 'pacientes'));
    }

    /**
     * Guarda la nueva solicitud.
     */
    public function store(Request $request)
    {
        $cargo  = session('cargo');
        $userId = session('user_id');

        $rules = [
            'asunto'               => 'required|string|max:200',
            'motivo'               => 'required|string|max:3000',
            'centro_solicitado_id' => 'required|exists:centro_medico,id',
            'archivos.*'           => 'nullable|file|max:5120',
        ];

        if ($cargo !== 'Paciente') {
            $rules['paciente_id'] = 'required|exists:users,id';
            $rules['prioridad']   = 'required|in:alta,media,baja';
        }

        $request->validate($rules, [
            'asunto.required'               => 'El asunto es obligatorio.',
            'motivo.required'               => 'El motivo es obligatorio.',
            'centro_solicitado_id.required' => 'Debes seleccionar el centro de destino.',
            'paciente_id.required'          => 'Debes seleccionar un paciente.',
            'prioridad.required'            => 'La prioridad es obligatoria.',
        ]);

        $pacienteId = ($cargo === 'Paciente') ? $userId : (int) $request->paciente_id;
        $paciente   = User::findOrFail($pacienteId);

        if ($paciente->centro_medico_id == $request->centro_solicitado_id) {
            return response()->json([
                'message' => 'El paciente ya pertenece al centro médico seleccionado.',
            ], 422);
        }

        $solicitud = SolicitudCambioCentro::create([
            'solicitante_id'       => $userId,
            'paciente_id'          => $pacienteId,
            'centro_actual_id'     => $paciente->centro_medico_id,
            'centro_solicitado_id' => $request->centro_solicitado_id,
            'asunto'               => trim($request->asunto),
            'motivo'               => trim($request->motivo),
            'prioridad'            => $cargo !== 'Paciente' ? $request->prioridad : null,
            'estado'               => 'pendiente',
        ]);

        if ($request->hasFile('archivos')) {
            foreach ($request->file('archivos') as $archivo) {
                $ruta = $archivo->store('solicitud_archivos', 'public');

                SolicitudArchivo::create([
                    'solicitud_id'    => $solicitud->id,
                    'emisor_id'       => $userId,
                    'nombre_original' => $archivo->getClientOriginalName(),
                    'ruta'            => $ruta,
                    'mime_type'       => $archivo->getMimeType(),
                ]);
            }
        }

        return response()->json([
            'ok' => true,
            'id' => $solicitud->id,
        ]);
    }
}