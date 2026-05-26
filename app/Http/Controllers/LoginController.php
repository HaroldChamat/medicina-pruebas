<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Superadmin;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'rut'      => 'required',
            'password' => 'required',
        ]);

        // ── Intentar login como Superadmin primero ────────────────────────
        $superadmin = Superadmin::where('Rut', $request->rut)->first();

        if ($superadmin && Hash::check($request->password, $superadmin->password)) {
            session()->put([
                'superadmin_id'     => $superadmin->id,
                'superadmin_nombre' => $superadmin->name . ' ' . $superadmin->Apellidos,
                'superadmin_email'  => $superadmin->email,
            ]);
            return redirect()->route('superadmin.dashboard');
        }

        // ── Login normal de usuarios ──────────────────────────────────────
        $user = User::with('cargo')
            ->where('Rut', $request->rut)
            ->first();

        if (!$user) {
            return back()->withErrors(['rut' => 'Usuario no encontrado']);
        }

        if (!$user->cargo) {
            return back()->withErrors(['rut' => 'Usuario sin cargo asignado']);
        }

        // Verificar contraseña
        if (!Hash::check($request->password, $user->password)) {
            return back()->withErrors(['rut' => 'Contraseña incorrecta']);
        }

        session()->put([
            'user_id' => $user->id,
            'cargo'   => $user->cargo->Nombre_cargo,
            'admin'   => (int) ($user->admin ?? 0),
            'nombre'  => $user->name,
        ]);

        return redirect('/login');
    }

    public function logout()
    {
        session()->flush();
        return redirect('/');
    }
}