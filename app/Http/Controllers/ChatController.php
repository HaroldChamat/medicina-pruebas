<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\Mensaje;
use App\Models\User;
use App\Events\NuevoMensaje;
use Carbon\Carbon;

class ChatController extends Controller
{
    public function index()
    {
        $userId  = session('user_id');
        $cargo   = session('cargo');
        $esAdmin = session('admin') === 1;

        if ($cargo === 'Paciente') {
            // Paciente ve sus citas programadas o finalizadas recientes
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

        } elseif ($esAdmin) {
            // Admin ve todas las citas programadas o finalizadas recientes
            $citas = Cita::with(['medico', 'paciente'])
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
            abort(403);
        }

        return view('Chat', compact('citas'));
    }

    public function show(Cita $cita)
    {
        $userId  = session('user_id');
        $cargo   = session('cargo');
        $esAdmin = session('admin') === 1;

        // Solo admin y el paciente de esa cita pueden acceder
        if ($cargo === 'Paciente' && $cita->paciente_id !== $userId) abort(403);
        if (!$esAdmin && $cargo !== 'Paciente') abort(403);

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

        // El "otro usuario" depende del rol
        $otroUsuario = $cargo === 'Paciente'
            ? User::where('admin', 1)->first()  // paciente habla con admin
            : $cita->paciente;                   // admin habla con paciente

        return view('ChatConversacion', compact('cita', 'mensajes', 'otroUsuario'));
    }

    public function store(Request $request, Cita $cita)
    {
        $userId  = session('user_id');
        $cargo   = session('cargo');
        $esAdmin = session('admin') === 1;

        if ($cargo === 'Paciente' && $cita->paciente_id !== $userId) abort(403);
        if (!$esAdmin && $cargo !== 'Paciente') abort(403);

        if (!$this->chatActivo($cita)) {
            return response()->json(['error' => 'Chat cerrado'], 403);
        }

        $request->validate(['contenido' => 'required|string|max:1000']);

        // Receptor: si es paciente → admin, si es admin → paciente
        if ($cargo === 'Paciente') {
            $receptor = User::where('admin', 1)->first();
            $receptorId = $receptor->id;
        } else {
            $receptorId = $cita->paciente_id;
        }

        $mensaje = Mensaje::create([
            'cita_id'    => $cita->id,
            'emisor_id'  => $userId,
            'receptor_id'=> $receptorId,
            'contenido'  => $request->contenido,
        ]);

        $mensaje->load('emisor');
        broadcast(new NuevoMensaje($mensaje))->toOthers();

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