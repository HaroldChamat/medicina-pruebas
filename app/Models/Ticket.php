<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'medico_id', 'admin_id', 'cita_id',
        'asunto', 'descripcion', 'prioridad', 'estado', 'tomado_en',
    ];

    protected $casts = [
        'tomado_en' => 'datetime',
    ];

    public function medico()
    {
        return $this->belongsTo(User::class, 'medico_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function cita()
    {
        return $this->belongsTo(Cita::class);
    }

    public function mensajes()
    {
        return $this->hasMany(TicketMensaje::class);
    }

    public function archivos()
    {
        return $this->hasMany(TicketArchivo::class);
    }

    public function getPrioridadColorAttribute(): string
    {
        return match($this->prioridad) {
            'alta'  => 'danger',
            'media' => 'warning',
            'baja'  => 'success',
        };
    }

    public function getEstadoColorAttribute(): string
    {
        return match($this->estado) {
            'abierto'     => 'primary',
            'en_progreso' => 'warning',
            'cerrado'     => 'secondary',
        };
    }
}