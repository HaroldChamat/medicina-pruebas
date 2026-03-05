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
        $medicos = User::with(['cargo', 'especialidad'])
            ->whereHas('cargo', function ($q) {
                $q->where('Nombre_cargo', 'Medico');
            })
            ->get();

        $especialidades = Especialidad::all();

        return view('Especialidad', compact('medicos', 'especialidades'));
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
            'admin'     => 'required|integer|in:0,1',
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
        $user->admin      = $request->admin;
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
        // Solo admins pueden eliminar usuarios
        if (session('admin') !== 1) {
            return response()->json(['message' => 'No autorizado'], 403);
        }

        // Evitar que el admin se elimine a sí mismo
        if ($id == session('user_id')) {
            return response()->json(['message' => 'No puedes eliminarte a ti mismo'], 422);
        }

        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['success' => true, 'message' => 'Usuario eliminado correctamente']);
    }
}