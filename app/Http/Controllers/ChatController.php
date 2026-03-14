<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\Mensaje;
use App\Events\NuevoMensaje;
use Carbon\Carbon;
use App\Helpers\CorreoHelper;

class ChatController extends Controller
{
    /**
     * Lista de chats disponibles para el usuario actual
     */
    public function index()
    {
        $userId = session('user_id');
        $cargo  = session('cargo');

        if ($cargo === 'Paciente') {
            // Paciente ve sus citas programadas o finalizadas (con 2 días de gracia)
            $citas = Cita::with(['medico', 'paciente'])
                ->where('paciente_id', $userId)
                ->where(function ($q) {
                    $q->where('estado', 'Programada')
                      ->orWhere(function ($q2) {
                          $q2->where('estado', 'Finalizada')
                             ->where('Fecha_y_hora', '>=', Carbon::now()->subDays(2));
                      });
                })
                ->orderBy('Fecha_y_hora', 'desc')
                ->get();

        } elseif ($cargo === 'Medico') {
            // Médico ve sus citas programadas o finalizadas (con 2 días de gracia)
            $citas = Cita::with(['medico', 'paciente'])
                ->where('medico_id', $userId)
                ->where(function ($q) {
                    $q->where('estado', 'Programada')
                      ->orWhere(function ($q2) {
                          $q2->where('estado', 'Finalizada')
                             ->where('Fecha_y_hora', '>=', Carbon::now()->subDays(2));
                      });
                })
                ->orderBy('Fecha_y_hora', 'desc')
                ->get();
        } else {
            $citas = collect();
        }

        return view('Chat', compact('citas'));
    }

    /**
     * Abre el chat de una cita específica
     */
    public function show(Cita $cita)
    {
        $userId = session('user_id');
        $cargo  = session('cargo');

        // Verificar que el usuario pertenece a esta cita
        if ($cargo === 'Paciente' && $cita->paciente_id !== $userId) abort(403);
        if ($cargo === 'Medico'   && $cita->medico_id   !== $userId) abort(403);

        // Verificar que el chat está activo
        if (!$this->chatActivo($cita)) {
            return redirect()->route('chat.index')
                ->with('error', 'Este chat ya no está disponible.');
        }

        // Marcar mensajes como leídos
        Mensaje::where('cita_id', $cita->id)
            ->where('receptor_id', $userId)
            ->where('leido', false)
            ->update(['leido' => true]);

        $mensajes = Mensaje::with(['emisor'])
            ->where('cita_id', $cita->id)
            ->orderBy('created_at', 'asc')
            ->get();

        $cita->load(['medico', 'paciente']);

        // Determinar con quién está hablando
        $otroUsuario = $cargo === 'Paciente' ? $cita->medico : $cita->paciente;

        return view('ChatConversacion', compact('cita', 'mensajes', 'otroUsuario'));
    }

    /**
     * Enviar mensaje
     */
    public function store(Request $request, Cita $cita)
    {
        $userId = session('user_id');
        $cargo  = session('cargo');

        if ($cargo === 'Paciente' && $cita->paciente_id !== $userId) abort(403);
        if ($cargo === 'Medico'   && $cita->medico_id   !== $userId) abort(403);

        if (!$this->chatActivo($cita)) {
            return response()->json(['error' => 'Chat cerrado'], 403);
        }

        $request->validate(['contenido' => 'required|string|max:1000']);

        $receptorId = $cargo === 'Paciente' ? $cita->medico_id : $cita->paciente_id;

        $mensaje = Mensaje::create([
            'cita_id'    => $cita->id,
            'emisor_id'  => $userId,
            'receptor_id'=> $receptorId,
            'contenido'  => $request->contenido,
        ]);

        $mensaje->load('emisor');

        broadcast(new NuevoMensaje($mensaje))->toOthers();

        CorreoHelper::nuevoMensajeChat($mensaje);
        return response()->json([
            'ok'      => true,
            'mensaje' => [
                'id'        => $mensaje->id,
                'contenido' => $mensaje->contenido,
                'hora'      => $mensaje->created_at->format('H:i'),
                'emisor'    => $mensaje->emisor->name . ' ' . $mensaje->emisor->Apellidos,
                'emisor_id' => $mensaje->emisor_id,
            ]
        ]);
    }

    /**
     * Verifica si el chat está activo
     */
    private function chatActivo(Cita $cita): bool
    {
        if ($cita->estado === 'Programada') return true;

        if ($cita->estado === 'Finalizada') {
            $limite = Carbon::parse($cita->Fecha_y_hora)->addDays(2);
            return Carbon::now()->lt($limite);
        }

        return false;
    }
}