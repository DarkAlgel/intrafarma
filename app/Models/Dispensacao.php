<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispensacao extends Model
{
    protected $table = 'dispensacoes';
    public $timestamps = false;

    protected $fillable = [
        'data_dispensa',
        'responsavel',
        'paciente_id',
        'lote_id',
        'dosagem',
        'nome_comercial',
        'quantidade_informada',
        'quantidade_base',
        'unidade',
        'numero_receita',
    ];
}