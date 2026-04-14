<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cargo;
use App\Models\User;
use App\Models\Especialidad;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $cargos = Cargo::all();
        return view('C_usuario', compact('cargos'));
    }

    public function index_welcome()
    {
        $usuario = User::with('cargo')->find(session('user_id'));

        $medicos = User::with('cargo')
            ->whereHas('cargo', function ($q) {
                $q->where('Nombre_cargo', 'Medico');
            })
            ->get();

        $pacientes = User::with('cargo')
            ->whereHas('cargo', function ($q) {
                $q->where('Nombre_cargo', 'Paciente');
            })
            ->get();

        $nombreCargo = $usuario?->cargo?->Nombre_cargo;

        return view('welcome', compact(
            'nombreCargo',
            'usuario',
            'medicos',
            'pacientes'
        ));
    }

    public function index_especialidad()
    {
        $medicos = User::with(['cargo', 'especialidades']) 
            ->whereHas('cargo', function ($q) {
                $q->where('Nombre_cargo', 'Medico');
            })
            ->get();

        $especialidades = \App\Models\Especialidad::all();

        return view('Especialidad', compact('medicos', 'especialidades')); 
    }

    public function index_medicos()
    {
        if (session('admin') !== 1) abort(403);

        $medicos = User::with(['cargo', 'especialidades'])
            ->whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Medico'))
            ->get();

        return view('admin.medicos', compact('medicos'));
    }

    public function index_pacientes()
    {
        if (session('admin') !== 1) abort(403);

        $pacientes = User::with('cargo')
            ->whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Paciente'))
            ->get();

        return view('admin.pacientes', compact('pacientes'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'Apellidos' => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'Rut'       => 'required|string|unique:users,Rut',
            'telefono'  => 'required|string|max:20',
            'id_cargo'  => 'required|exists:cargos,id',
            'password'  => 'required|string|min:6',
            'admin'     => 'nullable|in:0,1',
        ]);

        $cargosProtegidos = Cargo::whereIn('Nombre_cargo', ['Otro', 'Medico'])
            ->pluck('id')
            ->toArray();

        if (
            in_array($request->id_cargo, $cargosProtegidos) &&
            session('admin') !== 1
        ) {
            return response()->json([
                'message' => 'No tienes permisos para asignar este cargo'
            ], 403);
        }

        $user = new User();
        $user->id_cargo   = $request->id_cargo;
        $user->name       = $request->name;
        $user->admin = (int) ($request->admin ?? 0);
        $user->Apellidos  = $request->Apellidos;
        $user->email      = $request->email;
        $user->Rut        = $request->Rut;
        $user->telefono   = $request->telefono;
        $user->password   = Hash::make($request->password);
        $user->save();

        return response()->json(['success' => true, 'message' => 'Usuario creado exitosamente']);
    }

    public function update(Request $request, $id)
    {
        // Solo admins pueden editar usuarios
        if (session('admin') !== 1) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        $request->validate([
            'name'      => 'required|string|max:255',
            'Apellidos' => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $id,
            'telefono'  => 'nullable|string|max:20',
        ]);

        $user = User::findOrFail($id);
        $user->name      = $request->name;
        $user->Apellidos = $request->Apellidos;
        $user->email     = $request->email;
        $user->telefono  = $request->telefono;
        $user->save();

        return response()->json(['success' => true, 'message' => 'Usuario actualizado correctamente']);
    }

    public function destroy($id)
    {
        if (session('admin') !== 1) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        if ($id == session('user_id')) {
            return response()->json(['message' => 'No puedes eliminarte a ti mismo'], 422);
        }

        $user = User::with('cargo')->findOrFail($id);
        $cargo = $user->cargo?->Nombre_cargo;

        \DB::transaction(function () use ($user, $id, $cargo) {

            if ($cargo === 'Medico') {
                $citaIds = \App\Models\Cita::where('medico_id', $id)->pluck('id');

                // Mensajes de chat de esas citas
                \App\Models\Mensaje::whereIn('cita_id', $citaIds)->delete();

                // Tickets del médico (con mensajes y archivos)
                \App\Models\Ticket::where('medico_id', $id)->each(function ($ticket) {
                    $ticket->mensajes()->delete();
                    $ticket->archivos()->delete();
                    $ticket->delete();
                });

                // Desligar citas del médico: reasignar medico_id a null
                // no se puede porque tiene FK NOT NULL — en cambio anonimizamos
                // dejando los informes intactos pero desvinculando el médico:
                // Primero guardamos un "usuario fantasma" o simplemente
                // ponemos medico_id = admin actual
                $adminId = \App\Models\User::where('admin', 1)->value('id');

                // Preservar informes: actualizar medico_id de las citas que tienen informe
                \App\Models\Cita::where('medico_id', $id)
                    ->whereHas('enfermedad')
                    ->update(['medico_id' => $adminId]);

                // Borrar citas SIN informe (se van con sus mensajes ya eliminados)
                \App\Models\Cita::where('medico_id', $id)->delete();

                \App\Models\Horario::where('medico_id', $id)->delete();
                $user->especialidades()->detach();
                \App\Models\Notificacion::where('user_id', $id)->delete();

            } elseif ($cargo === 'Paciente') {
                $citaIds = \App\Models\Cita::where('paciente_id', $id)->pluck('id');
                \App\Models\Mensaje::whereIn('cita_id', $citaIds)->delete();

                $adminId = \App\Models\User::where('admin', 1)->value('id');

                // Preservar citas con informe reasignando paciente a admin
                \App\Models\Cita::where('paciente_id', $id)
                    ->whereHas('enfermedad')
                    ->update(['paciente_id' => $adminId]);

                // Borrar citas sin informe
                \App\Models\Cita::where('paciente_id', $id)->delete();

                \App\Models\Notificacion::where('user_id', $id)->delete();
            }

            $user->delete();
        });

        return response()->json(['success' => true, 'message' => 'Usuario eliminado correctamente']);
    }
}