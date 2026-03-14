<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketMensaje extends Model
{
    protected $table = 'ticket_mensajes';

    protected $fillable = ['ticket_id', 'emisor_id', 'contenido', 'leido'];

    public function emisor()
    {
        return $this->belongsTo(User::class, 'emisor_id');
    }

    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }
}