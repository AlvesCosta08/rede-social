<?php

namespace App\Console\Commands;

use App\Models\Comentario;
use App\Models\Publicacao;
use App\Models\Curtida;
use App\Models\Seguidor;
use Illuminate\Console\Command;

class LimparDadosOrfaos extends Command
{
    protected $signature = 'dados:limpar-orfaos 
                            {--force : Executar sem confirmação}';
    
    protected $description = 'Remove todos os dados órfãos do sistema';

    public function handle()
    {
        $this->info('🧹 Limpando dados órfãos...');
        $this->newLine();

        $total = 0;

        // 1. Comentários sem autor
        $count = Comentario::whereDoesntHave('autor')->count();
        if ($count > 0) {
            if ($this->option('force') || $this->confirm("Remover {$count} comentários órfãos?")) {
                Comentario::whereDoesntHave('autor')->delete();
                $total += $count;
                $this->line("   ✅ Comentários: {$count}");
            }
        } else {
            $this->line("   ✅ Comentários: 0");
        }

        // 2. Publicações sem autor
        $count = Publicacao::whereDoesntHave('autor')->count();
        if ($count > 0) {
            if ($this->option('force') || $this->confirm("Remover {$count} publicações órfãs?")) {
                Publicacao::whereDoesntHave('autor')->delete();
                $total += $count;
                $this->line("   ✅ Publicações: {$count}");
            }
        } else {
            $this->line("   ✅ Publicações: 0");
        }

        // 3. Curtidas sem autor
        $count = Curtida::whereDoesntHave('filiado')->count();
        if ($count > 0) {
            if ($this->option('force') || $this->confirm("Remover {$count} curtidas órfãs?")) {
                Curtida::whereDoesntHave('filiado')->delete();
                $total += $count;
                $this->line("   ✅ Curtidas: {$count}");
            }
        } else {
            $this->line("   ✅ Curtidas: 0");
        }

        // 4. Seguidores inválidos
        $count = Seguidor::whereDoesntHave('seguidor')->count();
        if ($count > 0) {
            if ($this->option('force') || $this->confirm("Remover {$count} seguidores (seguidor) inválidos?")) {
                Seguidor::whereDoesntHave('seguidor')->delete();
                $total += $count;
                $this->line("   ✅ Seguidores (seguidor): {$count}");
            }
        } else {
            $this->line("   ✅ Seguidores (seguidor): 0");
        }

        $count = Seguidor::whereDoesntHave('seguido')->count();
        if ($count > 0) {
            if ($this->option('force') || $this->confirm("Remover {$count} seguidores (seguido) inválidos?")) {
                Seguidor::whereDoesntHave('seguido')->delete();
                $total += $count;
                $this->line("   ✅ Seguidores (seguido): {$count}");
            }
        } else {
            $this->line("   ✅ Seguidores (seguido): 0");
        }

        $this->newLine();
        $this->info("✅ Total removido: {$total} registros");
    }
}