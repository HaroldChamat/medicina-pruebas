<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\CentroMedico;
use App\Models\Cargo;

class SuperadminPacienteController extends Controller
{
    /**
     * Lista paginada de pacientes con búsqueda y filtros.
     */
    public function index(Request $request)
    {
        $query = User::with(['centroMedico', 'cargo'])
            ->whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Paciente'));

        // Búsqueda por nombre, apellidos, email o RUT
        if ($request->filled('buscar')) {
            $b = $request->buscar;
            $query->where(function ($q) use ($b) {
                $q->where('name',      'like', "%{$b}%")
                  ->orWhere('Apellidos','like', "%{$b}%")
                  ->orWhere('email',   'like', "%{$b}%")
                  ->orWhere('Rut',     'like', "%{$b}%");
            });
        }

        // Filtro por centro médico
        if ($request->filled('centro')) {
            $query->where('centro_medico_id', $request->centro);
        }

        // Filtro por estado activo/inactivo
        if ($request->filled('activo')) {
            $query->where('activo', $request->activo);
        }

        $pacientes = $query->orderBy('name')->paginate(15)->withQueryString();
        $centros   = CentroMedico::orderBy('nombre')->get();

        return view('superadmin.usuarios.pacientes', compact('pacientes', 'centros'));
    }

    /**
     * Actualizar datos de un paciente (AJAX).
     */
    public function update(Request $request, User $paciente)
    {
        $rules = [
            'name'            => 'required|string|max:120',
            'Apellidos'       => 'nullable|string|max:120',
            'email'           => "required|email|unique:users,email,{$paciente->id}",
            'Rut'             => "nullable|string|max:20|unique:users,Rut,{$paciente->id}",
            'telefono'        => 'nullable|string|max:20',
            'centro_medico_id'=> 'nullable|exists:centro_medico,id',
            'activo'          => 'required|in:0,1',
        ];

        // Contraseña opcional: solo si viene rellena
        if ($request->filled('password')) {
            $rules['password']              = 'string|min:8|max:100';
            $rules['password_confirmation'] = 'required_with:password|same:password';
        }

        $request->validate($rules, [
            'name.required'   => 'El nombre es obligatorio.',
            'email.required'  => 'El email es obligatorio.',
            'email.unique'    => 'Este email ya está en uso.',
            'Rut.unique'      => 'Este RUT ya está registrado.',
            'activo.required' => 'El estado es obligatorio.',
        ]);

        $data = $request->only([
            'name', 'Apellidos', 'email', 'Rut',
            'telefono', 'centro_medico_id', 'activo',
        ]);

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $paciente->update($data);
        $paciente->load('centroMedico');

        return response()->json([
            'ok'      => true,
            'message' => 'Paciente actualizado correctamente.',
            'paciente'=> [
                'id'           => $paciente->id,
                'name'         => $paciente->name,
                'Apellidos'    => $paciente->Apellidos,
                'email'        => $paciente->email,
                'Rut'          => $paciente->Rut,
                'telefono'     => $paciente->telefono,
                'activo'       => $paciente->activo,
                'centro_medico_id'   => $paciente->centro_medico_id,
                'centro_nombre'      => $paciente->centroMedico->nombre ?? null,
            ],
        ]);
    }

    /**
     * Cambiar solo el centro médico de un paciente (AJAX).
     */
    public function cambiarCentro(Request $request, User $paciente)
    {
        $request->validate([
            'centro_medico_id' => 'required|exists:centro_medico,id',
        ], [
            'centro_medico_id.required' => 'Selecciona un centro médico.',
            'centro_medico_id.exists'   => 'El centro seleccionado no existe.',
        ]);

        if ($paciente->centro_medico_id == $request->centro_medico_id) {
            return response()->json([
                'message' => 'El paciente ya pertenece a ese centro médico.',
            ], 422);
        }

        $paciente->update(['centro_medico_id' => $request->centro_medico_id]);
        $paciente->load('centroMedico');

        return response()->json([
            'ok'           => true,
            'message'      => 'Centro médico actualizado.',
            'centro_nombre'=> $paciente->centroMedico->nombre ?? null,
        ]);
    }

    /**
     * Eliminar un paciente (AJAX).
     */
    public function destroy(User $paciente)
    {
        // Verificar que realmente sea un paciente
        if ($paciente->cargo?->Nombre_cargo !== 'Paciente') {
            return response()->json(['message' => 'El usuario no es un paciente.'], 422);
        }

        $paciente->delete();

        return response()->json([
            'ok'      => true,
            'message' => 'Paciente eliminado correctamente.',
        ]);
    }
}