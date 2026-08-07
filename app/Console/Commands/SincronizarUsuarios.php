<?php

namespace App\Console\Commands;

use App\Models\Comentario;
use App\Models\Publicacao;
use App\Models\Curtida;
use App\Models\Seguidor;
use App\Models\Filiado;
use App\Models\Antigo\FiliadoAntigo;
use App\Services\SincronizacaoUsuarioService;
use Illuminate\Console\Command;

class SincronizarUsuarios extends Command
{
    protected $signature = 'usuarios:sincronizar 
                            {--force : Executar sem confirmação}
                            {--dry-run : Apenas mostrar, não sincronizar}';
    
    protected $description = 'Sincroniza usuários do sistema antigo para o sistema novo';

    protected $sincronizacaoService;

    public function __construct(SincronizacaoUsuarioService $sincronizacaoService)
    {
        parent::__construct();
        $this->sincronizacaoService = $sincronizacaoService;
    }

    public function handle()
    {
        $this->info('🔄 Sincronizando usuários do sistema antigo...');
        $this->newLine();

        // Busca todas as matrículas do sistema
        $matriculas = $this->getMatriculasFromDatabase();
        
        $this->info("📋 Encontradas " . count($matriculas) . " matrículas para verificar");
        $this->newLine();

        if ($this->option('dry-run')) {
            $this->warn('⚠️ Modo dry-run: Apenas verificando, sem sincronizar');
            $this->newLine();
        }

        $bar = $this->output->createProgressBar(count($matriculas));
        $bar->start();

        $resultados = [
            'ja_existem' => 0,
            'sincronizados' => 0,
            'nao_encontrados' => []
        ];

        foreach ($matriculas as $matricula) {
            if (!$this->option('dry-run')) {
                $resultado = $this->sincronizacaoService->sincronizar($matricula);
                
                if ($resultado === true) {
                    $resultados['sincronizados']++;
                } elseif ($resultado === false) {
                    $resultados['nao_encontrados'][] = $matricula;
                }
            } else {
                // Dry-run: apenas verifica
                $existe = Filiado::where('matricula', $matricula)->exists();
                if ($existe) {
                    $resultados['ja_existem']++;
                } else {
                    $filiadoAntigo = FiliadoAntigo::where('matricula', $matricula)->first();
                    if ($filiadoAntigo) {
                        $resultados['sincronizados']++;
                    } else {
                        $resultados['nao_encontrados'][] = $matricula;
                    }
                }
            }
            
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info('📊 RESUMO:');
        if ($this->option('dry-run')) {
            $this->line("   ✅ Já existem: {$resultados['ja_existem']}");
            $this->line("   📝 Seriam sincronizados: {$resultados['sincronizados']}");
        } else {
            $this->line("   ✅ Sincronizados: {$resultados['sincronizados']}");
        }
        $this->line("   ❌ Não encontrados: " . count($resultados['nao_encontrados']));

        if (count($resultados['nao_encontrados']) > 0) {
            $this->newLine();
            $this->warn('⚠️ Matrículas não encontradas no sistema antigo:');
            $naoEncontrados = array_unique($resultados['nao_encontrados']);
            $this->line(implode(', ', array_slice($naoEncontrados, 0, 20)));
            
            if (count($naoEncontrados) > 20) {
                $this->line("   ... e mais " . (count($naoEncontrados) - 20) . " matrículas");
            }

            if (!$this->option('dry-run') && $this->option('force')) {
                if ($this->confirm("\nDeseja remover os dados com matrículas inválidas?")) {
                    $this->removerDadosInvalidos($naoEncontrados);
                }
            }
        }

        $this->newLine();
        $this->info('✅ Processo concluído!');
    }

    private function getMatriculasFromDatabase()
    {
        $matriculas = [];

        try {
            $matriculas = array_merge($matriculas, Comentario::pluck('filiado_matricula')->toArray());
        } catch (\Exception $e) {}
        
        try {
            $matriculas = array_merge($matriculas, Publicacao::pluck('filiado_matricula')->toArray());
        } catch (\Exception $e) {}
        
        try {
            $matriculas = array_merge($matriculas, Curtida::pluck('filiado_matricula')->toArray());
        } catch (\Exception $e) {}
        
        try {
            $matriculas = array_merge($matriculas, Seguidor::pluck('seguidor_matricula')->toArray());
            $matriculas = array_merge($matriculas, Seguidor::pluck('seguido_matricula')->toArray());
        } catch (\Exception $e) {}

        return array_unique($matriculas);
    }

    private function removerDadosInvalidos($matriculas)
    {
        $this->info('🗑️ Removendo dados inválidos...');

        try {
            $comentarios = Comentario::whereIn('filiado_matricula', $matriculas)->delete();
            $this->line("   ✅ Removidos {$comentarios} comentários");
        } catch (\Exception $e) {}

        try {
            $publicacoes = Publicacao::whereIn('filiado_matricula', $matriculas)->delete();
            $this->line("   ✅ Removidas {$publicacoes} publicações");
        } catch (\Exception $e) {}

        try {
            $curtidas = Curtida::whereIn('filiado_matricula', $matriculas)->delete();
            $this->line("   ✅ Removidas {$curtidas} curtidas");
        } catch (\Exception $e) {}

        try {
            $seguidores = Seguidor::whereIn('seguidor_matricula', $matriculas)
                ->orWhereIn('seguido_matricula', $matriculas)
                ->delete();
            $this->line("   ✅ Removidos {$seguidores} seguidores");
        } catch (\Exception $e) {}
    }
}