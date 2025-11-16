<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    protected $table = 'usuarios';
    public $timestamps = false;

    protected $fillable = [
        'nome',
        'celular',
        'email',
        'login',
        'senha_hash',
        'datacadastro',
        'ultimoacesso',
        'ativo',
    ];

    public function getAuthPassword()
    {
        return $this->senha_hash;
    }

    public function getNameAttribute()
    {
        return $this->attributes['nome'] ?? null;
    }
}