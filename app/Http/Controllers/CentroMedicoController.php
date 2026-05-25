<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CentroMedico;

class CentroMedicoController extends Controller
{
    public function index()
    {
        if (session('admin') !== 1) abort(403);

        $centros = CentroMedico::withCount('users')->get();
        return view('admin.centros_medicos', compact('centros'));
    }

    public function store(Request $request)
    {
        if (session('admin') !== 1) abort(403);

        $request->validate([
            'nombre'    => 'required|string|max:200',
            'direccion' => 'required|string|max:300',
        ]);

        $centro = CentroMedico::create([
            'nombre'    => trim($request->nombre),
            'direccion' => trim($request->direccion),
        ]);

        return response()->json([
            'ok'       => true,
            'id'       => $centro->id,
            'nombre'   => $centro->nombre,
            'direccion'=> $centro->direccion,
        ]);
    }

    public function update(Request $request, $id)
    {
        if (session('admin') !== 1) abort(403);

        $request->validate([
            'nombre'    => 'required|string|max:200',
            'direccion' => 'required|string|max:300',
        ]);

        $centro = CentroMedico::findOrFail($id);
        $centro->update([
            'nombre'    => trim($request->nombre),
            'direccion' => trim($request->direccion),
        ]);

        return response()->json(['ok' => true]);
    }

    public function destroy($id)
    {
        if (session('admin') !== 1) abort(403);

        $centro = CentroMedico::findOrFail($id);
        // Desasociar usuarios antes de eliminar
        $centro->users()->update(['centro_medico_id' => null]);
        $centro->delete();

        return response()->json(['ok' => true]);
    }
}