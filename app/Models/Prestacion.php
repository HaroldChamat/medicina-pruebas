<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prestacion extends Model
{
    protected $table = 'prestaciones';

    protected $fillable = [
        'nombre',
    ];

    /**
     * Médicos que ofrecen esta prestación (a través de medico_prestaciones).
     */
    public function medicos()
    {
        return $this->belongsToMany(
            User::class,
            'medico_prestaciones',
            'id_prestacion',
            'id_medico'
        )->withPivot([
            'cantidad_horas',
            'hora_atencion',
            'hora_entrada',
            'hora_salida',
            'cant_online',
        ])->withTimestamps();
    }

    /**
     * Registros directos de la tabla intermedia.
     */
    public function medicoPrestaciones()
    {
        return $this->hasMany(MedicoPrestacion::class, 'id_prestacion');
    }
}