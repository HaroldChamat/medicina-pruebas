<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cita;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class EnviarRecordatorioCitas extends Command
{
    protected $signature = 'citas:recordatorio';
    protected $description = 'Envía recordatorio por correo 48 horas antes de la cita';

    public function handle()
    {
        $inicio = Carbon::now()->addHours(48)->startOfHour();
        $fin    = Carbon::now()->addHours(48)->endOfHour();

        $citas = Cita::with(['medico', 'paciente', 'enfermedad', 'tratamiento'])
            ->whereBetween('Fecha_y_hora', [$inicio, $fin])
            ->where('estado', 'Pendiente')
            ->get();

        foreach ($citas as $cita) {

            // generar PDF
            $pdf = Pdf::loadView('emails.EmailPDF', compact('cita'));

            Mail::send('emails.RecordatorioCita', compact('cita'), function ($message) use ($cita, $pdf) {
                $message->to($cita->paciente->email)
                        ->subject('⏰ Recordatorio de cita médica')
                        ->attachData(
                            $pdf->output(),
                            'recordatorio_cita.pdf'
                        );
            });
        }

        $this->info('Recordatorios enviados correctamente');
    }
}
