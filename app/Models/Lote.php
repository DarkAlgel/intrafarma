<?php
// app/Models/Lote.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    protected $table = 'lotes';
    public $timestamps = false; 

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'medicamento_id',
        'data_fabricacao',
        'validade',
        'nome_comercial',
        'observacao',
        'ativo',
    ];

    /**
     * Retorna o medicamento associado a este lote.
     * Necessário para buscar o nome e dosagem na Dispensação.
     */
    public function medicamento()
    {
        // A tabela 'lotes' tem a chave estrangeira 'medicamento_id'
        return $this->belongsTo(Medicamento::class);
    }
}