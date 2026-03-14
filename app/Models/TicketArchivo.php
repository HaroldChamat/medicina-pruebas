<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketArchivo extends Model
{
    protected $table = 'ticket_archivos';

    protected $fillable = ['ticket_id', 'emisor_id', 'nombre_original', 'ruta', 'mime_type'];

    public function emisor()
    {
        return $this->belongsTo(User::class, 'emisor_id');
    }
}