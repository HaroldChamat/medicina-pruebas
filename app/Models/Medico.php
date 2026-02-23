<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medico extends Model
{
    //

    protected $fillable = [
        'Nombre',
        'Apellidos',
        'email',
        'Rut_medico',
        'telefono'
    ];

    public function horario()
    {
        return $this->hasOne(Horario::class);
    }

    public function especialidades()
    {
        return $this->hasOne(Especialidad::class, 'id_medico');
    }

    
}
