<?php
// app/Models/Entrada.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entrada extends Model
{
    protected $table = 'entradas';
    
    protected $fillable = [
        'data_entrada', 'fornecedor_id', 'lote_id', 'numero_lote_fornecedor', 
        'quantidade_informada', 'unidade', 'unidades_por_embalagem', 'estado', 'observacao'
    ];
    
    // 🟢 CORREÇÃO FINAL DOS TIMESTAMPS: Desabilita timestamps para esta tabela
    public $timestamps = false; 
}