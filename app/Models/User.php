<?php

namespace App\Models;

use App\Models\Cargo;
use App\Models\Especialidad;
use App\Models\Horario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
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
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'admin' => 'integer',
        'activo' => 'integer',
    ];
    
    protected $hidden = [
        'password',
    ];
    

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

    public function scopeActivos($query)
    {
        return $query->where('activo', 1);
    }
}
