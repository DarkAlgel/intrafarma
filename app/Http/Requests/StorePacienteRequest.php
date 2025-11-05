<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

//Verifica se o cpf é valido antes de enviar para o banco
class StorePacienteRequest extends FormRequest
{
    public function rules()
    {
        return [
            'nome' => 'required|string|max:255',
            'cpf' => ['required', 'string', 'max:14', function($attribute, $value, $fail) {
                if (!$this->cpfValido($value)) {
                    $fail('O CPF informado não é válido.');
                }
            }],
            'telefone' => 'nullable|string|max:20',
            'cidade' => 'nullable|string|max:255',
        ];
    }

    private function cpfValido($cpf)
    {
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        if (strlen($cpf) != 11) return false;
        if (preg_match('/(\d)\1{10}/', $cpf)) return false;

        for ($t=9; $t<11; $t++) {
            $d = 0;
            for ($c=0; $c<$t; $c++) {
                $d += $cpf[$c] * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf[$t] != $d) return false;
        }

        return true;
    }
}
