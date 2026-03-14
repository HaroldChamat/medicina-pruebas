<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    protected $table = 'mensajes';

    protected $fillable = [
        'cita_id',
        'emisor_id',
        'receptor_id',
        'contenido',
        'leido',
    ];

    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }

    public function emisor()
    {
        return $this->belongsTo(User::class, 'emisor_id');
    }

    public function receptor()
    {
        return $this->belongsTo(User::class, 'receptor_id');
    }
}