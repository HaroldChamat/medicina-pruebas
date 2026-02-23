<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita;
use App\Models\Enfermedad;
use App\Models\Tratamiento;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;

class InformeController extends Controller
{
    public function index()
    {
        return view('Informe', [
            'Citas' => Cita::with(['medico', 'paciente', 'enfermedad', 'tratamiento'])->get(),
        ]);
    }

    public function index_paciente()
    {
        $citas = Cita::with([
            'medico',
            'paciente',
            'enfermedad',
            'tratamiento'
        ])
        ->whereHas('enfermedad')
        ->whereHas('tratamiento')
        ->get();

        $medicos = User::where('id_cargo', 2)->get();

        return view('Informacion', compact('citas', 'medicos'));
    }

    public function create(Cita $cita)
    {
        return view('Informe', compact('cita'));
    }

    public function store(Request $request, Cita $cita)
    {
        $request->validate([
            'enfermedad' => 'required|string',
            'tratamiento' => 'required|string',
        ]);

        Enfermedad::updateOrCreate(
            ['cita_id' => $cita->id],
            ['descripcion' => $request->enfermedad]
        );

        Tratamiento::updateOrCreate(
            ['cita_id' => $cita->id],
            ['descripcion' => $request->tratamiento]
        );

        return redirect('/citas')->with('success', 'Informe guardado correctamente');
    }

    public function pdf(Cita $cita)
    {
        $cita->load([
            'medico',
            'paciente',
            'enfermedad',
            'tratamiento'
        ]);

        $pdf = Pdf::loadView('PDF.PDFinforme', compact('cita'))->setPaper('a4', 'portrait');

        return $pdf->download(
            'Informe_Cita_' . $cita->id . '.pdf'
        );
    }

    public function enviarPorEmail(Request $request)
    {
        $request->validate([
            'cita_id' => 'required|exists:citas,id',
            'correo'  => 'required|email'
        ]);

        $cita = Cita::with(['medico','paciente','enfermedad','tratamiento'])
                    ->findOrFail($request->cita_id);

        // generar PDF
        $pdf = Pdf::loadView('emails.EmailPDF', compact('cita'));

        Mail::send('emails.EmailPDF', compact('cita'), function ($message) use ($request, $pdf) {
            $message->to($request->correo)
                    ->subject('Informe Médico')
                    ->attachData(
                        $pdf->output(),
                        'informe_medico.pdf'
                    );
        });

        return response()->json(['ok' => true]);
    }
}
