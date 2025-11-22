<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMedicamentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tarjaTipos = ['sem_tarja', 'tarja_amarela', 'tarja_vermelha', 'tarja_preta'];
        $formaRetiradaTipos = ['MIP', 'com_prescricao'];
        $formaFisicaTipos = ['solida', 'pastosa', 'liquida', 'gasosa'];

        return [
            'codigo' => [
                'required',
                'string',
                'max:255',
                Rule::unique('medicamentos')->ignore($this->route('medicamento')),
            ],

            'nome' => ['required', 'string', 'max:255'],
            'laboratorio_id' => ['required', 'integer', 'exists:laboratorios,id'],

            // 🔥 REMOVIDO DA VIEW — tornando opcional
            'classe_terapeutica_id' => ['nullable', 'integer', 'exists:classes_terapeuticas,id'],

            'tarja' => ['required', Rule::in($tarjaTipos)],
            'forma_retirada' => ['required', Rule::in($formaRetiradaTipos)],
            'forma_fisica' => ['required', Rule::in($formaFisicaTipos)],

            // 🔥 Você removeu do form — não pode ser required
            'apresentacao' => ['nullable', 'string'],
            'unidade_base' => ['nullable', 'string'],

            // 🔥 Também removido no form — tornar opcional
            'dosagem_valor' => ['nullable', 'numeric', 'min:0'],
            'dosagem_unidade' => ['nullable', 'string', 'max:50'],

            // 🔥 Removidos do formulário
            'limite_minimo' => ['nullable', 'numeric', 'min:0'],
            'ativo' => ['nullable', 'boolean'],

            // Genérico vira opcional — se não vier marcado, vira false
            'generico' => ['nullable', 'boolean'],
        ];
    }
}
