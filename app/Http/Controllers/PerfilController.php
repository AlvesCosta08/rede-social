<?php

namespace App\Http\Controllers;

use App\Models\Filiado;
use App\Models\Publicacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PerfilController extends Controller
{
    /**
     * Mostrar perfil de um membro
     */
    public function show($matricula)
    {
        $membroLogado = Auth::user();
        $perfil = Filiado::on('mysql')
            ->with(['publicacoes' => function($query) {
                $query->orderBy('created_at', 'desc')->limit(10);
            }])
            ->where('matricula', $matricula)
            ->firstOrFail();

        return view('perfil.show', compact('perfil', 'membroLogado'));
    }

    /**
     * Editar perfil do usuário logado
     */
    public function edit()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Sessão expirada.');
        }

        $perfil = Filiado::on('mysql')->where('matricula', $user->matricula)->first();
        
        if (!$perfil) {
            Auth::logout();
            Session::flush();
            return redirect()->route('login')->with('error', 'Membro não encontrado.');
        }

        return view('perfil.edit', compact('perfil'));
    }

    /**
     * Atualizar perfil
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Sessão expirada.');
        }

        $perfil = Filiado::on('mysql')->where('matricula', $user->matricula)->first();

        if (!$perfil) {
            return redirect()->route('login')->with('error', 'Membro não encontrado.');
        }

        // Validação
        $rules = [
            'nome' => 'required|string|max:255',
            'nome_carteira' => 'nullable|string|max:100',
            'telefone' => 'required|string|max:20',
            'dataNascimento' => 'required|date|before:today',
            'estadoCivil' => 'nullable|string|max:50',
            'endereco' => 'required|string|max:255',
            'numero' => 'required|numeric',
            'bairro' => 'required|string|max:255',
            'cep' => 'required|string|max:20',
            'cidade' => 'required|string|max:255',
            'uf' => 'required|string|max:2|min:2',
            'congregacao' => 'required|string|max:255',
            'funcao' => 'required|string|max:255',
            'dataBatismo' => 'nullable|date|before_or_equal:today',
            'data_Consagracao' => 'nullable|date|before_or_equal:today',
            'mae' => 'nullable|string|max:255',
            'pai' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:500',
            'privacidade' => 'nullable|boolean',
        ];

        // Validação de email
        if ($request->has('email') && $request->email !== $perfil->email) {
            $rules['email'] = 'required|email|max:255|unique:filiado,email';
        } elseif ($request->has('email') && $request->email == $perfil->email) {
            $rules['email'] = 'required|email|max:255';
        }

        // Validação de documento
        if ($request->has('documento') && $request->documento !== $perfil->documento) {
            $rules['documento'] = [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    $limpo = preg_replace('/[^0-9]/', '', $value);
                    if (strlen($limpo) < 11) {
                        $fail('Digite um documento válido (mínimo 11 dígitos).');
                    }
                },
                'unique:filiado,documento'
            ];
        } elseif ($request->has('documento') && $request->documento == $perfil->documento) {
            $rules['documento'] = 'required|string|max:20';
        }

        // Validação de senha
        if ($request->filled('new_password')) {
            $rules['current_password'] = 'required|string';
            $rules['new_password'] = 'required|string|min:8|confirmed';
        }

        $messages = [
            'nome.required' => 'O nome completo é obrigatório.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'Digite um e-mail válido.',
            'email.unique' => 'Este e-mail já está cadastrado por outro membro.',
            'telefone.required' => 'O telefone é obrigatório.',
            'documento.required' => 'O documento é obrigatório.',
            'documento.unique' => 'Este documento já está cadastrado por outro membro.',
            'dataNascimento.required' => 'A data de nascimento é obrigatória.',
            'dataNascimento.before' => 'A data de nascimento deve ser anterior a hoje.',
            'endereco.required' => 'O endereço é obrigatório.',
            'numero.required' => 'O número é obrigatório.',
            'numero.numeric' => 'Digite um número válido.',
            'bairro.required' => 'O bairro é obrigatório.',
            'cep.required' => 'O CEP é obrigatório.',
            'cidade.required' => 'A cidade é obrigatória.',
            'uf.required' => 'O estado (UF) é obrigatório.',
            'uf.min' => 'Selecione um estado válido (UF com 2 letras).',
            'uf.max' => 'Selecione um estado válido (UF com 2 letras).',
            'congregacao.required' => 'A congregação é obrigatória.',
            'funcao.required' => 'A função/cargo é obrigatória.',
            'current_password.required' => 'Digite sua senha atual para alterar.',
            'new_password.required' => 'Digite a nova senha.',
            'new_password.min' => 'A nova senha deve ter no mínimo 8 caracteres.',
            'new_password.confirmed' => 'A confirmação da senha não coincide.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput($request->except('password', 'password_confirmation'));
        }

        // Alterar senha
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $perfil->password)) {
                return back()
                    ->withErrors(['current_password' => 'Senha atual incorreta.'])
                    ->withInput($request->except('password', 'password_confirmation'));
            }
            $perfil->password = Hash::make($request->new_password);
        }

        // Montar dados para atualizar
        $dadosParaAtualizar = [
            'nome' => $request->nome,
            'nome_carteira' => $request->nome_carteira,
            'telefone' => $request->telefone,
            'dataNascimento' => $request->dataNascimento,
            'estadoCivil' => $request->estadoCivil,
            'endereco' => $request->endereco,
            'numero' => $request->numero,
            'bairro' => $request->bairro,
            'cep' => $request->cep,
            'cidade' => $request->cidade,
            'uf' => $request->uf,
            'congregacao' => $request->congregacao,
            'funcao' => $request->funcao,
            'dataBatismo' => $request->dataBatismo,
            'data_Consagracao' => $request->data_Consagracao,
            'mae' => $request->mae,
            'pai' => $request->pai,
            'bio' => $request->bio,
            'privacidade' => $request->has('privacidade'),
        ];

        // Atualiza email se mudou
        if ($request->has('email') && $request->email !== $perfil->email) {
            $dadosParaAtualizar['email'] = $request->email;
        }

        // Atualiza documento se mudou
        if ($request->has('documento') && $request->documento !== $perfil->documento) {
            $dadosParaAtualizar['documento'] = preg_replace('/[^0-9]/', '', $request->documento);
        }

        try {
            $perfil->update($dadosParaAtualizar);
            
            // Atualiza sessão com novo nome
            Session::put('membro_nome', $perfil->nome);

            Log::info('Perfil atualizado com sucesso', [
                'matricula' => $perfil->matricula,
                'nome' => $perfil->nome,
                'ip' => request()->ip()
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar perfil', [
                'matricula' => $perfil->matricula,
                'error' => $e->getMessage()
            ]);
            
            return back()
                ->withInput($request->except('password', 'password_confirmation'))
                ->with('error', 'Erro ao atualizar perfil: ' . $e->getMessage());
        }

        $mensagem = 'Perfil atualizado com sucesso!';
        if ($request->filled('new_password')) {
            $mensagem = 'Perfil e senha atualizados com sucesso!';
        }

        return redirect()->route('perfil.show', $perfil->matricula)
                         ->with('success', $mensagem);
    }

    /**
     * ✅ NOVO: Listar posts de um membro
     */
    public function posts($matricula)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Faça login para ver os posts.');
        }

        $perfil = Filiado::on('mysql')->where('matricula', $matricula)->firstOrFail();

        // Verifica se pode ver os posts (público ou próprio)
        $podeVer = $perfil->privacidade || $user->matricula === $matricula || $user->isAdmin();
        
        if (!$podeVer) {
            abort(403, 'Este perfil é privado.');
        }

        $publicacoes = Publicacao::on('mysql')
            ->where('filiado_matricula', $matricula)
            ->with(['autor', 'comentarios.autor', 'curtidas'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        // Adiciona flag de curtida
        $publicacoes->each(function($publicacao) use ($user) {
            $publicacao->curtida_por_mim = $publicacao->isCurtidoPor($user);
        });

        return view('perfil.posts', compact('perfil', 'publicacoes'));
    }

    /**
     * ✅ MELHORADO: Upload de foto usando Auth e Storage
     */
    public function uploadFoto(Request $request)
    {
        Log::info('📸 INICIANDO UPLOAD DE FOTO');

        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Usuário não autenticado.'], 401);
        }

        // Validação
        if (!$request->hasFile('foto')) {
            return response()->json(['success' => false, 'message' => 'Nenhum arquivo selecionado.'], 422);
        }

        $file = $request->file('foto');
        
        if (!$file->isValid()) {
            return response()->json(['success' => false, 'message' => 'Arquivo inválido.'], 422);
        }

        // Valida tamanho (2MB)
        if ($file->getSize() > 2 * 1024 * 1024) {
            return response()->json(['success' => false, 'message' => 'A imagem deve ter no máximo 2MB.'], 422);
        }

        // Valida tipo MIME
        $mimeType = $file->getMimeType();
        $extensoesPermitidas = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        if (!in_array($mimeType, $extensoesPermitidas)) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de arquivo não permitido. Use JPEG, PNG, GIF ou WEBP.'
            ], 422);
        }

        try {
            // Remove foto antiga
            if ($user->foto) {
                $this->removerFotoArquivo($user->foto);
            }

            // ✅ MELHORADO: Usa Storage do Laravel
            $nomeArquivo = time() . '_' . $user->matricula . '.' . $file->getClientOriginalExtension();
            
            // Salva na pasta storage/app/public/fotos
            $path = $file->storeAs('fotos', $nomeArquivo, 'public');
            
            if (!$path) {
                throw new \Exception('Falha ao salvar o arquivo.');
            }

            // Atualiza banco
            $user->foto = $nomeArquivo;
            $user->save();

            // Atualiza sessão
            Session::put('membro_foto', $nomeArquivo);

            Log::info('✅ Foto atualizada com sucesso', [
                'matricula' => $user->matricula,
                'arquivo' => $nomeArquivo
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Foto atualizada com sucesso!',
                'foto_url' => asset('storage/fotos/' . $nomeArquivo)
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erro no upload: ' . $e->getMessage(), [
                'matricula' => $user->matricula,
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao fazer upload: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ MELHORADO: Remover foto
     */
    public function removerFoto()
    {
        Log::info('🗑️ INICIANDO REMOÇÃO DE FOTO');

        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Usuário não autenticado.'], 401);
        }

        try {
            if ($user->foto) {
                $this->removerFotoArquivo($user->foto);
                $user->foto = null;
                $user->save();
                
                // Remove da sessão
                Session::forget('membro_foto');
            }

            Log::info('✅ Foto removida com sucesso', [
                'matricula' => $user->matricula
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Foto removida com sucesso!'
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erro ao remover foto: ' . $e->getMessage(), [
                'matricula' => $user->matricula
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover foto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ NOVO: Método auxiliar para remover arquivo
     */
    private function removerFotoArquivo($nome)
    {
        // Remove do storage
        if (Storage::disk('public')->exists('fotos/' . $nome)) {
            Storage::disk('public')->delete('fotos/' . $nome);
            Log::info('Foto removida (storage): ' . $nome);
        }

        // Remove do public (legado - compatibilidade)
        $publicPath = public_path('storage/fotos/' . $nome);
        if (file_exists($publicPath)) {
            unlink($publicPath);
            Log::info('Foto removida (public): ' . $nome);
        }
    }
}