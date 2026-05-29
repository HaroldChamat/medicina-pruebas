<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cargo;
use App\Models\User;
use App\Models\Especialidad;
use App\Models\CentroMedico;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $cargos  = Cargo::all();
        $centros = session('admin') === 1 ? collect() : CentroMedico::orderBy('nombre')->get();
        return view('C_usuario', compact('cargos', 'centros'));
    }

    public function index_welcome()
    {
        $usuario = User::with(['cargo', 'centroMedico'])->find(session('user_id'));

        $centroId = session('centro_medico_id');

        $medicos = User::with('cargo')
            ->whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Medico'))
            ->when($centroId, fn($q) => $q->where('centro_medico_id', $centroId))
            ->get();

        $pacientes = User::with('cargo')
            ->whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Paciente'))
            ->when($centroId, fn($q) => $q->where('centro_medico_id', $centroId))
            ->get();

        $nombreCargo = $usuario?->cargo?->Nombre_cargo;

        return view('welcome', compact('nombreCargo', 'usuario', 'medicos', 'pacientes'));
    }

    public function index_especialidad()
    {
        $centroId = session('centro_medico_id');

        $medicos = User::with(['cargo', 'especialidades'])
            ->whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Medico'))
            ->where('activo', 1)
            ->when($centroId, fn($q) => $q->where('centro_medico_id', $centroId))
            ->get();

        $especialidades = Especialidad::all();

        return view('Especialidad', compact('medicos', 'especialidades'));
    }

    public function index_medicos()
    {
        if (session('admin') !== 1) abort(403);

        $centroId = session('centro_medico_id');

        $medicos = User::with(['cargo', 'especialidades'])
            ->whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Medico'))
            ->when($centroId, fn($q) => $q->where('centro_medico_id', $centroId))
            ->get();

        return view('admin.medicos', compact('medicos'));
    }

    public function index_pacientes()
    {
        if (session('admin') !== 1) abort(403);

        $centroId = session('centro_medico_id');

        $pacientes = User::with('cargo')
            ->whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Paciente'))
            ->when($centroId, fn($q) => $q->where('centro_medico_id', $centroId))
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
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'Rut.unique'   => 'Este RUT ya está registrado.',
            'password.min' => 'La contraseña debe tener al menos 6 caracteres.',
        ]);

        $cargosProtegidos = Cargo::whereIn('Nombre_cargo', ['Otro', 'Medico'])
            ->pluck('id')->toArray();

        if (in_array($request->id_cargo, $cargosProtegidos) && session('admin') !== 1) {
            return response()->json(['message' => 'No tienes permisos para asignar este cargo.'], 403);
        }

        // ── Determinar centro médico ──────────────────────────────────────
        // Si hay un admin en sesión → se asigna su centro automáticamente.
        // Si es un registro libre (paciente desde la web) → usa el centro seleccionado.
        if (session('admin') === 1) {
            $centroMedicoId = session('centro_medico_id');
        } else {
            // Validar que el centro fue enviado y existe
            $request->validate([
                'centro_medico_id' => 'required|exists:centro_medico,id',
            ], [
                'centro_medico_id.required' => 'Debes seleccionar un centro médico.',
                'centro_medico_id.exists'   => 'El centro médico seleccionado no es válido.',
            ]);
            $centroMedicoId = $request->centro_medico_id;
        }

        $user = new User();
        $user->id_cargo         = $request->id_cargo;
        $user->name             = $request->name;
        $user->admin            = (int) ($request->admin ?? 0);
        $user->activo           = 1;
        $user->Apellidos        = $request->Apellidos;
        $user->email            = $request->email;
        $user->Rut              = $request->Rut;
        $user->telefono         = $request->telefono;
        $user->password         = Hash::make($request->password);
        $user->centro_medico_id = $centroMedicoId;
        $user->save();

        return response()->json(['success' => true, 'message' => 'Usuario creado exitosamente.']);
    }

    public function update(Request $request, $id)
    {
        if (session('admin') !== 1) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        // Admin solo puede editar usuarios de su centro
        $user = User::findOrFail($id);
        $centroId = session('centro_medico_id');

        if ($centroId && $user->centro_medico_id !== $centroId) {
            return response()->json(['message' => 'No autorizado. El usuario no pertenece a tu centro.'], 403);
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

        $request->validate($rules);

        $user->name      = $request->name;
        $user->Apellidos = $request->Apellidos;
        $user->email     = $request->email;
        $user->telefono  = $request->telefono;

        if ($request->filled('Rut'))      $user->Rut      = $request->Rut;
        if ($request->filled('password')) $user->password = Hash::make($request->password);

        $user->save();

        return response()->json(['success' => true, 'message' => 'Usuario actualizado correctamente.']);
    }

    public function desactivar($id)
    {
        if (session('admin') !== 1) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        $user = User::findOrFail($id);
        $centroId = session('centro_medico_id');

        if ($centroId && $user->centro_medico_id !== $centroId) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        if ($id == session('user_id')) {
            return response()->json(['message' => 'No puedes desactivarte a ti mismo.'], 422);
        }

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
        $centroId = session('centro_medico_id');

        if ($centroId && $user->centro_medico_id !== $centroId) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

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

        // Admin solo puede eliminar usuarios de su centro
        $centroId = session('centro_medico_id');
        if ($centroId && $user->centro_medico_id !== $centroId) {
            return response()->json(['message' => 'No autorizado. El usuario no pertenece a tu centro.'], 403);
        }

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

                $adminId = User::where('admin', 1)
                    ->where('centro_medico_id', $user->centro_medico_id)
                    ->value('id');

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