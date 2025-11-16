<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMedicamentoRequest extends FormRequest
{
    /**
     * Determina se o usuário está autorizado a fazer este request.
     */
    public function authorize(): bool
    {
        // Altere para false se precisar de lógica de autorização específica
        return true;
    }

    /**
     * Retorna as regras de validação que se aplicam ao request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        // Valores permitidos para os ENUMs (conforme seu schema)
        $tarjaTipos = ['sem_tarja', 'tarja_amarela', 'tarja_vermelha', 'tarja_preta'];
        $formaRetiradaTipos = ['MIP', 'com_prescricao'];
        $formaFisicaTipos = ['solida', 'pastosa', 'liquida', 'gasosa'];
        $unidadeContagemTipos = [
            'comprimido', 'capsula', 'dragea', 'sache', 'ampola', 'frasco', 
            'caixa', 'ml', 'g', 'unidade', 'aerosol', 'xarope', 'solucao'
        ];

        return [
            'codigo' => [
                'required',
                'string',
                'max:255',
                // Garante que o código é único. 
                // No update, ignora o ID do medicamento atual.
                Rule::unique('medicamentos')->ignore($this->route('medicamento')),
            ],
            'nome' => ['required', 'string', 'max:255'],
            'laboratorio_id' => ['required', 'integer', 'exists:laboratorios,id'],
            'classe_terapeutica_id' => ['required', 'integer', 'exists:classes_terapeuticas,id'],
            'tarja' => ['required', Rule::in($tarjaTipos)],
            'forma_retirada' => ['required', Rule::in($formaRetiradaTipos)],
            'forma_fisica' => ['required', Rule::in($formaFisicaTipos)],
            'apresentacao' => ['required', Rule::in($unidadeContagemTipos)],
            'unidade_base' => ['required', Rule::in($unidadeContagemTipos)],
            'dosagem_valor' => ['required', 'numeric', 'min:0'],
            'dosagem_unidade' => ['required', 'string', 'max:50'], // Ajuste o max se necessário
            'limite_minimo' => ['required', 'numeric', 'min:0'],

            // Para campos boolean (checkboxes), é ideal que o HTML
            // envie '1' (marcado) ou '0' (desmarcado).
            'generico' => ['required', 'boolean'],
            'ativo' => ['required', 'boolean'],
        ];
    }
}