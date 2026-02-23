<?php

namespace App\Models;
use App\Models\Cargo;
use App\Models\Especialidad;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
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
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected $casts = [
        'admin' => 'integer',
    ];

    public function cargo()
    {
        return $this->belongsTo(Cargo::class, 'id_cargo');
    }

    public function horario()
    {
        return $this->hasOne(Horario::class, 'medico_id');
    }

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class);
    }

}
