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
        $cargo  = session('cargo');
        $userId = session('user_id');

        // Paciente: ve sus propios informes en una vista dedicada
        if ($cargo === 'Paciente') {
            $Citas = Cita::with(['medico', 'paciente', 'enfermedad', 'tratamiento'])
                ->where('paciente_id', $userId)
                ->whereHas('enfermedad')
                ->whereHas('tratamiento')
                ->get();

            return view('MisInformes', compact('Citas'));
        }

        // Admin: ve todos los informes
        return view('Informe', [
            'Citas' => Cita::with(['medico', 'paciente', 'enfermedad', 'tratamiento'])->get(),
        ]);
    }

    public function index_paciente()
    {
        $cargo   = session('cargo');
        $userId  = session('user_id');

        $query = Cita::with(['medico', 'paciente', 'enfermedad', 'tratamiento'])
            ->whereHas('enfermedad')
            ->whereHas('tratamiento');

        // Médico solo ve los informes de sus propias citas
        if ($cargo === 'Medico') {
            $query->where('medico_id', $userId);
        }

        $citas   = $query->get();
        $medicos = User::whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Medico'))->get();

        return view('Informacion', compact('citas', 'medicos'));
    }

    public function create(Cita $cita)
    {
        return view('Informe', compact('cita'));
    }

    public function store(Request $request, Cita $cita)
    {
        $request->validate([
            'enfermedad'  => 'required|string',
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
        $cita->load(['medico', 'paciente', 'enfermedad', 'tratamiento']);

        $pdf = Pdf::loadView('PDF.PDFinforme', compact('cita'))
                  ->setPaper('a4', 'portrait');

        return $pdf->download('Informe_Cita_' . $cita->id . '.pdf');
    }

    public function enviarPorEmail(Request $request)
    {
        $request->validate([
            'cita_id' => 'required|exists:citas,id',
            'correo'  => 'required|email',
        ]);

        $cita = Cita::with(['medico', 'paciente', 'enfermedad', 'tratamiento'])
                    ->findOrFail($request->cita_id);

        $pdf = Pdf::loadView('emails.EmailPDF', compact('cita'));

        Mail::send('emails.EmailPDF', compact('cita'), function ($message) use ($request, $pdf) {
            $message->to($request->correo)
                    ->subject('Informe Médico')
                    ->attachData($pdf->output(), 'informe_medico.pdf');
        });

        return response()->json(['ok' => true]);
    }
}