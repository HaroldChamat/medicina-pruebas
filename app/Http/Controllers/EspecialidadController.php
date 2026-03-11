<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class EspecialidadController extends Controller
{
 
    public function actualizarEspecialidad(Request $request)
    {
      
        $request->validate([
            'medico_id'         => 'required|exists:users,id',
            'especialidad_id'   => 'required|array|min:1|max:4', 
            'especialidad_id.*' => 'exists:especialidads,id', 
        ]);

        $medico = User::findOrFail($request->medico_id);

        $medico->especialidades()->sync($request->especialidad_id);

        return response()->json([
            'ok' => true,
            'message' => 'Especialidades actualizadas correctamente'
        ]);
    }
}