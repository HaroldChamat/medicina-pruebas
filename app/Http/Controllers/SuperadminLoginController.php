<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Superadmin;

class SuperadminLoginController extends Controller
{
    public function showLogin()
    {
        if (session()->has('superadmin_id')) {
            return redirect()->route('superadmin.dashboard');
        }
        return view('superadmin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $superadmin = Superadmin::where('email', $request->email)->first();

        if (!$superadmin || !Hash::check($request->password, $superadmin->password)) {
            return back()->withErrors(['email' => 'Credenciales incorrectas']);
        }

        session()->put([
            'superadmin_id'     => $superadmin->id,
            'superadmin_nombre' => $superadmin->name . ' ' . $superadmin->Apellidos,
            'superadmin_email'  => $superadmin->email,
        ]);

        return redirect()->route('superadmin.dashboard');
    }

    public function logout()
    {
        session()->forget(['superadmin_id', 'superadmin_nombre', 'superadmin_email']);
        return redirect()->route('superadmin.login');
    }
}