<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class EspecialidadController extends Controller
{
 
    public function store(Request $request)
    {
        $request->validate([
            'Nombre_especialidad' => 'required|string|max:100|unique:especialidads,Nombre_especialidad',
        ]);

        $especialidad = \App\Models\Especialidad::create([
            'Nombre_especialidad' => ucwords(strtolower(trim($request->Nombre_especialidad))),
        ]);

        return response()->json([
            'ok'   => true,
            'id'   => $especialidad->id,
            'nombre' => $especialidad->Nombre_especialidad,
        ]);
    }

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