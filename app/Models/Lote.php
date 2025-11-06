<?php
// app/Models/Lote.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    protected $table = 'lotes';
    
    protected $fillable = [
        'medicamento_id', 
        'data_fabricacao', 
        'validade', 
        'nome_comercial', 
        'ativo', 
        'observacao'
    ];
    
    protected $dates = [
        'data_fabricacao', 
        'validade'
    ];
    
    // 🟢 CORREÇÃO FINAL: Desabilita timestamps para esta tabela
    public $timestamps = false; 
}