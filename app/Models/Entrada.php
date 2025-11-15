<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // Novo import necessário

class Entrada extends Model
{
    use HasFactory;
    public $timestamps = false;
    
    // Suas colunas permitidas para preenchimento em massa
    protected $fillable = [
        'data_entrada',
        'fornecedor_id',
        'lote_id',
        'numero_lote_fornecedor',
        'quantidade_informada',
        'quantidade_base',
        'unidade',
        'unidades_por_embalagem',
        'estado',
        'observacao',
    ];
    
    // Indica que o timestamp não é gerenciado pelo Laravel nesta tabela, se for o caso. 
    // Se você usa created_at e updated_at, remova a linha abaixo.
    // public $timestamps = false; 

    /**
     * Define o relacionamento BelongsTo com o Fornecedor.
     * Necessário para Entrada::with('fornecedor') no controller.
     */
    public function fornecedor(): BelongsTo
    {
        return $this->belongsTo(Fornecedor::class, 'fornecedor_id');
    }

    /**
     * Define o relacionamento BelongsTo com o Lote.
     */
    public function lote(): BelongsTo
    {
        return $this->belongsTo(Lote::class, 'lote_id');
    }
}