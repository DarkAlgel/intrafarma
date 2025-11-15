<?php
// app/Models/Fornecedor.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fornecedor extends Model
{
    protected $table = 'fornecedores';
    
    // Permitir que esses campos sejam preenchidos em massa
    protected $fillable = ['nome', 'tipo', 'contato'];
    
    // Desabilita timestamps, conforme seu dicionário de dados
    public $timestamps = false; 
}