<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mensaje extends Model
{
    // Indicamos el nombre exacto de tu tabla
    protected $table = 'mensajes';

    // Permitimos la asignación masiva de estos campos
    protected $fillable = [
        'remitente',
        'destinatario',
        'id_depaA',
        'id_depaB',
        'mensaje',
        'fecha'
    ];
}