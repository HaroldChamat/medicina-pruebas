<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CentroMedico;
use App\Models\User;
use App\Models\Cita;
use App\Models\Mensaje;
use App\Models\Notificacion;
use App\Models\Ticket;
use App\Models\TicketMensaje;
use App\Models\TicketArchivo;
use Illuminate\Support\Facades\DB;

class SuperadminCentroController extends Controller
{
    public function index()
    {
        $centros = CentroMedico::withCount([
            'users as total_usuarios',
            'users as total_admins'    => fn($q) => $q->where('admin', 1),
            'users as total_medicos'   => fn($q) =>
                $q->whereHas('cargo', fn($q2) => $q2->where('Nombre_cargo', 'Medico')),
            'users as total_pacientes' => fn($q) =>
                $q->whereHas('cargo', fn($q2) => $q2->where('Nombre_cargo', 'Paciente')),
        ])->latest()->get();

        return view('superadmin.centros.index', compact('centros'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'    => 'required|string|max:200',
            'direccion' => 'required|string|max:300',
        ]);

        $centro = CentroMedico::create([
            'nombre'                   => trim($request->nombre),
            'direccion'                => trim($request->direccion),
            'creado_por_superadmin_id' => session('superadmin_id'),
        ]);

        return response()->json([
            'ok'       => true,
            'id'       => $centro->id,
            'nombre'   => $centro->nombre,
            'direccion'=> $centro->direccion,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre'    => 'required|string|max:200',
            'direccion' => 'required|string|max:300',
        ]);

        $centro = CentroMedico::findOrFail($id);
        $centro->update([
            'nombre'    => trim($request->nombre),
            'direccion' => trim($request->direccion),
        ]);

        return response()->json(['ok' => true]);
    }

    /**
     * Previsualización de lo que se eliminará — sin eliminar nada.
     */
    public function previewDestroy($id)
    {
        $centro = CentroMedico::withCount([
            'users as total_admins'    => fn($q) => $q->where('admin', 1),
            'users as total_medicos'   => fn($q) =>
                $q->whereHas('cargo', fn($q2) => $q2->where('Nombre_cargo', 'Medico')),
            'users as total_pacientes' => fn($q) =>
                $q->whereHas('cargo', fn($q2) => $q2->where('Nombre_cargo', 'Paciente')),
        ])->findOrFail($id);

        $userIds = User::where('centro_medico_id', $id)->pluck('id');
        $citaIds = Cita::whereIn('medico_id', $userIds)
            ->orWhereIn('paciente_id', $userIds)
            ->pluck('id');

        return response()->json([
            'centro'          => $centro->nombre,
            'admins'          => $centro->total_admins,
            'medicos'         => $centro->total_medicos,
            'pacientes'       => $centro->total_pacientes,
            'citas'           => $citaIds->count(),
            'tickets'         => Ticket::whereIn('medico_id', $userIds)->count(),
            'informes'        => Cita::whereIn('id', $citaIds)->has('enfermedad')->count(),
        ]);
    }

    /**
     * Eliminación definitiva con doble confirmación (flag confirm=true requerido).
     */
    public function destroy(Request $request, $id)
    {
        if ($request->input('confirm') !== 'CONFIRMAR') {
            return response()->json(['error' => 'Confirmación inválida'], 422);
        }

        $centro = CentroMedico::findOrFail($id);

        DB::transaction(function () use ($centro, $id) {
            $userIds = User::where('centro_medico_id', $id)->pluck('id');

            // 1. Citas asociadas a usuarios del centro
            $citaIds = Cita::whereIn('medico_id', $userIds)
                ->orWhereIn('paciente_id', $userIds)
                ->pluck('id');

            // 2. Mensajes de esas citas
            Mensaje::whereIn('cita_id', $citaIds)->delete();

            // 3. Tickets de los médicos del centro
            $ticketIds = Ticket::whereIn('medico_id', $userIds)->pluck('id');
            TicketMensaje::whereIn('ticket_id', $ticketIds)->delete();
            TicketArchivo::whereIn('ticket_id', $ticketIds)->delete();
            Ticket::whereIn('id', $ticketIds)->delete();

            // 4. Notificaciones de los usuarios
            Notificacion::whereIn('user_id', $userIds)->delete();

            // 5. Citas
            Cita::whereIn('id', $citaIds)->delete();

            // 6. Usuarios del centro
            User::where('centro_medico_id', $id)->delete();

            // 7. Centro médico
            $centro->delete();
        });

        return response()->json(['ok' => true]);
    }

    /**
     * Vista detallada de un centro: lista todos sus usuarios, citas, etc.
     */
    public function show(Request $request, $id)
    {
        $centro = CentroMedico::findOrFail($id);

        $filtroCargo  = $request->query('cargo', '');
        $filtroEstado = $request->query('estado', '');

        $admins = User::where('centro_medico_id', $id)
            ->where('admin', 1)
            ->with('cargo')
            ->get();

        $medicos = User::where('centro_medico_id', $id)
            ->whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Medico'))
            ->with(['cargo', 'especialidades', 'horario'])
            ->get();

        $pacientes = User::where('centro_medico_id', $id)
            ->whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Paciente'))
            ->with('cargo')
            ->get();

        $citasQuery = Cita::with(['medico', 'paciente', 'prestacion'])
            ->where(function ($q) use ($id) {
                $userIds = User::where('centro_medico_id', $id)->pluck('id');
                $q->whereIn('medico_id', $userIds);
            });

        if ($filtroEstado) {
            $citasQuery->where('estado', $filtroEstado);
        }

        $citas = $citasQuery->orderByDesc('Fecha_y_hora')->paginate(20);

        return view('superadmin.centros.show', compact(
            'centro', 'admins', 'medicos', 'pacientes', 'citas',
            'filtroCargo', 'filtroEstado'
        ));
    }
}