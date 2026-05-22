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
            ->where('activo', 1)
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
        ], [
            'name.required'      => 'El nombre es obligatorio.',
            'name.string'        => 'El nombre debe ser texto.',
            'Apellidos.required' => 'Los apellidos son obligatorios.',
            'email.required'     => 'El correo electrónico es obligatorio.',
            'email.email'        => 'El formato del correo electrónico no es válido.',
            'email.unique'       => 'Este correo electrónico ya está registrado. Por favor ingresa otro.',
            'Rut.required'       => 'El RUT es obligatorio.',
            'Rut.unique'         => 'Este RUT ya está registrado. Por favor ingresa otro RUT.',
            'telefono.required'  => 'El teléfono es obligatorio.',
            'id_cargo.required'  => 'Debes seleccionar un cargo.',
            'id_cargo.exists'    => 'El cargo seleccionado no es válido.',
            'password.required'  => 'La contraseña es obligatoria.',
            'password.min'       => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        $cargosProtegidos = Cargo::whereIn('Nombre_cargo', ['Otro', 'Medico'])
            ->pluck('id')
            ->toArray();

        if (
            in_array($request->id_cargo, $cargosProtegidos) &&
            session('admin') !== 1
        ) {
            return response()->json([
                'message' => 'No tienes permisos para asignar este cargo.'
            ], 403);
        }

        $user = new User();
        $user->id_cargo   = $request->id_cargo;
        $user->name       = $request->name;
        $user->admin      = (int) ($request->admin ?? 0);
        $user->activo     = 1;
        $user->Apellidos  = $request->Apellidos;
        $user->email      = $request->email;
        $user->Rut        = $request->Rut;
        $user->telefono   = $request->telefono;
        $user->password   = Hash::make($request->password);
        $user->save();

        return response()->json(['success' => true, 'message' => 'Usuario creado exitosamente.']);
    }

    public function update(Request $request, $id)
    {
        if (session('admin') !== 1) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $rules = [
            'name'      => 'required|string|max:255',
            'Apellidos' => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $id,
            'telefono'  => 'nullable|string|max:20',
            'Rut'       => 'nullable|string|unique:users,Rut,' . $id,
        ];

        if ($request->filled('password')) {
            $rules['password'] = 'string|min:6';
        }

        $messages = [
            'name.required'      => 'El nombre es obligatorio.',
            'Apellidos.required' => 'Los apellidos son obligatorios.',
            'email.required'     => 'El correo electrónico es obligatorio.',
            'email.email'        => 'El formato del correo electrónico no es válido.',
            'email.unique'       => 'Este correo electrónico ya está en uso por otro usuario. Por favor ingresa otro.',
            'Rut.unique'         => 'Este RUT ya está registrado en otro usuario. Por favor ingresa otro RUT.',
            'telefono.max'       => 'El teléfono no puede tener más de 20 caracteres.',
            'password.min'       => 'La contraseña debe tener al menos 6 caracteres.',
        ];

        $request->validate($rules, $messages);

        $user = User::findOrFail($id);
        $user->name      = $request->name;
        $user->Apellidos = $request->Apellidos;
        $user->email     = $request->email;
        $user->telefono  = $request->telefono;

        if ($request->filled('Rut')) {
            $user->Rut = $request->Rut;
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json(['success' => true, 'message' => 'Usuario actualizado correctamente.']);
    }

    public function desactivar($id)
    {
        if (session('admin') !== 1) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        if ($id == session('user_id')) {
            return response()->json(['message' => 'No puedes desactivarte a ti mismo.'], 422);
        }

        $user = User::findOrFail($id);
        $user->activo = 0;
        $user->save();

        return response()->json(['success' => true, 'message' => 'Médico desactivado correctamente.']);
    }

    public function activar($id)
    {
        if (session('admin') !== 1) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $user = User::findOrFail($id);
        $user->activo = 1;
        $user->save();

        return response()->json(['success' => true, 'message' => 'Médico reactivado correctamente.']);
    }

    public function destroy($id)
    {
        if (session('admin') !== 1) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        if ($id == session('user_id')) {
            return response()->json(['message' => 'No puedes eliminarte a ti mismo.'], 422);
        }

        $user = User::with('cargo')->findOrFail($id);
        $cargo = $user->cargo?->Nombre_cargo;

        \DB::transaction(function () use ($user, $id, $cargo) {

            if ($cargo === 'Medico') {
                $user->activo = 0;
                $user->save();
                return;
            }

            if ($cargo === 'Paciente') {
                $citaIds = \App\Models\Cita::where('paciente_id', $id)->pluck('id');
                \App\Models\Mensaje::whereIn('cita_id', $citaIds)->delete();

                $adminId = User::where('admin', 1)->value('id');

                \App\Models\Cita::where('paciente_id', $id)
                    ->whereHas('enfermedad')
                    ->update(['paciente_id' => $adminId]);

                \App\Models\Cita::where('paciente_id', $id)->delete();

                \App\Models\Notificacion::where('user_id', $id)->delete();
            }

            $user->delete();
        });

        return response()->json(['success' => true, 'message' => 'Usuario eliminado correctamente.']);
    }
}