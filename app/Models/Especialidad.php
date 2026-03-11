<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Especialidad extends Model
{
    protected $table = 'especialidads';

    protected $fillable = [
        'Nombre_especialidad'
    ];

    public function medicos()
    {
        return $this->belongsToMany(
            User::class, 
            'medico_especialidad', 
            'especialidad_id', 
            'medico_id'
        );
    }
}
