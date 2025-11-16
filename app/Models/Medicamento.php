<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medicamento extends Model
{
    protected $table = 'medicamentos';
    public $timestamps = false; // Assumindo que não usa timestamps

    protected $fillable = [
        'codigo',
        'nome',
        'laboratorio_id',
        'classe_terapeutica_id',
        'tarja',
        'forma_retirada',
        'forma_fisica',
        'apresentacao',
        'unidade_base',
        'dosagem_valor',
        'dosagem_unidade',
        'generico',
        'limite_minimo',
        'serial_por_classe',
        'ativo'
    ];

    /**
     * Define o relacionamento com o Laboratório (FK: laboratorio_id).
     */
    public function laboratorio()
    {
        // ⭐️ CORREÇÃO PARA O ERRO: Adicionando o relacionamento
        return $this->belongsTo(Laboratorio::class);
    }

    /**
     * Define o relacionamento com a Classe Terapêutica (FK: classe_terapeutica_id).
     */
    public function classeTerapeutica()
    {
        // ⭐️ CORREÇÃO PARA O ERRO: Adicionando o relacionamento
        return $this->belongsTo(ClasseTerapeutica::class);
    }
    
    // NOTA: Para funcionar, os Models App\Models\Laboratorio.php e App\Models\ClasseTerapeutica.php precisam existir.
}