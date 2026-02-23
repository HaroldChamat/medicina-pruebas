<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Cita;
use App\Models\Horario;
use Carbon\Carbon;

class CitaSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('enfermedades')->truncate();
        DB::table('tratamientos')->truncate();
        DB::table('citas')->truncate();

        $medicos   = User::whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Medico'))->get();
        $pacientes = User::whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Paciente'))->get();

        $citasCreadas = 0;

        foreach ($medicos as $medico) {
            $horario = Horario::where('medico_id', $medico->id)->first();

            if (!$horario) {
                continue;
            }

            $duracion = $horario->hora_atencion;

            // ── Citas FINALIZADAS (semana pasada) ──────────────────────
            // Sirven para probar el historial médico
            $diasPasados = [
                Carbon::now()->subDays(7),
                Carbon::now()->subDays(5),
            ];

            foreach ($diasPasados as $i => $dia) {
                $paciente = $pacientes[$citasCreadas % $pacientes->count()];
                $hora     = Carbon::parse($dia->format('Y-m-d') . ' ' . $horario->hora_inicio)
                                  ->addMinutes($duracion * $i);

                Cita::create([
                    'medico_id'   => $medico->id,
                    'paciente_id' => $paciente->id,
                    'Fecha_y_hora'=> $hora,
                    'estado'      => 'Finalizada',
                ]);

                $citasCreadas++;
            }

            // ── Citas PENDIENTES (próximos días) ───────────────────────
            // Sirven para probar el listado principal y las notificaciones
            $diasFuturos = [
                Carbon::now()->addDays(1),
                Carbon::now()->addDays(2),
                Carbon::now()->addDays(3),
            ];

            foreach ($diasFuturos as $i => $dia) {
                $paciente = $pacientes[$citasCreadas % $pacientes->count()];
                $hora     = Carbon::parse($dia->format('Y-m-d') . ' ' . $horario->hora_inicio)
                                  ->addMinutes($duracion * $i);

                Cita::create([
                    'medico_id'   => $medico->id,
                    'paciente_id' => $paciente->id,
                    'Fecha_y_hora'=> $hora,
                    'estado'      => 'Pendiente',
                ]);

                $citasCreadas++;
            }

            // ── Cita CANCELADA (ayer) ──────────────────────────────────
            // Para probar que no aparece en el flujo normal
            $paciente = $pacientes[$citasCreadas % $pacientes->count()];
            $hora     = Carbon::parse(
                            Carbon::now()->subDay()->format('Y-m-d') . ' ' . $horario->hora_inicio
                        );

            Cita::create([
                'medico_id'   => $medico->id,
                'paciente_id' => $paciente->id,
                'Fecha_y_hora'=> $hora,
                'estado'      => 'Cancelada',
            ]);

            $citasCreadas++;
        }

        $this->command->info("✅ Citas creadas: {$citasCreadas} en total");
        $this->command->info('   → Finalizadas:  ' . Cita::where('estado', 'Finalizada')->count());
        $this->command->info('   → Pendientes:   ' . Cita::where('estado', 'Pendiente')->count());
        $this->command->info('   → Canceladas:   ' . Cita::where('estado', 'Cancelada')->count());
    }
}