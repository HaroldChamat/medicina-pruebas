<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cita;
use App\Models\Enfermedad;
use App\Models\Tratamiento;

class InformeSeeder extends Seeder
{
    public function run(): void
    {
        // Solo aplica a citas Finalizadas que no tengan informe aún
        $citasFinalizadas = Cita::where('estado', 'Finalizada')
            ->doesntHave('enfermedad')
            ->get();

        // Datos de ejemplo clínicamente coherentes
        $informes = [
            [
                'enfermedad'   => 'Hipertensión arterial leve. Presión sistólica 145/90 mmHg.',
                'tratamiento'  => 'Enalapril 10mg cada 12 horas. Control en 30 días.',
            ],
            [
                'enfermedad'   => 'Faringitis aguda bacteriana. Presencia de exudado en amígdalas.',
                'tratamiento'  => 'Amoxicilina 500mg cada 8 horas por 7 días. Reposo relativo.',
            ],
            [
                'enfermedad'   => 'Lumbalgia mecánica. Contractura muscular en región lumbar.',
                'tratamiento'  => 'Ibuprofeno 400mg cada 8 horas. Fisioterapia 2 veces por semana.',
            ],
            [
                'enfermedad'   => 'Gastritis aguda. Dolor epigástrico y náuseas.',
                'tratamiento'  => 'Omeprazol 20mg en ayunas por 14 días. Dieta blanda.',
            ],
            [
                'enfermedad'   => 'Otitis media aguda izquierda. Conducto auditivo inflamado.',
                'tratamiento'  => 'Amoxicilina-clavulánico 875mg cada 12 horas. Analgésicos SOS.',
            ],
            [
                'enfermedad'   => 'Diabetes tipo 2 controlada. HbA1c 7.2%.',
                'tratamiento'  => 'Metformina 850mg con desayuno y cena. Dieta hipocalórica.',
            ],
            [
                'enfermedad'   => 'Conjuntivitis bacteriana bilateral. Secreción purulenta.',
                'tratamiento'  => 'Tobramicina colirio 3 gotas cada 6 horas por 5 días.',
            ],
            [
                'enfermedad'   => 'Esguince de tobillo derecho grado I. Leve edema periarticular.',
                'tratamiento'  => 'Reposo, hielo local 20 min cada 4 horas. Vendaje funcional.',
            ],
        ];

        foreach ($citasFinalizadas as $index => $cita) {
            $informe = $informes[$index % count($informes)];

            Enfermedad::create([
                'cita_id'     => $cita->id,
                'descripcion' => $informe['enfermedad'],
            ]);

            Tratamiento::create([
                'cita_id'     => $cita->id,
                'descripcion' => $informe['tratamiento'],
            ]);
        }

        $this->command->info('✅ Informes médicos creados: ' . $citasFinalizadas->count());
        $this->command->info('   → Cada cita Finalizada tiene ahora enfermedad y tratamiento');
    }
}