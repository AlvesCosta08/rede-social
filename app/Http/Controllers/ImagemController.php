<?php

namespace App\Http\Controllers;

use App\Models\Membro; // ⭐ ADICIONADO PARA REFERÊNCIA
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;

class ImagemController extends Controller
{
    /**
     * Mostrar uma imagem de perfil
     */
    public function show($filename)
    {
        // ✅ VALIDA O NOME DO ARQUIVO
        if (empty($filename) || !$this->validateFilename($filename)) {
            Log::warning('⚠️ Nome de arquivo inválido', [
                'filename' => $filename,
                'ip' => request()->ip()
            ]);
            
            return $this->getDefaultImage();
        }

        // ✅ VERIFICA SE O ARQUIVO EXISTE NO STORAGE
        $storagePath = storage_path('app/public/fotos/' . $filename);
        $publicPath = public_path('storage/fotos/' . $filename);

        $filePath = null;

        // Tenta encontrar o arquivo
        if (file_exists($storagePath)) {
            $filePath = $storagePath;
        } elseif (file_exists($publicPath)) {
            $filePath = $publicPath;
        }

        if (!$filePath) {
            Log::warning('⚠️ Arquivo não encontrado', [
                'filename' => $filename,
                'storage_path' => $storagePath,
                'public_path' => $publicPath,
                'ip' => request()->ip()
            ]);
            
            return $this->getDefaultImage();
        }

        try {
            // ✅ VERIFICA SE É UMA IMAGEM VÁLIDA
            $mimeType = mime_content_type($filePath);
            if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'])) {
                Log::warning('⚠️ Tipo MIME inválido', [
                    'filename' => $filename,
                    'mime_type' => $mimeType,
                    'ip' => request()->ip()
                ]);
                
                return $this->getDefaultImage();
            }

            // ✅ RETORNA A IMAGEM COM CACHE
            $fileContent = file_get_contents($filePath);
            $lastModified = filemtime($filePath);
            
            $response = response($fileContent, 200)
                ->header('Content-Type', $mimeType)
                ->header('Content-Length', filesize($filePath))
                ->header('Cache-Control', 'public, max-age=604800, must-revalidate') // 7 dias
                ->header('Pragma', 'public')
                ->header('Expires', gmdate('D, d M Y H:i:s', time() + 604800) . ' GMT')
                ->header('Last-Modified', gmdate('D, d M Y H:i:s', $lastModified) . ' GMT')
                ->header('ETag', '"' . md5($fileContent) . '"');

            Log::info('📸 Imagem servida', [
                'filename' => $filename,
                'size' => filesize($filePath),
                'mime_type' => $mimeType,
                'ip' => request()->ip()
            ]);

            return $response;

        } catch (\Exception $e) {
            Log::error('❌ Erro ao servir imagem', [
                'filename' => $filename,
                'error' => $e->getMessage(),
                'ip' => request()->ip()
            ]);
            
            return $this->getDefaultImage();
        }
    }

    /**
     * Validar nome do arquivo (evitar path traversal)
     */
    private function validateFilename($filename): bool
    {
        // ✅ NOME DEVE TER EXTENSÃO VÁLIDA
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'JPG', 'JPEG', 'PNG', 'GIF', 'WEBP', 'SVG'];
        
        if (!in_array($extension, $allowedExtensions)) {
            return false;
        }

        // ✅ NOME DEVE TER O FORMATO ESPERADO (timestamp_matricula.ext)
        // Permite também nomes sem underscore (para imagens padrão)
        if (!preg_match('/^[0-9]+_[0-9]+\.[a-zA-Z]+$/', $filename) && 
            !preg_match('/^[a-zA-Z0-9_-]+\.[a-zA-Z]+$/', $filename)) {
            return false;
        }

        // ✅ EVITA PATH TRAVERSAL
        if (strpos($filename, '..') !== false || 
            strpos($filename, '/') !== false || 
            strpos($filename, '\\') !== false) {
            return false;
        }

        // ✅ EVITA ARQUIVOS PERIGOSOS
        $dangerous = ['php', 'exe', 'bat', 'sh', 'cmd', 'js', 'html', 'htm', 'xml'];
        if (in_array(strtolower($extension), $dangerous)) {
            return false;
        }

        return true;
    }

    /**
     * Retorna uma imagem padrão quando não encontra
     */
    private function getDefaultImage()
    {
        Log::info('🖼️ Imagem padrão gerada', [
            'ip' => request()->ip()
        ]);

        // ⭐ Cria uma imagem SVG moderna com iniciais
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="200" height="200" viewBox="0 0 200 200">
            <defs>
                <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" style="stop-color:#6C63FF;stop-opacity:1" />
                    <stop offset="100%" style="stop-color:#4A47A3;stop-opacity:1" />
                </linearGradient>
            </defs>
            <rect width="200" height="200" rx="100" fill="url(#grad)"/>
            <circle cx="100" cy="80" r="35" fill="rgba(255,255,255,0.3)"/>
            <circle cx="100" cy="80" r="25" fill="white"/>
            <circle cx="100" cy="80" r="20" fill="#6C63FF"/>
            <circle cx="100" cy="140" r="45" fill="rgba(255,255,255,0.3)"/>
            <circle cx="100" cy="145" r="35" fill="white"/>
            <text x="100" y="155" font-family="Arial, sans-serif" font-size="40" font-weight="bold" fill="#6C63FF" text-anchor="middle">👤</text>
        </svg>';

        return response($svg)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Cache-Control', 'public, max-age=86400')
            ->header('Expires', gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');
    }

    /**
     * Upload de imagem (método alternativo)
     * ⭐ VERIFICA PERMISSÃO USANDO PODE()
     */
    public function upload(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado.'
            ], 401);
        }

        // ⭐ VERIFICA SE O USUÁRIO PODE FAZER UPLOAD
        if (!$user->pode('editar_membro', $user)) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para alterar sua foto.'
            ], 403);
        }

        $request->validate([
            'foto' => 'required|image|max:2048|mimes:jpeg,png,gif,webp'
        ]);

        try {
            $file = $request->file('foto');
            $nomeArquivo = time() . '_' . $user->matricula . '.' . $file->getClientOriginalExtension();
            
            // Salva usando Storage
            $path = $file->storeAs('fotos', $nomeArquivo, 'public');
            
            if (!$path) {
                throw new \Exception('Falha ao salvar o arquivo.');
            }

            // Remove foto antiga
            if ($user->foto) {
                Storage::disk('public')->delete('fotos/' . $user->foto);
                // Remove do public (legado)
                $publicPath = public_path('storage/fotos/' . $user->foto);
                if (file_exists($publicPath)) {
                    unlink($publicPath);
                }
            }

            $user->foto = $nomeArquivo;
            $user->save();

            Log::info('📸 Upload de foto via ImagemController', [
                'matricula' => $user->matricula,
                'arquivo' => $nomeArquivo,
                'ip' => request()->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Foto atualizada com sucesso!',
                'foto_url' => asset('storage/fotos/' . $nomeArquivo)
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erro no upload', [
                'matricula' => $user->matricula,
                'error' => $e->getMessage(),
                'ip' => request()->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao fazer upload: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remover imagem (método alternativo)
     */
    public function destroy(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Usuário não autenticado.'
            ], 401);
        }

        // ⭐ VERIFICA SE O USUÁRIO PODE REMOVER A FOTO
        if (!$user->pode('editar_membro', $user)) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para remover sua foto.'
            ], 403);
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

                Log::info('🗑️ Foto removida via ImagemController', [
                    'matricula' => $user->matricula,
                    'ip' => request()->ip()
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Foto removida com sucesso!'
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erro ao remover foto', [
                'matricula' => $user->matricula,
                'error' => $e->getMessage(),
                'ip' => request()->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao remover foto.'
            ], 500);
        }
    }
}