<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'usuarios';
    
    // Si tu tabla usa un ID diferente a 'id', ponlo aquí (ejemplo: 'id_usuario')
    // protected $primaryKey = 'id_usuario'; 

    protected $fillable = [
        'id_persona',
        'email',
        'pass',
        'admin',
    ];

    protected $hidden = [
        'pass',
        'remember_token',
    ];

    // INDISPENSABLE: Indica que 'pass' debe tratarse como el password oficial
    public function getAuthPassword()
    {
        return $this->pass;
    }

    // Opcional pero recomendado: para que Laravel sepa que 'pass' está encriptado
    protected $casts = [
        'admin' => 'boolean',
        'pass' => 'hashed', 
    ];
}