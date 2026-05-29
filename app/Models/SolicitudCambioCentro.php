<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudCambioCentro extends Model
{
    protected $table = 'solicitudes_cambio_centro';

    protected $fillable = [
        'solicitante_id',
        'paciente_id',
        'centro_actual_id',
        'centro_solicitado_id',
        'asunto',
        'motivo',
        'prioridad',
        'estado',
        'nota_superadmin',
        'gestionado_por',
        'gestionado_en',
    ];

    protected $casts = [
        'gestionado_en' => 'datetime',
    ];

    // ── Relaciones ────────────────────────────────────────────────────────

    public function solicitante()
    {
        return $this->belongsTo(User::class, 'solicitante_id');
    }

    public function paciente()
    {
        return $this->belongsTo(User::class, 'paciente_id');
    }

    public function centroActual()
    {
        return $this->belongsTo(CentroMedico::class, 'centro_actual_id');
    }

    public function centroSolicitado()
    {
        return $this->belongsTo(CentroMedico::class, 'centro_solicitado_id');
    }

    public function archivos()
    {
        return $this->hasMany(SolicitudArchivo::class, 'solicitud_id');
    }

    public function gestorSuperadmin()
    {
        return $this->belongsTo(Superadmin::class, 'gestionado_por');
    }

    // ── Accessors de color ────────────────────────────────────────────────

    public function getPrioridadColorAttribute(): string
    {
        return match ($this->prioridad) {
            'alta'  => 'danger',
            'media' => 'warning',
            'baja'  => 'success',
            default => 'secondary',
        };
    }

    public function getEstadoColorAttribute(): string
    {
        return match ($this->estado) {
            'pendiente' => 'warning',
            'aceptada'  => 'success',
            'rechazada' => 'danger',
            default     => 'secondary',
        };
    }

    public function getEstadoIconAttribute(): string
    {
        return match ($this->estado) {
            'pendiente' => '⏳',
            'aceptada'  => '✅',
            'rechazada' => '❌',
            default     => '❓',
        };
    }
}