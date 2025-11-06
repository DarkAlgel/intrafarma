<?php
// app/Models/Medicamento.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicamento extends Model
{
    protected $table = 'medicamentos';
    protected $guarded = ['id'];
    
    // 🟢 CORREÇÃO FINAL: Desabilita timestamps para esta tabela
    public $timestamps = false; 
}