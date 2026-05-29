<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Superadmin extends Authenticatable
{
    protected $table = 'superadmins';

    protected $fillable = [
        'name',
        'Apellidos',
        'email',
        'Rut',
        'telefono',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    /**
     * Centros médicos creados por este superadmin.
     */
    public function centrosMedicos()
    {
        return $this->hasMany(CentroMedico::class, 'creado_por_superadmin_id');
    }

    /**
     * Admins (usuarios con admin=1) creados por este superadmin.
     */
    public function admins()
    {
        return $this->hasMany(User::class, 'creado_por_superadmin_id')
            ->where('admin', 1);
    }
}