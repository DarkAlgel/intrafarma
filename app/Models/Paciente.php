<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    protected $table = 'pacientes';
    public $timestamps = false; // a tabela não tem created_at/updated_at

    protected $fillable = [
        'nome',
        'cpf',
        'telefone',
        'cidade'
    ];
}