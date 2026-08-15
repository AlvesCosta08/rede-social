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

class GenerateThumbnail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly Membro $membro,
        public readonly string $imagePath
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('🖼️ Gerando thumbnail', [
                'matricula' => $this->membro->matricula,
                'image' => $this->imagePath
            ]);

            $fullPath = storage_path('app/public/' . $this->imagePath);

            if (!file_exists($fullPath)) {
                Log::warning('⚠️ Arquivo não encontrado para thumbnail', [
                    'path' => $fullPath
                ]);
                return;
            }

            $dir = dirname($fullPath);
            $filename = basename($fullPath);
            $thumbnailPath = $dir . '/thumb_' . $filename;

            // ⭐ DESCOMENTE QUANDO INSTALAR INTERVENTION IMAGE
            // $image = Image::make($fullPath);
            // $image->resize(200, 200, function ($constraint) {
            //     $constraint->aspectRatio();
            //     $constraint->upsize();
            // });
            // $image->save($thumbnailPath);

            Log::info('✅ Thumbnail gerada com sucesso', [
                'matricula' => $this->membro->matricula,
                'thumbnail' => $thumbnailPath
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erro ao gerar thumbnail', [
                'matricula' => $this->membro->matricula,
                'error' => $e->getMessage()
            ]);
        }
    }
}