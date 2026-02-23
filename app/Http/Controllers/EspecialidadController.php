<?php

namespace App\Http\Controllers;
use App\Models\User;

use Illuminate\Http\Request;

class EspecialidadController extends Controller
{
    //

    public function actualizarEspecialidad(Request $request)
    {
        $request->validate([
            'medico_id' => 'required|exists:users,id',
            'especialidad_id' => 'required|exists:especialidads,id',
        ]);

        $medico = User::findOrFail($request->medico_id);
        $medico->especialidad_id = $request->especialidad_id;
        $medico->save();

        return response()->json(['ok' => true]);
    }



}
