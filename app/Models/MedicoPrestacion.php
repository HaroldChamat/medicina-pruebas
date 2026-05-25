<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicoPrestacion extends Model
{
    protected $table = 'medico_prestaciones';

    protected $fillable = [
        'id_medico',
        'id_prestacion',
        'cantidad_horas',
        'hora_atencion',
        'hora_entrada',
        'hora_salida',
        'cant_online',
    ];

    protected $casts = [
        'hora_entrada' => 'string',
        'hora_salida'  => 'string',
    ];

    /**
     * El médico asociado a este registro.
     */
    public function medico()
    {
        return $this->belongsTo(User::class, 'id_medico');
    }

    /**
     * La prestación asociada a este registro.
     */
    public function prestacion()
    {
        return $this->belongsTo(Prestacion::class, 'id_prestacion');
    }

    /**
     * Genera los slots de hora disponibles para esta prestación.
     * Retorna array de strings con formato 'HH:mm'.
     */
    public function slotsDisponibles(): array
    {
        $slots  = [];
        $inicio = \Carbon\Carbon::createFromFormat('H:i:s', $this->hora_entrada);
        $fin    = \Carbon\Carbon::createFromFormat('H:i:s', $this->hora_salida);
        $dur    = (int) $this->hora_atencion; // minutos por paciente

        if ($dur <= 0) {
            return $slots;
        }

        while ($inicio->copy()->addMinutes($dur)->lte($fin)) {
            $slots[] = $inicio->format('H:i');
            $inicio->addMinutes($dur);
        }

        return $slots;
    }
}