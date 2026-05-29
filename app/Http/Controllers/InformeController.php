<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\Enfermedad;
use App\Models\Tratamiento;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use App\Helpers\NotificacionHelper;
use App\Helpers\CorreoHelper;

class InformeController extends Controller
{
    /**
     * Scope base: citas del centro del admin logueado.
     */
    private function citasDelCentro()
    {
        $centroId = session('centro_medico_id');
        $medicoIds = $centroId
            ? User::where('centro_medico_id', $centroId)
                ->whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Medico'))
                ->pluck('id')->toArray()
            : [];

        return Cita::whereIn('medico_id', $medicoIds);
    }

    public function index()
    {
        $cargo  = session('cargo');
        $userId = session('user_id');

        if ($cargo === 'Paciente') {
            $Citas = Cita::with(['medico', 'paciente', 'enfermedad', 'tratamiento'])
                ->where('paciente_id', $userId)
                ->whereHas('enfermedad')
                ->whereHas('tratamiento')
                ->get();

            return view('MisInformes', compact('Citas'));
        }

        // Admin: solo ve informes de su centro
        return view('Informe', [
            'Citas' => $this->citasDelCentro()
                ->with(['medico', 'paciente', 'enfermedad', 'tratamiento'])
                ->get(),
        ]);
    }

    public function index_paciente()
    {
        $cargo  = session('cargo');
        $userId = session('user_id');

        $query = Cita::with(['medico', 'paciente', 'enfermedad', 'tratamiento'])
            ->whereHas('enfermedad')
            ->whereHas('tratamiento');

        if ($cargo === 'Medico') {
            $query->where('medico_id', $userId);
        } elseif ($cargo === 'Admin') {
            // Admin: solo su centro
            $centroId  = session('centro_medico_id');
            $medicoIds = $centroId
                ? User::where('centro_medico_id', $centroId)
                    ->whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Medico'))
                    ->pluck('id')->toArray()
                : [];
            $query->whereIn('medico_id', $medicoIds);
        }

        $citas   = $query->get();
        $medicos = User::whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Medico'))
            ->when(session('centro_medico_id'), fn($q) => $q->where('centro_medico_id', session('centro_medico_id')))
            ->get();

        return view('Informacion', compact('citas', 'medicos'));
    }

    public function create(Cita $cita)
    {
        $this->autorizarCita($cita);
        return view('Informe', compact('cita'));
    }

    public function show(Cita $cita)
    {
        if (session('cargo') === 'Paciente' && $cita->paciente_id !== session('user_id')) {
            abort(403, 'No autorizado');
        }

        $cita->load(['medico.especialidades', 'paciente', 'enfermedad', 'tratamiento']);
        return view('InformeVer', compact('cita'));
    }

    public function edit(Cita $cita)
    {
        $this->autorizarCita($cita);
        $cita->load(['medico', 'paciente', 'enfermedad', 'tratamiento']);
        return view('InformeEditar', compact('cita'));
    }

    public function store(Request $request, Cita $cita)
    {
        $this->autorizarCita($cita);

        $request->validate([
            'enfermedad'  => 'required|string',
            'tratamiento' => 'required|string',
        ]);

        Enfermedad::updateOrCreate(['cita_id' => $cita->id], ['descripcion' => $request->enfermedad]);
        Tratamiento::updateOrCreate(['cita_id' => $cita->id], ['descripcion' => $request->tratamiento]);

        $cita->estado = 'Finalizada';
        $cita->save();
        $cita->load(['medico', 'paciente']);

        $nombreMedico = $cita->medico->name . ' ' . $cita->medico->Apellidos;
        $urlVer = '/Informe/' . $cita->id . '/ver';

        NotificacionHelper::enviar($cita, $cita->medico_id, 'Informe generado', 'El informe médico fue generado exitosamente', 'success', $urlVer);
        NotificacionHelper::enviar($cita, $cita->paciente_id, 'Informe disponible', "El Dr. {$nombreMedico} generó tu informe médico", 'success', $urlVer);
        CorreoHelper::informeGenerado($cita, false);

        return redirect('/citas')->with('success', 'Informe guardado correctamente');
    }

    public function update(Request $request, Cita $cita)
    {
        $this->autorizarCita($cita);

        $request->validate([
            'enfermedad'  => 'required|string',
            'tratamiento' => 'required|string',
        ]);

        Enfermedad::updateOrCreate(['cita_id' => $cita->id], ['descripcion' => $request->enfermedad]);
        Tratamiento::updateOrCreate(['cita_id' => $cita->id], ['descripcion' => $request->tratamiento]);

        $cita->estado = 'Finalizada';
        $cita->save();
        $cita->load(['medico', 'paciente']);

        $nombreMedico = $cita->medico->name . ' ' . $cita->medico->Apellidos;
        $urlVer = '/Informe/' . $cita->id . '/ver';

        NotificacionHelper::enviar($cita, $cita->medico_id, 'Informe actualizado', 'El informe médico fue actualizado', 'warning', $urlVer);
        NotificacionHelper::enviar($cita, $cita->paciente_id, 'Informe actualizado', "El Dr. {$nombreMedico} actualizó tu informe médico", 'warning', $urlVer);
        CorreoHelper::informeGenerado($cita, true);

        return redirect('/citas')->with('success', 'Informe actualizado correctamente');
    }

    public function pdf(Cita $cita)
    {
        // Paciente solo puede ver su propio PDF
        if (session('cargo') === 'Paciente' && $cita->paciente_id !== session('user_id')) {
            abort(403);
        }

        $cita->load(['medico', 'paciente', 'enfermedad', 'tratamiento']);
        $pdf = Pdf::loadView('PDF.PDFinforme', compact('cita'))->setPaper('a4', 'portrait');
        return $pdf->download('Informe_Cita_' . $cita->id . '.pdf');
    }

    public function enviarPorEmail(Request $request)
    {
        $request->validate([
            'cita_id' => 'required|exists:citas,id',
            'correo'  => 'required|email',
        ]);

        $cita = Cita::with(['medico', 'paciente', 'enfermedad', 'tratamiento'])->findOrFail($request->cita_id);
        $this->autorizarCita($cita);

        $pdf = Pdf::loadView('emails.EmailPDF', compact('cita'));
        Mail::send('emails.EmailPDF', compact('cita'), function ($message) use ($request, $pdf) {
            $message->to($request->correo)->subject('Informe Médico')
                ->attachData($pdf->output(), 'informe_medico.pdf');
        });

        return response()->json(['ok' => true]);
    }

    /**
     * Verifica que el admin tenga acceso a la cita (mismo centro).
     */
    private function autorizarCita(Cita $cita): void
    {
        if (session('cargo') === 'Admin') {
            $centroId  = session('centro_medico_id');
            $medicoIds = $centroId
                ? User::where('centro_medico_id', $centroId)
                    ->whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Medico'))
                    ->pluck('id')->toArray()
                : [];

            if (!in_array($cita->medico_id, $medicoIds)) {
                abort(403, 'No autorizado');
            }
        }
    }
}