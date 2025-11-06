<?php
// app/Models/Laboratorio.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laboratorio extends Model
{
    protected $table = 'laboratorios';
    protected $fillable = ['nome'];
    
    // 🟢 CORREÇÃO: Informa ao Laravel que esta tabela não tem created_at e updated_at
    public $timestamps = false; 
}