<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\DTOs\Membro\UpdateMembroDTO;
use App\Models\Membro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Mostrar perfil do usuário
     */
    public function show()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Faça login para continuar.');
        }

        return view('perfil.show', compact('user'));
    }

    /**
     * Mostrar formulário de edição
     */
    public function edit()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Faça login para continuar.');
        }

        return view('perfil.edit', compact('user'));
    }

    /**
     * Atualizar perfil
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Faça login para continuar.');
        }

        Log::info('📝 ATUALIZANDO PERFIL', [
            'matricula' => $user->matricula,
            'ip' => $request->ip()
        ]);

        try {
            $validated = $request->validate([
                'nome' => 'nullable|string|max:255|min:3',
                'email' => 'nullable|email|max:255|unique:filiado,email,' . $user->matricula . ',matricula',
                'telefone' => 'nullable|string|max:20|min:10',
                'telefone2' => 'nullable|string|max:20',
                'endereco' => 'nullable|string|max:255',
                'numero' => 'nullable|string|max:20',
                'bairro' => 'nullable|string|max:255',
                'cep' => 'nullable|string|max:20',
                'cidade' => 'nullable|string|max:100',
                'uf' => 'nullable|string|max:2|in:AC,AL,AP,AM,BA,CE,DF,ES,GO,MA,MT,MS,MG,PA,PB,PR,PE,PI,RJ,RN,RS,RO,RR,SC,SP,SE,TO',
                'bio' => 'nullable|string|max:500',
                'privacidade' => 'nullable|boolean',
                'current_password' => 'nullable|string|min:8',
                'new_password' => 'nullable|string|min:8|confirmed',
            ]);

            // Verificar senha atual se for mudar
            if (!empty($validated['new_password'])) {
                if (empty($validated['current_password'])) {
                    return back()->withErrors(['current_password' => 'Digite sua senha atual para alterar a senha.']);
                }

                if (!Hash::check($validated['current_password'], $user->password)) {
                    return back()->withErrors(['current_password' => 'Senha atual incorreta.']);
                }
            }

            // Criar DTO
            $updateDTO = UpdateMembroDTO::fromRequest([
                'matricula' => $user->matricula,
                ...$validated
            ]);

            // Atualizar
            $user->update($updateDTO->toArray());

            Log::info('✅ PERFIL ATUALIZADO', [
                'matricula' => $user->matricula,
                'ip' => $request->ip()
            ]);

            return redirect()->route('perfil.show')
                ->with('success', 'Perfil atualizado com sucesso!');

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            Log::error('❌ ERRO AO ATUALIZAR PERFIL', [
                'matricula' => $user->matricula,
                'error' => $e->getMessage(),
                'ip' => $request->ip()
            ]);

            return back()->with('error', 'Erro ao atualizar perfil. Tente novamente.');
        }
    }

    /**
     * Upload de foto de perfil
     */
    public function uploadFoto(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado.'
            ], 401);
        }

        try {
            $request->validate([
                'foto' => 'required|image|max:2048|mimes:jpeg,png,gif,webp'
            ]);

            $file = $request->file('foto');
            $nomeArquivo = time() . '_' . $user->matricula . '.' . $file->getClientOriginalExtension();
            
            // Salvar
            $path = $file->storeAs('fotos', $nomeArquivo, 'public');
            
            if (!$path) {
                throw new \Exception('Falha ao salvar o arquivo.');
            }

            // Remover foto antiga
            if ($user->foto) {
                Storage::disk('public')->delete('fotos/' . $user->foto);
                
                $publicPath = public_path('storage/fotos/' . $user->foto);
                if (file_exists($publicPath)) {
                    unlink($publicPath);
                }
            }

            $user->foto = $nomeArquivo;
            $user->save();

            Log::info('📸 UPLOAD DE FOTO', [
                'matricula' => $user->matricula,
                'arquivo' => $nomeArquivo,
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Foto atualizada com sucesso!',
                'foto_url' => asset('storage/fotos/' . $nomeArquivo)
            ]);

        } catch (\Exception $e) {
            Log::error('❌ ERRO NO UPLOAD DE FOTO', [
                'matricula' => $user->matricula,
                'error' => $e->getMessage(),
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao fazer upload: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remover foto de perfil
     */
    public function removerFoto(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado.'
            ], 401);
        }

        try {
            if ($user->foto) {
                Storage::disk('public')->delete('fotos/' . $user->foto);
                
                $publicPath = public_path('storage/fotos/' . $user->foto);
                if (file_exists($publicPath)) {
                    unlink($publicPath);
                }

                $user->foto = null;
                $user->save();

                Log::info('🗑️ FOTO REMOVIDA', [
                    'matricula' => $user->matricula,
                    'ip' => $request->ip()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Foto removida com sucesso!'
            ]);

        } catch (\Exception $e) {
            Log::error('❌ ERRO AO REMOVER FOTO', [
                'matricula' => $user->matricula,
                'error' => $e->getMessage(),
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover foto.'
            ], 500);
        }
    }
}