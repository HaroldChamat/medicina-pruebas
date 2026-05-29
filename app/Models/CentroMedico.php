<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CentroMedico extends Model
{
    protected $table = 'centro_medico';

    protected $fillable = [
        'nombre',
        'direccion',
    ];

    /**
     * Usuarios (médicos/pacientes/admin) asociados a este centro.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'centro_medico_id');
    }

    /**
     * Solo los médicos de este centro.
     */
    public function medicos()
    {
        return $this->hasMany(User::class, 'centro_medico_id')
            ->whereHas('cargo', fn($q) => $q->where('Nombre_cargo', 'Medico'));
    }
}