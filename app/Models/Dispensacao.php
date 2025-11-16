<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispensacao extends Model
{
    protected $table = 'dispensacoes';
    public $timestamps = false; // Assumindo que a tabela não tem created_at/updated_at

    protected $fillable = [
        'data_dispensa',
        'responsavel',
        'paciente_id',
        'lote_id',
        'dosagem',
        'nome_comercial',
        'quantidade_informada',
        'quantidade_base',
        'unidade',
        'numero_receita',
    ];

    // ⭐️ RELACIONAMENTO 1: Retorna o paciente associado à dispensação. ⭐️
    public function paciente()
    {
        // A tabela dispensacoes tem a chave paciente_id
        return $this->belongsTo(Paciente::class);
    }

    // ⭐️ RELACIONAMENTO 2: Retorna o lote associado à dispensação. ⭐️
    public function lote()
    {
        // A tabela dispensacoes tem a chave lote_id
        return $this->belongsTo(Lote::class);
    }
    
    // NOTA: Certifique-se que no Model App\Models\Lote.php exista o relacionamento 'medicamento()'
    // para que Dispensacao::with('lote.medicamento') funcione.
}