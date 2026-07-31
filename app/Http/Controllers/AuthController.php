<?php

namespace App\Http\Controllers;

use App\Models\Filiado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // Mostrar tela de login
    public function showLogin()
    {
        if (session('membro_logado')) {
            return redirect()->route('feed');
        }
        return view('auth.login');
    }

    // Processar login
    public function login(Request $request)
    {
        $request->validate([
            'matricula' => 'required|numeric',
            'password' => 'required|string|min:6'
        ]);

        $filiado = Filiado::where('matricula', $request->matricula)->first();

        if ($filiado && Hash::check($request->password, $filiado->password)) {
            // Verificar status
            if (!in_array(strtolower($filiado->status), ['ativo', 'membro'])) {
                return back()->with('error', 'Sua conta está ' . $filiado->status . '.');
            }

            Session::put('membro_logado', $filiado->matricula);
            Session::put('membro_nome', $filiado->nome);
            Session::put('membro_funcao', $filiado->funcao);

            return redirect()->route('feed')->with('success', 'Bem-vindo(a) ' . $filiado->nome . '!');
        }

        return back()->with('error', 'Matrícula ou senha incorretos!');
    }

    // Mostrar tela de cadastro
    public function showRegister()
    {
        if (session('membro_logado')) {
            return redirect()->route('feed');
        }
        return view('auth.register');
    }

    // Processar cadastro
    public function register(Request $request)
    {
        // Funções que precisam de consagração
        $funcoesMinisteriais = ['Diacono', 'Presbitero', 'Pastor', 'Evangelista', 'Auxiliar', 'Obreiro'];
        
        // Regras de validação
        $rules = [
            'nome' => 'required|string|max:255',
            'email' => 'required|email|unique:filiado,email',
            'telefone' => 'required|string|max:20',
            'documento' => 'required|string|max:20|unique:filiado,documento',
            'dataNascimento' => 'required|date|before:today',
            'dataBatismo' => 'nullable|date',
            'endereco' => 'required|string|max:255',
            'cidade' => 'required|string|max:100',
            'uf' => 'required|string|max:2',
            'congregacao' => 'required|string|max:255',
            'funcao' => 'required|string|in:Membro,Auxiliar,Obreiro,Evangelista,Diacono,Presbitero,Pastor',
            'password' => 'required|string|min:6|confirmed',
            'terms' => 'accepted'
        ];

        // Se a função for ministerial, data_Consagracao é obrigatória
        if (in_array($request->funcao, $funcoesMinisteriais)) {
            $rules['data_Consagracao'] = 'required|date';
        } else {
            $rules['data_Consagracao'] = 'nullable|date';
        }

        // Mensagens de erro personalizadas
        $messages = [
            'nome.required' => 'O campo nome é obrigatório.',
            'nome.max' => 'O nome não pode ter mais de 255 caracteres.',
            'email.required' => 'O campo email é obrigatório.',
            'email.email' => 'Digite um email válido.',
            'email.unique' => 'Este email já está cadastrado.',
            'telefone.required' => 'O campo telefone é obrigatório.',
            'documento.required' => 'O campo documento é obrigatório.',
            'documento.unique' => 'Este documento já está cadastrado.',
            'dataNascimento.required' => 'A data de nascimento é obrigatória.',
            'dataNascimento.date' => 'A data de nascimento não é uma data válida.',
            'dataNascimento.before' => 'A data de nascimento deve ser anterior a hoje.',
            'dataBatismo.date' => 'A data de batismo não é uma data válida.',
            'endereco.required' => 'O campo endereço é obrigatório.',
            'cidade.required' => 'O campo cidade é obrigatório.',
            'uf.required' => 'O campo UF é obrigatório.',
            'uf.max' => 'UF deve ter 2 caracteres.',
            'congregacao.required' => 'O campo congregação é obrigatório.',
            'funcao.required' => 'O campo função é obrigatório.',
            'funcao.in' => 'Selecione uma função válida.',
            'data_Consagracao.required' => 'A data de consagração é obrigatória para funções ministeriais.',
            'data_Consagracao.date' => 'A data de consagração não é uma data válida.',
            'password.required' => 'A senha é obrigatória.',
            'password.min' => 'A senha deve ter no mínimo 6 caracteres.',
            'password.confirmed' => 'A confirmação da senha não corresponde.',
            'terms.accepted' => 'Você deve aceitar os termos de uso.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Gerar matrícula
        $ultimo = Filiado::orderBy('matricula', 'desc')->first();
        $matricula = $ultimo ? $ultimo->matricula + 1 : 1001;

        // Criar membro - COM TODOS OS CAMPOS
        $filiado = Filiado::create([
            'matricula' => $matricula,
            'nome' => $request->nome,
            'nome_carteira' => $request->nome,
            'email' => $request->email,
            'telefone' => $request->telefone,
            'documento' => $request->documento,
            'dataNascimento' => $request->dataNascimento,
            'dataBatismo' => $request->dataBatismo ?? null,
            'data_Consagracao' => $request->data_Consagracao ?? null,
            'data_Desligamento' => null,
            'logradouro' => $request->logradouro ?? '',
            'endereco' => $request->endereco,
            'numero' => $request->numero ?? 0,
            'bairro' => $request->bairro ?? '',
            'cep' => $request->cep ?? '',
            'cidade' => $request->cidade,
            'uf' => $request->uf,
            'congregacao' => $request->congregacao,
            'estadoCivil' => $request->estadoCivil ?? '',
            'mae' => $request->mae ?? '',
            'pai' => $request->pai ?? '',
            'arquivo' => $request->arquivo ?? '',
            'cartas' => $request->cartas ?? '', // CAMPO ADICIONADO
            'datCadastro' => now(),
            'funcao' => $request->funcao,
            'status' => 'ativo',
            'password' => Hash::make($request->password),
            'privacidade' => true
        ]);

        // Logar automaticamente
        Session::put('membro_logado', $filiado->matricula);
        Session::put('membro_nome', $filiado->nome);
        Session::put('membro_funcao', $filiado->funcao);

        return redirect()->route('feed')->with('success', 'Cadastro realizado! Bem-vindo(a)! 🎉');
    }

    // Logout
    public function logout()
    {
        Session::flush();
        return redirect()->route('login')->with('success', 'Você saiu com sucesso.');
    }
}