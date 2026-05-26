<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\TicketMensaje;
use App\Models\TicketArchivo;
use App\Models\Cita;
use App\Models\User;
use App\Events\NuevoTicketMensaje;
use App\Helpers\NotificacionHelper;
use Carbon\Carbon;
use App\Helpers\CorreoHelper;

class TicketController extends Controller
{
    // ── Lista de tickets ──────────────────────────────────────────────────
    public function index()
    {
        $userId   = session('user_id');
        $cargo    = session('cargo');
        $esAdmin  = session('admin') === 1;
        $centroId = session('centro_medico_id');

        if ($cargo === 'Medico') {

            $tickets = Ticket::with(['admin', 'cita'])
                ->where('medico_id', $userId)
                ->orderByRaw("FIELD(estado, 'en_progreso', 'abierto', 'cerrado')")
                ->orderByRaw("FIELD(prioridad, 'alta', 'media', 'baja')")
                ->get();

        } elseif ($esAdmin) {

            $tickets = Ticket::with(['medico', 'cita', 'admin'])
                ->whereHas('medico', function ($q) use ($centroId) {
                    if ($centroId) {
                        $q->where('centro_medico_id', $centroId);
                    }
                })
                ->orderByRaw("FIELD(estado, 'abierto', 'en_progreso', 'cerrado')")
                ->orderByRaw("FIELD(prioridad, 'alta', 'media', 'baja')")
                ->get();

        } else {
            abort(403);
        }

        // Citas del médico para crear ticket
        $citasMedico = collect();

        if ($cargo === 'Medico') {
            $citasMedico = Cita::with('paciente')
                ->where('medico_id', $userId)
                ->orderBy('Fecha_y_hora', 'desc')
                ->get();
        }

        return view('Tickets', compact('tickets', 'citasMedico'));
    }

    // ── Crear ticket ──────────────────────────────────────────────────────
    public function store(Request $request)
    {
        if (session('cargo') !== 'Medico') abort(403);

        $request->validate([
            'asunto'      => 'required|string|max:150',
            'descripcion' => 'required|string',
            'prioridad'   => 'required|in:alta,media,baja',
            'cita_id'     => 'nullable|exists:citas,id',
            'archivos.*'  => 'nullable|file|max:5120',
        ]);

        $ticket = Ticket::create([
            'medico_id'   => session('user_id'),
            'asunto'      => $request->asunto,
            'descripcion' => $request->descripcion,
            'prioridad'   => $request->prioridad,
            'cita_id'     => $request->cita_id ?: null,
            'estado'      => 'abierto',
        ]);

        // Subir archivos
        if ($request->hasFile('archivos')) {
            foreach ($request->file('archivos') as $archivo) {

                $ruta = $archivo->store('ticket_archivos', 'public');

                TicketArchivo::create([
                    'ticket_id'       => $ticket->id,
                    'emisor_id'       => session('user_id'),
                    'nombre_original' => $archivo->getClientOriginalName(),
                    'ruta'            => $ruta,
                    'mime_type'       => $archivo->getMimeType(),
                ]);
            }
        }

        // Notificar admins del mismo centro
        $ticket->load('medico');
        $citaDummy = new \App\Models\Cita();

        foreach (NotificacionHelper::getAdmins() as $admin) {

            if (
                $ticket->medico &&
                $admin->centro_medico_id != $ticket->medico->centro_medico_id
            ) {
                continue;
            }

            NotificacionHelper::enviar(
                $citaDummy,
                $admin->id,
                'Nuevo ticket',
                "Dr. {$ticket->medico->name} {$ticket->medico->Apellidos}: {$ticket->asunto}",
                $ticket->prioridad === 'alta' ? 'danger' : 'info',
                '/tickets/' . $ticket->id
            );
        }

        CorreoHelper::ticketNuevo($ticket);

        return response()->json([
            'ok' => true,
            'ticket_id' => $ticket->id
        ]);
    }

    // ── Ver ticket ────────────────────────────────────────────────────────
    public function show(Ticket $ticket)
    {
        $userId   = session('user_id');
        $cargo    = session('cargo');
        $esAdmin  = session('admin') === 1;
        $centroId = session('centro_medico_id');

        $ticket->load([
            'medico',
            'admin',
            'cita.paciente',
            'mensajes.emisor',
            'archivos.emisor'
        ]);

        if ($cargo === 'Medico' && $ticket->medico_id !== $userId) {
            abort(403);
        }

        if (
            $esAdmin &&
            $centroId &&
            $ticket->medico &&
            $ticket->medico->centro_medico_id != $centroId
        ) {
            abort(403);
        }

        // Marcar leídos
        TicketMensaje::where('ticket_id', $ticket->id)
            ->where('emisor_id', '!=', $userId)
            ->where('leido', false)
            ->update(['leido' => true]);

        return view('TicketDetalle', compact('ticket'));
    }

    // ── Tomar ticket ──────────────────────────────────────────────────────
    public function tomar(Ticket $ticket)
    {
        if (session('admin') !== 1) abort(403);

        $centroId = session('centro_medico_id');

        $ticket->load('medico');

        if (
            $centroId &&
            $ticket->medico &&
            $ticket->medico->centro_medico_id != $centroId
        ) {
            abort(403);
        }

        if ($ticket->admin_id) {
            return response()->json([
                'error' => 'Este ticket ya fue tomado por otro administrador'
            ], 422);
        }

        $ticket->update([
            'admin_id'  => session('user_id'),
            'estado'    => 'en_progreso',
            'tomado_en' => Carbon::now(),
        ]);

        $ticket->load('admin', 'medico');

        NotificacionHelper::enviar(
            new \App\Models\Cita(),
            $ticket->medico_id,
            'Ticket tomado',
            "El administrador {$ticket->admin->name} tomó tu ticket: {$ticket->asunto}",
            'success',
            '/tickets/' . $ticket->id
        );

        broadcast(new NuevoTicketMensaje(
            new TicketMensaje([
                'ticket_id' => $ticket->id,
                'emisor_id' => session('user_id'),
                'contenido' => '__tomado__'
            ])
        ));

        CorreoHelper::ticketTomado($ticket);

        return response()->json([
            'ok' => true,
            'admin' => $ticket->admin->name . ' ' . $ticket->admin->Apellidos
        ]);
    }

    // ── Cerrar ticket ─────────────────────────────────────────────────────
    public function cerrar(Ticket $ticket)
    {
        if (session('admin') !== 1) abort(403);
        if ($ticket->admin_id !== session('user_id')) abort(403);

        $centroId = session('centro_medico_id');

        $ticket->load('medico');

        if (
            $centroId &&
            $ticket->medico &&
            $ticket->medico->centro_medico_id != $centroId
        ) {
            abort(403);
        }

        $ticket->update([
            'estado' => 'cerrado'
        ]);

        NotificacionHelper::enviar(
            new \App\Models\Cita(),
            $ticket->medico_id,
            'Ticket cerrado',
            "Tu ticket \"{$ticket->asunto}\" fue cerrado",
            'warning',
            '/tickets/' . $ticket->id
        );

        CorreoHelper::ticketCerrado($ticket);

        return response()->json(['ok' => true]);
    }

    // ── Mensaje ───────────────────────────────────────────────────────────
    public function mensaje(Request $request, Ticket $ticket)
    {
        $userId   = session('user_id');
        $cargo    = session('cargo');
        $esAdmin  = session('admin') === 1;
        $centroId = session('centro_medico_id');

        $ticket->load('medico');

        if ($cargo === 'Medico' && $ticket->medico_id !== $userId) {
            abort(403);
        }

        if (
            $esAdmin &&
            $centroId &&
            $ticket->medico &&
            $ticket->medico->centro_medico_id != $centroId
        ) {
            abort(403);
        }

        if ($esAdmin && $ticket->admin_id !== $userId) {
            abort(403);
        }

        if ($ticket->estado === 'cerrado') {
            return response()->json([
                'error' => 'El ticket está cerrado'
            ], 403);
        }

        $request->validate([
            'contenido' => 'required|string|max:2000'
        ]);

        $msg = TicketMensaje::create([
            'ticket_id' => $ticket->id,
            'emisor_id' => $userId,
            'contenido' => $request->contenido,
        ]);

        $msg->load('emisor');

        broadcast(new NuevoTicketMensaje($msg))->toOthers();

        $receptorId = $esAdmin
            ? $ticket->medico_id
            : $ticket->admin_id;

        if ($receptorId) {

            NotificacionHelper::enviar(
                new \App\Models\Cita(),
                $receptorId,
                'Nuevo mensaje en ticket',
                $ticket->asunto,
                'info',
                '/tickets/' . $ticket->id
            );

            $receptor = User::find($receptorId);

            if ($receptor) {
                CorreoHelper::nuevoMensajeTicket($msg, $receptor);
            }
        }

        return response()->json([
            'ok' => true,
            'mensaje' => [
                'id'        => $msg->id,
                'contenido' => $msg->contenido,
                'hora'      => $msg->created_at->format('H:i'),
                'emisor'    => $msg->emisor->name . ' ' . $msg->emisor->Apellidos,
                'emisor_id' => $msg->emisor_id,
            ]
        ]);
    }

    // ── Subir archivo ─────────────────────────────────────────────────────
    public function subirArchivo(Request $request, Ticket $ticket)
    {
        $userId   = session('user_id');
        $cargo    = session('cargo');
        $esAdmin  = session('admin') === 1;
        $centroId = session('centro_medico_id');

        $ticket->load('medico');

        if ($cargo === 'Medico' && $ticket->medico_id !== $userId) {
            abort(403);
        }

        if (
            $esAdmin &&
            $centroId &&
            $ticket->medico &&
            $ticket->medico->centro_medico_id != $centroId
        ) {
            abort(403);
        }

        if ($ticket->estado === 'cerrado') {
            return response()->json([
                'error' => 'El ticket está cerrado'
            ], 403);
        }

        $request->validate([
            'archivo' => 'required|file|max:5120'
        ]);

        $archivo = $request->file('archivo');
        $ruta    = $archivo->store('ticket_archivos', 'public');

        TicketArchivo::create([
            'ticket_id'       => $ticket->id,
            'emisor_id'       => $userId,
            'nombre_original' => $archivo->getClientOriginalName(),
            'ruta'            => $ruta,
            'mime_type'       => $archivo->getMimeType(),
        ]);

        return response()->json(['ok' => true]);
    }
}