<?php

namespace App\Models;
use App\Models\User;

use Illuminate\Database\Eloquent\Model;

class Especialidad extends Model
{
    //

    protected $fillable = [
        'Nombre_especialidad'
    ];

    public function medico()
    {
        return $this->belongsTo(User::class, 'medico_id');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }


}
