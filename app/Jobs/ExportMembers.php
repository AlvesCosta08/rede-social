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

class ExportMembers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Número de tentativas da job
     */
    public $tries = 3;

    /**
     * Tempo limite em segundos
     */
    public $timeout = 300;

    public function __construct(
        public readonly string $filePath,
        public readonly array $filters = [],
        public readonly string $format = 'csv'
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info('📊 Exportando membros em background', [
                'file' => $this->filePath,
                'format' => $this->format,
                'filters' => $this->filters
            ]);

            $query = Membro::query();

            if (!empty($this->filters['status'])) {
                $query->where('status', $this->filters['status']);
            }

            if (!empty($this->filters['funcao'])) {
                $query->where('funcao', $this->filters['funcao']);
            }

            if (!empty($this->filters['cidade'])) {
                $query->where('cidade', 'LIKE', "%{$this->filters['cidade']}%");
            }

            if (!empty($this->filters['congregacao'])) {
                $query->where('congregacao', 'LIKE', "%{$this->filters['congregacao']}%");
            }

            $membros = $query->orderBy('matricula')->get();

            $content = match ($this->format) {
                'csv' => $this->generateCsv($membros),
                'json' => $this->generateJson($membros),
                'excel' => $this->generateExcel($membros),
                default => $this->generateCsv($membros),
            };

            Storage::disk('public')->put($this->filePath, $content);

            Log::info('✅ Exportação concluída', [
                'file' => $this->filePath,
                'format' => $this->format,
                'total' => $membros->count()
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Erro ao exportar membros', [
                'file' => $this->filePath,
                'error' => $e->getMessage()
            ]);

            if ($this->attempts() < $this->tries) {
                $this->release(30); // Aguarda 30 segundos
            }
        }
    }

    /**
     * Gera CSV
     */
    private function generateCsv($membros): string
    {
        $headers = [
            'Matrícula',
            'Nome',
            'Email',
            'Telefone',
            'Função',
            'Nível',
            'Cidade',
            'UF',
            'Congregação',
            'Status',
            'Data Nascimento',
            'Data Cadastro'
        ];

        $csv = implode(';', $headers) . "\n";

        foreach ($membros as $membro) {
            $row = [
                $membro->matricula,
                $membro->nome,
                $membro->email ?? '',
                $membro->telefone ?? '',
                $membro->funcao ?? 'Membro',
                $membro->nivel ?? 'usuario',
                $membro->cidade ?? '',
                $membro->uf ?? '',
                $membro->congregacao ?? '',
                $membro->status ?? 'ativo',
                $membro->dataNascimento?->format('d/m/Y') ?? '',
                $membro->datCadastro?->format('d/m/Y') ?? ''
            ];
            $csv .= implode(';', $row) . "\n";
        }

        return $csv;
    }

    /**
     * Gera JSON
     */
    private function generateJson($membros): string
    {
        $data = $membros->map(function ($membro) {
            return [
                'matricula' => $membro->matricula,
                'nome' => $membro->nome,
                'email' => $membro->email,
                'telefone' => $membro->telefone,
                'funcao' => $membro->funcao ?? 'Membro',
                'nivel' => $membro->nivel ?? 'usuario',
                'cidade' => $membro->cidade,
                'uf' => $membro->uf,
                'congregacao' => $membro->congregacao,
                'status' => $membro->status ?? 'ativo',
                'data_nascimento' => $membro->dataNascimento?->format('Y-m-d'),
                'data_cadastro' => $membro->datCadastro?->format('Y-m-d'),
            ];
        });

        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Gera Excel (XML)
     */
    private function generateExcel($membros): string
    {
        // ⭐ SIMPLES XML PARA EXCEL (PODE SUBSTITUIR PELO PHPSPREADSHEET)
        $xml = '<?xml version="1.0" encoding="UTF-8"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
    xmlns:o="urn:schemas-microsoft-com:office:office"
    xmlns:x="urn:schemas-microsoft-com:office:excel"
    xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">
    <Worksheet ss:Name="Membros">
        <Table>';

        // Cabeçalhos
        $headers = ['Matrícula', 'Nome', 'Email', 'Telefone', 'Função', 'Cidade', 'UF', 'Congregação', 'Status'];
        $xml .= '<Row>';
        foreach ($headers as $header) {
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($header) . '</Data></Cell>';
        }
        $xml .= '</Row>';

        // Dados
        foreach ($membros as $membro) {
            $xml .= '<Row>';
            $xml .= '<Cell><Data ss:Type="Number">' . $membro->matricula . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($membro->nome) . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($membro->email ?? '') . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($membro->telefone ?? '') . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($membro->funcao ?? 'Membro') . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($membro->cidade ?? '') . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($membro->uf ?? '') . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($membro->congregacao ?? '') . '</Data></Cell>';
            $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($membro->status ?? 'ativo') . '</Data></Cell>';
            $xml .= '</Row>';
        }

        $xml .= '</Table></Worksheet></Workbook>';

        return $xml;
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('❌ Job de exportação falhou permanentemente', [
            'file' => $this->filePath,
            'format' => $this->format,
            'error' => $exception->getMessage()
        ]);
    }
}