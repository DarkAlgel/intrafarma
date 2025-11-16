<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicamento extends Model
{
    use HasFactory;

    /**
     * O nome da tabela associada ao model.
     *
     * @var string
     */
    protected $table = 'medicamentos';

    // =================================================================
    // A CORREÇÃO ESTÁ AQUI
    // Esta linha diz ao Laravel para NÃO tentar gerenciar
    // as colunas 'created_at' e 'updated_at', que não existem
    // no seu schema.
    // =================================================================
    public $timestamps = false;

    /**
     * Os atributos que podem ser atribuídos em massa.
     *
     * @var array<int, string>
     */
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
        'ativo',
        // 'serial_por_classe' é gerenciado pelo Trigger do banco,
        // por isso não está no $fillable.
    ];

    /**
     * Define a relação com Laboratorio.
     */
    public function laboratorio()
    {
        return $this->belongsTo(Laboratorio::class);
    }

    /**
     * Define a relação com ClasseTerapeutica.
     */
    public function classeTerapeutica()
    {
        // Especifica a chave estrangeira, já que o nome do método
        // é diferente do padrão (que seria 'classe_terapeutica')
        return $this->belongsTo(ClasseTerapeutica::class, 'classe_terapeutica_id');
    }

    /**
     * Define a relação com Lotes.
     */
    public function lotes()
    {
        return $this->hasMany(Lote::class); // Você precisará criar o Model 'Lote'
    }
}