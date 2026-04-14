<?php

namespace App\Models;
namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail; // Importante
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable;

    protected $table = 'usuarios';
    
    protected $fillable = ['id_persona', 'email', 'pass', 'admin', 'email_verified_at' ];

    public function getAuthPassword()
    {
        return $this->pass;
    }
    const UPDATED_AT = 'updated_at'; 
    const CREATED_AT = 'created_at';
}