<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SolicitudArchivo extends Model
{
    protected $table = 'solicitud_archivos';

    protected $fillable = [
        'solicitud_id',
        'emisor_id',
        'nombre_original',
        'ruta',
        'mime_type',
    ];

    public function solicitud()
    {
        return $this->belongsTo(SolicitudCambioCentro::class, 'solicitud_id');
    }

    public function emisor()
    {
        return $this->belongsTo(User::class, 'emisor_id');
    }
}