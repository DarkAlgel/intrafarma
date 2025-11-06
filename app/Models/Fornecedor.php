<?php
// app/Models/Fornecedor.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fornecedor extends Model
{
    protected $table = 'fornecedores';
    protected $guarded = ['id'];
    
    // 🟢 CORREÇÃO: Desabilita timestamps para esta tabela
    public $timestamps = false; 
}