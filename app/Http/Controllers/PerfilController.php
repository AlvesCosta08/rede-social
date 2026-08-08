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
     * ⭐ VERIFICA PERMISSÃO PARA DADOS PRIVADOS
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

        // ⭐ VERIFICA SE O USUÁRIO LOGADO PODE VER DADOS PRIVADOS
        $podeVerPrivado = $membroLogado && (
            $membroLogado->matricula === $matricula || 
            $membroLogado->pode('ver_membro', $perfil)
        );

        // Se não tiver permissão, esconde dados sensíveis
        if (!$podeVerPrivado) {
            $perfil->makeHidden(['email', 'telefone', 'documento', 'endereco', 'dataNascimento', 'mae', 'pai']);
        }

        return view('perfil.show', compact('perfil', 'membroLogado', 'podeVerPrivado'));
    }

    /**
     * Editar perfil do usuário logado
     * ⭐ VERIFICA SE O USUÁRIO ESTÁ AUTENTICADO
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
     * ⭐ VERIFICA PERMISSÃO ANTES DE ATUALIZAR
     * ⭐ CORRIGIDO: VALIDAÇÃO DE DATAS E BIO
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

        // ⭐ VERIFICA SE O USUÁRIO PODE EDITAR ESTE PERFIL
        if (!$user->pode('editar_membro', $perfil) && $user->matricula !== $perfil->matricula) {
            abort(403, 'Você não tem permissão para editar este perfil.');
        }

        // ⭐ VALIDAÇÃO CORRIGIDA - DATAS ACEITAM VÁRIOS FORMATOS
        $rules = [
            'nome' => 'required|string|max:255',
            'nome_carteira' => 'nullable|string|max:100',
            'telefone' => 'required|string|max:20',
            'dataNascimento' => 'nullable|date|before:today',
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
            'bio' => 'nullable|string|max:5000', // ⭐ AUMENTADO PARA 5000
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

        // Mensagens de erro
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
            'bio.max' => 'A biografia não pode ter mais de 5000 caracteres.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput($request->except('password', 'password_confirmation'));
        }

        // ⭐ VERIFICA SENHA ATUAL
        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $perfil->password)) {
                return back()
                    ->withErrors(['current_password' => 'Senha atual incorreta.'])
                    ->withInput($request->except('password', 'password_confirmation'));
            }
            $perfil->password = Hash::make($request->new_password);
        }

        // ⭐ PREPARA OS DADOS PARA ATUALIZAÇÃO
        $dadosParaAtualizar = [
            'nome' => $request->nome,
            'nome_carteira' => $request->nome_carteira,
            'telefone' => $request->telefone,
            'dataNascimento' => $this->formatarData($request->dataNascimento),
            'estadoCivil' => $request->estadoCivil,
            'endereco' => $request->endereco,
            'numero' => $request->numero,
            'bairro' => $request->bairro,
            'cep' => $request->cep,
            'cidade' => $request->cidade,
            'uf' => $request->uf,
            'congregacao' => $request->congregacao,
            'funcao' => $request->funcao,
            'dataBatismo' => $this->formatarData($request->dataBatismo),
            'data_Consagracao' => $this->formatarData($request->data_Consagracao),
            'mae' => $request->mae,
            'pai' => $request->pai,
            'bio' => $this->formatarBio($request->bio), // ⭐ TRUNCA BIO SE NECESSÁRIO
            'privacidade' => $request->has('privacidade'),
        ];

        // Atualiza email se foi alterado
        if ($request->has('email') && $request->email !== $perfil->email) {
            $dadosParaAtualizar['email'] = $request->email;
        }

        // Atualiza documento se foi alterado
        if ($request->has('documento') && $request->documento !== $perfil->documento) {
            $dadosParaAtualizar['documento'] = preg_replace('/[^0-9]/', '', $request->documento);
        }

        try {
            // ⭐ ATUALIZA O PERFIL (OS MUTATORS DO MODEL FORMATAM AS DATAS)
            $perfil->update($dadosParaAtualizar);
            
            Session::put('membro_nome', $perfil->nome);

            Log::info('✅ Perfil atualizado com sucesso', [
                'matricula' => $perfil->matricula,
                'nome' => $perfil->nome,
                'ip' => request()->ip()
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erro ao atualizar perfil', [
                'matricula' => $perfil->matricula,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
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
     * ⭐ FUNÇÃO AUXILIAR - FORMATA DATA PARA Y-m-d
     */
    private function formatarData($data)
    {
        if (empty($data)) {
            return null;
        }

        // Se já estiver no formato Y-m-d, retorna
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
            return $data;
        }

        // Se estiver no formato d/m/Y, converte
        if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $data)) {
            try {
                $date = Carbon::createFromFormat('d/m/Y', $data);
                return $date->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        // Tenta converter qualquer formato
        try {
            $date = Carbon::parse($data);
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * ⭐ FUNÇÃO AUXILIAR - FORMATA BIO (LIMITA TAMANHO)
     */
    private function formatarBio($bio)
    {
        if (empty($bio)) {
            return null;
        }

        // Limita a 5000 caracteres
        if (strlen($bio) > 5000) {
            return substr($bio, 0, 5000);
        }

        return $bio;
    }

    /**
     * Listar posts de um membro
     * ⭐ VERIFICA PERMISSÃO USANDO O MÉTODO PODE()
     */
    public function posts($matricula)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Faça login para ver os posts.');
        }

        $perfil = Filiado::on('mysql')->where('matricula', $matricula)->firstOrFail();

        // ⭐ VERIFICA PERMISSÃO USANDO O MÉTODO PODE()
        $podeVer = $perfil->privacidade || 
                   $user->matricula === $matricula || 
                   $user->pode('ver_membro', $perfil);
        
        if (!$podeVer) {
            abort(403, 'Este perfil é privado.');
        }

        $publicacoes = Publicacao::on('mysql')
            ->where('filiado_matricula', $matricula)
            ->with(['autor', 'comentarios.autor', 'curtidas'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $publicacoes->each(function($publicacao) use ($user) {
            $publicacao->curtida_por_mim = $publicacao->isCurtidoPor($user);
        });

        return view('perfil.posts', compact('perfil', 'publicacoes'));
    }

    /**
     * Upload de foto usando Auth e Storage
     * ⭐ APENAS O PRÓPRIO USUÁRIO PODE FAZER UPLOAD
     */
    public function uploadFoto(Request $request)
    {
        Log::info('📸 INICIANDO UPLOAD DE FOTO');

        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Usuário não autenticado.'], 401);
        }

        if (!$request->hasFile('foto')) {
            return response()->json(['success' => false, 'message' => 'Nenhum arquivo selecionado.'], 422);
        }

        $file = $request->file('foto');
        
        if (!$file->isValid()) {
            return response()->json(['success' => false, 'message' => 'Arquivo inválido.'], 422);
        }

        if ($file->getSize() > 2 * 1024 * 1024) {
            return response()->json(['success' => false, 'message' => 'A imagem deve ter no máximo 2MB.'], 422);
        }

        $mimeType = $file->getMimeType();
        $extensoesPermitidas = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        if (!in_array($mimeType, $extensoesPermitidas)) {
            return response()->json([
                'success' => false,
                'message' => 'Tipo de arquivo não permitido. Use JPEG, PNG, GIF ou WEBP.'
            ], 422);
        }

        try {
            if ($user->foto) {
                $this->removerFotoArquivo($user->foto);
            }

            $nomeArquivo = time() . '_' . $user->matricula . '.' . $file->getClientOriginalExtension();
            
            $path = $file->storeAs('fotos', $nomeArquivo, 'public');
            
            if (!$path) {
                throw new \Exception('Falha ao salvar o arquivo.');
            }

            $user->foto = $nomeArquivo;
            $user->save();

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
     * Remover foto
     * ⭐ APENAS O PRÓPRIO USUÁRIO PODE REMOVER SUA FOTO
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
     * Método auxiliar para remover arquivo
     */
    private function removerFotoArquivo($nome)
    {
        if (Storage::disk('public')->exists('fotos/' . $nome)) {
            Storage::disk('public')->delete('fotos/' . $nome);
            Log::info('Foto removida (storage): ' . $nome);
        }

        $publicPath = public_path('storage/fotos/' . $nome);
        if (file_exists($publicPath)) {
            unlink($publicPath);
            Log::info('Foto removida (public): ' . $nome);
        }
    }

    /**
     * ⭐ MÉTODO PARA EXIBIR IMAGEM (ROTA)
     */
    public function getFoto($filename)
    {
        $path = storage_path('app/public/fotos/' . $filename);

        if (!file_exists($path)) {
            Log::warning('⚠️ Arquivo não encontrado', [
                'filename' => $filename,
                'storage_path' => $path,
                'public_path' => public_path('storage/fotos/' . $filename),
                'ip' => request()->ip()
            ]);

            // ⭐ GERA IMAGEM PADRÃO
            return $this->gerarImagemPadrao();
        }

        $mimeType = mime_content_type($path);
        
        Log::info('📸 Imagem servida', [
            'filename' => $filename,
            'size' => filesize($path),
            'mime_type' => $mimeType,
            'ip' => request()->ip()
        ]);

        return response()->file($path);
    }

    /**
     * ⭐ GERA IMAGEM PADRÃO QUANDO A FOTO NÃO EXISTE
     */
    private function gerarImagemPadrao()
    {
        Log::info('🖼️ Imagem padrão gerada', [
            'ip' => request()->ip()
        ]);

        // Cria uma imagem SVG com as iniciais
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200">
            <rect width="200" height="200" rx="100" fill="#d4af37"/>
            <text x="100" y="120" font-family="Arial" font-size="80" font-weight="bold" fill="white" text-anchor="middle">👤</text>
        </svg>';

        return response($svg)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}