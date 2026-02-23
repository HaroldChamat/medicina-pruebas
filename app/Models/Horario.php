<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Paciente;

class Horario extends Model
{
    protected $fillable = [
        'medico_id',
        'hora_inicio',
        'hora_fin',
        'almuerzo_inicio',
        'almuerzo_fin',
        'hora_atencion'
    ];

    public function users()
    {
        return $this->belongsTo(User::class);
    }
}
