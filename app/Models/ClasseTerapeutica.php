<?php
// app/Models/ClasseTerapeutica.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClasseTerapeutica extends Model
{
    protected $table = 'classes_terapeuticas';
    protected $fillable = ['codigo_classe', 'nome'];
    
    // 🟢 CORREÇÃO: Desabilita timestamps
    public $timestamps = false; 
}