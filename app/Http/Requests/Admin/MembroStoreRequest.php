<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class MembroStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nome' => 'required|string|max:255',
            'nome_carteira' => 'nullable|string|max:100',
            'email' => 'required|email|unique:filiado,email',
            'documento' => 'required|string|max:20|unique:filiado,documento',
            'telefone' => 'required|string|max:20',
            'dataNascimento' => 'required|date|before:today',
            'estadoCivil' => 'nullable|string|max:50|in:Solteiro,Casado,Divorciado,Viúvo',
            'endereco' => 'required|string|max:255',
            'numero' => 'required|numeric|min:0',
            'bairro' => 'required|string|max:255',
            'cep' => 'required|string|max:20',
            'cidade' => 'required|string|max:255',
            'uf' => 'required|string|max:2|in:AC,AL,AP,AM,BA,CE,DF,ES,GO,MA,MT,MS,MG,PA,PB,PR,PE,PI,RJ,RN,RS,RO,RR,SC,SP,SE,TO',
            'congregacao' => 'required|string|max:255',
            'funcao' => 'required|string|max:255',
            'dataBatismo' => 'nullable|date|before_or_equal:today',
            'data_Consagracao' => 'nullable|date|before_or_equal:today',
            'mae' => 'nullable|string|max:255',
            'pai' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:500',
            'status' => 'required|string|in:Ativo,Inativo,Pendente,Transferido',
            'senha' => 'required|string|min:6|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'nome.required' => 'O nome completo é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.unique' => 'Este e-mail já está cadastrado.',
            'documento.required' => 'O documento é obrigatório.',
            'documento.unique' => 'Este documento já está cadastrado.',
            'telefone.required' => 'O telefone é obrigatório.',
            'dataNascimento.required' => 'A data de nascimento é obrigatória.',
            'dataNascimento.before' => 'A data de nascimento deve ser anterior a hoje.',
            'endereco.required' => 'O endereço é obrigatório.',
            'numero.required' => 'O número é obrigatório.',
            'bairro.required' => 'O bairro é obrigatório.',
            'cep.required' => 'O CEP é obrigatório.',
            'cidade.required' => 'A cidade é obrigatória.',
            'uf.required' => 'O estado é obrigatório.',
            'uf.in' => 'Selecione um estado válido.',
            'congregacao.required' => 'A congregação é obrigatória.',
            'funcao.required' => 'A função é obrigatória.',
            'status.required' => 'O status é obrigatório.',
            'senha.required' => 'A senha é obrigatória.',
            'senha.min' => 'A senha deve ter no mínimo 6 caracteres.',
            'senha.confirmed' => 'A confirmação da senha não coincide.',
        ];
    }
}