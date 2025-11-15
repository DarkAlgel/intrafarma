<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Novo import necessário

class Lote extends Model
{
    use HasFactory;
    public $timestamps = false;
    
    // Suas colunas permitidas para preenchimento em massa
    protected $fillable = [
        'medicamento_id',
        'data_fabricacao',
        'validade',
        'nome_comercial',
        'ativo',
        'observacao',
    ];

    /**
     * Define o relacionamento BelongsTo com o Modelo Medicamento.
     * Necessário para Lote::with('medicamento') no controller.
     */
    public function medicamento(): BelongsTo
    {
        // Assumimos que a chave estrangeira na tabela 'lotes' é 'medicamento_id'
        return $this->belongsTo(Medicamento::class, 'medicamento_id');
    }

    // Você pode adicionar mais relacionamentos ou métodos aqui conforme necessário...
    
}