<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Cargo;
use App\Models\CentroMedico;
use Illuminate\Support\Facades\Hash;

class SuperadminAdminController extends Controller
{
    public function index(Request $request)
    {
        $filtroCentro = $request->query('centro_id', '');

        $query = User::where('admin', 1)
            ->with(['cargo', 'centroMedico'])
            ->whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Admin'));

        if ($filtroCentro) {
            $query->where('centro_medico_id', $filtroCentro);
        }

        $admins  = $query->get();
        $centros = CentroMedico::orderBy('nombre')->get();

        return view('superadmin.admins.index', compact('admins', 'centros', 'filtroCentro'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'Apellidos'       => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email',
            'Rut'             => 'required|string|unique:users,Rut',
            'telefono'        => 'required|string|max:20',
            'password'        => 'required|string|min:6',
            'centro_medico_id'=> 'required|exists:centro_medico,id',
        ], [
            'email.unique'    => 'Este correo ya está registrado.',
            'Rut.unique'      => 'Este RUT ya está registrado.',
            'password.min'    => 'La contraseña debe tener al menos 6 caracteres.',
            'centro_medico_id.required' => 'Debes asignar un centro médico.',
        ]);

        $cargoAdmin = Cargo::where('Nombre_cargo', 'Admin')->first();
        if (!$cargoAdmin) {
            return response()->json(['message' => 'Cargo Admin no encontrado'], 500);
        }

        $user = User::create([
            'name'                     => $request->name,
            'Apellidos'                => $request->Apellidos,
            'email'                    => $request->email,
            'Rut'                      => $request->Rut,
            'telefono'                 => $request->telefono,
            'password'                 => Hash::make($request->password),
            'id_cargo'                 => $cargoAdmin->id,
            'admin'                    => 1,
            'activo'                   => 1,
            'centro_medico_id'         => $request->centro_medico_id,
            'creado_por_superadmin_id' => session('superadmin_id'),
        ]);

        return response()->json(['ok' => true, 'id' => $user->id]);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'      => 'required|string|max:255',
            'Apellidos' => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $id,
            'Rut'       => 'nullable|string|unique:users,Rut,' . $id,
            'telefono'  => 'nullable|string|max:20',
            'centro_medico_id' => 'nullable|exists:centro_medico,id',
        ], [
            'email.unique' => 'Este correo ya está en uso.',
            'Rut.unique'   => 'Este RUT ya está registrado.',
        ]);

        $user->update([
            'name'             => $request->name,
            'Apellidos'        => $request->Apellidos,
            'email'            => $request->email,
            'Rut'              => $request->Rut ?? $user->Rut,
            'telefono'         => $request->telefono,
            'centro_medico_id' => $request->centro_medico_id ?? $user->centro_medico_id,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return response()->json(['ok' => true]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['ok' => true]);
    }
}