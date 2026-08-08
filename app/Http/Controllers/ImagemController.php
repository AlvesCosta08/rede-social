<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth; // ⭐ ADICIONADO

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
            if (!in_array($mimeType, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
                Log::warning('⚠️ Tipo MIME inválido', [
                    'filename' => $filename,
                    'mime_type' => $mimeType,
                    'ip' => request()->ip()
                ]);
                
                return $this->getDefaultImage();
            }

            // ✅ RETORNA A IMAGEM COM CACHE
            $fileContent = file_get_contents($filePath);
            
            $response = response($fileContent, 200)
                ->header('Content-Type', $mimeType)
                ->header('Content-Length', filesize($filePath))
                ->header('Cache-Control', 'public, max-age=86400, must-revalidate')
                ->header('Pragma', 'public')
                ->header('Expires', gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT')
                ->header('Last-Modified', gmdate('D, d M Y H:i:s', filemtime($filePath)) . ' GMT');

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
    private function validateFilename($filename)
    {
        // ✅ NOME DEVE TER EXTENSÃO VÁLIDA
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'JPG', 'JPEG', 'PNG', 'GIF', 'WEBP'];
        
        if (!in_array($extension, $allowedExtensions)) {
            return false;
        }

        // ✅ NOME DEVE TER O FORMATO ESPERADO (timestamp_matricula.ext)
        $pattern = '/^[0-9]+_[0-9]+\.[a-zA-Z]+$/';
        if (!preg_match($pattern, $filename)) {
            return false;
        }

        // ✅ EVITA PATH TRAVERSAL
        if (strpos($filename, '..') !== false || strpos($filename, '/') !== false || strpos($filename, '\\') !== false) {
            return false;
        }

        // ✅ EVITA ARQUIVOS PERIGOSOS
        $dangerous = ['php', 'exe', 'bat', 'sh', 'cmd', 'js', 'html', 'htm'];
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
        // ✅ CRIA UMA IMAGEM PADRÃO DINAMICAMENTE
        $width = 200;
        $height = 200;
        $image = imagecreatetruecolor($width, $height);
        
        // Cor de fundo (roxo)
        $bgColor = imagecolorallocate($image, 108, 60, 225);
        imagefill($image, 0, 0, $bgColor);
        
        // Texto
        $textColor = imagecolorallocate($image, 255, 255, 255);
        $text = '👤';
        
        // Usar GD para criar imagem
        $fontSize = 80;
        $font = public_path('fonts/arial.ttf');
        
        if (file_exists($font)) {
            $textBox = imagettfbbox($fontSize, 0, $font, $text);
            $textWidth = $textBox[2] - $textBox[0];
            $textHeight = $textBox[1] - $textBox[7];
            $x = ($width - $textWidth) / 2;
            $y = ($height - $textHeight) / 2 + $textHeight;
            imagettftext($image, $fontSize, 0, $x, $y, $textColor, $font, $text);
        } else {
            // Fallback: texto simples
            $x = ($width - 40) / 2;
            $y = ($height - 20) / 2;
            imagestring($image, 5, $x, $y, 'USER', $textColor);
        }

        // Salva em buffer
        ob_start();
        imagepng($image);
        $imageData = ob_get_clean();
        imagedestroy($image);

        Log::info('🖼️ Imagem padrão gerada', [
            'ip' => request()->ip()
        ]);

        return response($imageData, 200)
            ->header('Content-Type', 'image/png')
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

        // ⭐ VERIFICA SE O USUÁRIO PODE FAZER UPLOAD (SEMPRE PODE, É SEU PRÓPRIO PERFIL)
        // Não há restrição adicional aqui, pois o usuário só pode alterar sua própria foto

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
                'arquivo' => $nomeArquivo
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Foto atualizada com sucesso!',
                'foto_url' => asset('storage/fotos/' . $nomeArquivo)
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erro no upload', [
                'matricula' => $user->matricula,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao fazer upload: ' . $e->getMessage()
            ], 500);
        }
    }
}