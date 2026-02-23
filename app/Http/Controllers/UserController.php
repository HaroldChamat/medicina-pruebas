<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cargo;
use App\Models\User;
use App\Models\Especialidad;

class UserController extends Controller
{
    //

    public function index()
    {
        $cargos = Cargo::all();
        return view('C_usuario', compact('cargos'));
    }

    public function index_welcome()
    {
        // usuario logueado
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
            'name' => 'required|string|max:255',
            'Apellidos' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'Rut' => 'required|string|unique:users,Rut',
            'telefono' => 'required|string|max:20',
            'id_cargo' => 'required|exists:cargos,id',
            'admin' => 'required|integer|in:0,1',
        ]);

        // Obtener cargos protegidos
        $cargosProtegidos = Cargo::whereIn('Nombre_cargo', ['Otro', 'Medico'])
            ->pluck('id')
            ->toArray();

        // 🔒 Si intenta asignar Admin o Medico y NO es Admin → BLOQUEAR
        if (
            in_array($request->id_cargo, $cargosProtegidos) &&
            session('admin') !== 1
        ) {
            return response()->json([
                'message' => 'No tienes permisos para asignar este cargo'
            ], 403);
        }

        $user = new \App\Models\User();
        $user->id_cargo = $request->id_cargo;
        $user->name = $request->name;
        $user->admin = $request->admin;
        $user->Apellidos = $request->Apellidos;
        $user->email = $request->email;
        $user->Rut = $request->Rut;
        $user->telefono = $request->telefono;
        
        $user->save();

        return response()->json(['success' => true, 'message' => 'Usuario creado exitosamente']);
    }
}
