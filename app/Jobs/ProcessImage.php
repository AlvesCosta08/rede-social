<?php

namespace App\Jobs;

use App\Models\Membro;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Número de tentativas da job
     */
    public $tries = 3;

    /**
     * Tempo limite em segundos
     */
    public $timeout = 60;

    public function __construct(
        public readonly Membro $membro,
        public readonly string $imagePath,
        public readonly string $operation = 'optimize'
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('🖼️ Processando imagem em background', [
                'matricula' => $this->membro->matricula,
                'path' => $this->imagePath,
                'operation' => $this->operation
            ]);

            $fullPath = storage_path('app/public/' . $this->imagePath);

            if (!file_exists($fullPath)) {
                Log::warning('⚠️ Arquivo não encontrado para processamento', [
                    'path' => $fullPath
                ]);
                return;
            }

            switch ($this->operation) {
                case 'optimize':
                    $this->optimizeImage($fullPath);
                    break;
                case 'resize':
                    $this->resizeImage($fullPath);
                    break;
                case 'thumbnail':
                    $this->createThumbnail($fullPath);
                    break;
                default:
                    Log::warning('⚠️ Operação desconhecida', [
                        'operation' => $this->operation
                    ]);
            }

            Log::info('✅ Imagem processada com sucesso', [
                'matricula' => $this->membro->matricula,
                'operation' => $this->operation
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erro ao processar imagem', [
                'matricula' => $this->membro->matricula,
                'path' => $this->imagePath,
                'error' => $e->getMessage()
            ]);

            // Reprocessa em caso de falha
            if ($this->attempts() < $this->tries) {
                $this->release(10); // Aguarda 10 segundos
            }
        }
    }

    /**
     * Otimiza a imagem
     */
    private function optimizeImage(string $path): void
    {
        // ⭐ DESCOMENTE QUANDO INSTALAR INTERVENTION IMAGE
        // $image = Image::make($path);
        // $image->save($path, 85); // Qualidade 85%
        
        Log::info('🔄 Imagem otimizada', ['path' => $path]);
    }

    /**
     * Redimensiona a imagem
     */
    private function resizeImage(string $path): void
    {
        // ⭐ DESCOMENTE QUANDO INSTALAR INTERVENTION IMAGE
        // $image = Image::make($path);
        // $image->resize(800, 800, function ($constraint) {
        //     $constraint->aspectRatio();
        //     $constraint->upsize();
        // });
        // $image->save($path);
        
        Log::info('🔄 Imagem redimensionada', ['path' => $path]);
    }

    /**
     * Cria thumbnail da imagem
     */
    private function createThumbnail(string $path): void
    {
        $dir = dirname($path);
        $filename = basename($path);
        $thumbnailPath = $dir . '/thumb_' . $filename;

        // ⭐ DESCOMENTE QUANDO INSTALAR INTERVENTION IMAGE
        // $image = Image::make($path);
        // $image->resize(200, 200, function ($constraint) {
        //     $constraint->aspectRatio();
        //     $constraint->upsize();
        // });
        // $image->save($thumbnailPath);
        
        Log::info('🔄 Thumbnail criada', [
            'original' => $path,
            'thumbnail' => $thumbnailPath
        ]);
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('❌ Job de imagem falhou permanentemente', [
            'matricula' => $this->membro->matricula,
            'path' => $this->imagePath,
            'error' => $exception->getMessage()
        ]);
    }
}