<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'rut' => 'required'
        ]);

        // buscar usuario por RUT
        $user = User::with('cargo')
            ->where('Rut', $request->rut)
            ->first();
        // si no se encuentra el usuario o no tiene cargo asignado, mostrar error
        if (!$user) {
            return back()->withErrors([
                'rut' => 'Usuario no encontrado'
            ]);
        } else {
            if (!$user->cargo) {
            return back()->withErrors([
                'rut' => 'Usuario sin cargo asignado'
            ]);
        }


            // guardar sesión (si decides seguir usándola)
            session()->put([
                'user_id' => $user->id,
                'cargo'   => $user->cargo->Nombre_cargo,
                'admin'   => (int) ($user->admin ?? 0)
            ]);

            return redirect('/');
        }
    }

    public function logout()
    {
        session()->flush();
        return redirect('/');
    }
}
