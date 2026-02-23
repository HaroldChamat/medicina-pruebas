<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cita extends Model
{
    //

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'Rut');
        return $this->belongsTo(User::class, 'Rut');
    }


    protected $casts = [
        'Fecha_y_hora' => 'date:Y-m-d'
    ];

    protected $fillable = [
        'Fecha_y_hora',
        'estado',
        'medico_id',
        'paciente_id'
    ];

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


}
