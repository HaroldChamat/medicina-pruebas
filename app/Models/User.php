<?php

namespace App\Models;

use App\Models\Cargo;
use App\Models\Especialidad;
use App\Models\Horario;
use App\Models\CentroMedico;
use App\Models\Prestacion;
use App\Models\MedicoPrestacion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'Apellidos',
        'email',
        'Rut',
        'telefono',
        'id_cargo',
        'admin',
        'activo',
        'password',
        'centro_medico_id',  // ← nuevo
    ];

    protected $casts = [
        'admin'  => 'integer',
        'activo' => 'integer',
    ];

    protected $hidden = [
        'password',
    ];

    // ── Relaciones existentes ────────────────────────────────────────────

    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'id_cargo');
    }

    public function horario()
    {
        return $this->hasOne(Horario::class, 'medico_id');
    }

    public function especialidades()
    {
        return $this->belongsToMany(
            Especialidad::class,
            'medico_especialidad',
            'medico_id',
            'especialidad_id'
        );
    }

    // ── Nuevas relaciones ────────────────────────────────────────────────

    /**
     * Centro médico al que pertenece el usuario.
     */
    public function centroMedico()
    {
        return $this->belongsTo(CentroMedico::class, 'centro_medico_id');
    }

    /**
     * Prestaciones que ofrece este médico (con datos del pivot).
     */
    public function prestaciones()
    {
        return $this->belongsToMany(
            Prestacion::class,
            'medico_prestaciones',
            'id_medico',
            'id_prestacion'
        )->withPivot([
            'id',
            'cantidad_horas',
            'hora_atencion',
            'hora_entrada',
            'hora_salida',
            'cant_online',
        ])->withTimestamps();
    }

    /**
     * Registros directos de medico_prestaciones para este médico.
     */
    public function medicoPrestaciones()
    {
        return $this->hasMany(MedicoPrestacion::class, 'id_medico');
    }

    // ── Scopes ───────────────────────────────────────────────────────────

    public function scopeActivos($query)
    {
        return $query->where('activo', 1);
    }
    public function citasPaciente()
    {
        return $this->hasMany(\App\Models\Cita::class, 'paciente_id');
    }
}