<?php

namespace App\Models;
use App\Models\Cita;

use Illuminate\Database\Eloquent\Model;

class Enfermedad extends Model
{
    protected $table = 'enfermedades';

    protected $fillable = ['cita_id', 'descripcion'];

    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }
}
