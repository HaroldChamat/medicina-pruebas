<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'Rut');
    }

    protected $casts = [
        'Fecha_y_hora' => 'datetime',
    ];

    protected $fillable = [
        'Fecha_y_hora',
        'estado',
        'medico_id',
        'paciente_id',
        'prestacion_id',
        'codigo_cita',
    ];

    protected static function booted(): void
    {
        static::creating(function ($cita) {
            do {
                $codigo = 'CIT-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
            } while (self::where('codigo_cita', $codigo)->exists());

            $cita->codigo_cita = $codigo;
        });
    }

    public function medico()
    {
        return $this->belongsTo(User::class, 'medico_id');
    }

    public function paciente()
    {
        return $this->belongsTo(User::class, 'paciente_id');
    }

    public function enfermedad()
    {
        return $this->hasOne(Enfermedad::class);
    }

    public function tratamiento()
    {
        return $this->hasOne(Tratamiento::class);
    }

    public function prestacion()
    {
        return $this->belongsTo(Prestacion::class);
    }
}