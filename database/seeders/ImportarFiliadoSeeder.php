<?php

namespace Database\Seeders;

use App\Models\Filiado;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class ImportarFiliadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->info('🔄 IMPORTADOR DE FILIADO - BANCO ANTIGO → BANCO NOVO');
        $this->info('==================================================');
        $this->newLine();

        // ============================================================
        // VERIFICAR CONEXÃO COM O BANCO ANTIGO
        // ============================================================
        $this->info('🔍 Verificando conexão com o banco antigo...');
        
        try {
            DB::connection('sistema_antigo')->getPdo();
            $this->info('✅ Conexão com banco antigo estabelecida!');
        } catch (\Exception $e) {
            $this->error('❌ Erro ao conectar ao banco antigo: ' . $e->getMessage());
            $this->warn('⚠️ Verifique as configurações no arquivo .env');
            return;
        }

        // ============================================================
        // VERIFICAR TABELA FILIADO NO BANCO ANTIGO
        // ============================================================
        $this->info('🔍 Verificando tabela filiado no banco antigo...');
        
        try {
            $totalAntigo = DB::connection('sistema_antigo')->table('filiado')->count();
            $this->info("📊 Total de registros no banco antigo: {$totalAntigo}");
            
            if ($totalAntigo === 0) {
                $this->warn('⚠️ Nenhum registro encontrado na tabela filiado.');
                return;
            }
        } catch (\Exception $e) {
            $this->error('❌ Erro ao acessar tabela filiado: ' . $e->getMessage());
            return;
        }

        // ============================================================
        // VERIFICAR TABELA FILIADO NO BANCO NOVO
        // ============================================================
        $this->info('🔍 Verificando tabela filiado no banco novo...');
        $totalNovo = Filiado::count();
        $this->info("📊 Total de registros no banco novo: {$totalNovo}");
        $this->newLine();

        // ============================================================
        // CONFIRMAÇÃO
        // ============================================================
        if (!$this->confirm("Deseja importar {$totalAntigo} registros do banco antigo?")) {
            $this->info('❌ Operação cancelada.');
            return;
        }

        // ============================================================
        // BUSCAR DADOS DO BANCO ANTIGO
        // ============================================================
        $this->info('📥 Buscando dados do banco antigo...');
        
        $membrosAntigos = DB::connection('sistema_antigo')
                            ->table('filiado')
                            ->orderBy('matricula')
                            ->get();
        
        $total = $membrosAntigos->count();
        $this->info("📋 Total a importar: {$total}");
        $this->newLine();

        // ============================================================
        // PROCESSAR IMPORTAÇÃO
        // ============================================================
        $this->info('🔄 Importando dados...');
        $this->newLine();
        
        $bar = $this->output->createProgressBar($total);
        $bar->start();
        
        $importados = 0;
        $atualizados = 0;
        $erros = 0;
        $errosLista = [];

        foreach ($membrosAntigos as $antigo) {
            try {
                // Verifica se já existe no banco novo
                $existe = Filiado::where('matricula', $antigo->matricula)->exists();
                
                // Mapear todos os campos
                $dados = [
                    'matricula' => $antigo->matricula,
                    'congregacao' => $antigo->congregacao ?? '',
                    'nome' => $antigo->nome ?? 'Usuário',
                    'nome_carteira' => $antigo->nome_carteira ?? $antigo->nome,
                    'logradouro' => $antigo->logradouro ?? '',
                    'endereco' => $antigo->endereco ?? '',
                    'numero' => $antigo->numero ?? 0,
                    'bairro' => $antigo->bairro ?? '',
                    'cep' => $antigo->cep ?? '',
                    'email' => $antigo->email ?? '',
                    'cidade' => $antigo->cidade ?? '',
                    'uf' => $antigo->uf ?? '',
                    'documento' => $antigo->documento ?? '',
                    'telefone' => $antigo->telefone ?? '',
                    'estadoCivil' => $antigo->estadoCivil ?? '',
                    'dataNascimento' => $antigo->dataNascimento ?? null,
                    'mae' => $antigo->mae ?? '',
                    'pai' => $antigo->pai ?? '',
                    'datCadastro' => $antigo->datCadastro ?? null,
                    'dataBatismo' => $antigo->dataBatismo ?? null,
                    'data_Consagracao' => $antigo->data_Consagracao ?? null,
                    'arquivo' => $antigo->arquivo ?? '',
                    'cartas' => $antigo->cartas ?? '',
                    'funcao' => $antigo->funcao ?? 'Membro',
                    'status' => strtolower($antigo->status ?? 'ativo'),
                    // Campos novos do banco novo
                    'bio' => null,
                    'privacidade' => true,
                    'foto' => null,
                    'password' => Hash::make('123456'), // Senha padrão
                ];
                
                if (!$existe) {
                    Filiado::create($dados);
                    $importados++;
                } else {
                    // Atualiza apenas se o registro não tiver senha (não ativado)
                    $membro = Filiado::where('matricula', $antigo->matricula)->first();
                    if (empty($membro->password)) {
                        Filiado::where('matricula', $antigo->matricula)->update($dados);
                        $atualizados++;
                    } else {
                        // Se já tem senha, não atualiza os dados principais
                        $atualizados++;
                    }
                }
                
            } catch (\Exception $e) {
                $erros++;
                $errosLista[] = [
                    'matricula' => $antigo->matricula,
                    'erro' => $e->getMessage()
                ];
                
                Log::error("Erro ao importar matrícula {$antigo->matricula}: " . $e->getMessage());
            }
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine(2);

        // ============================================================
        // RESUMO
        // ============================================================
        $this->info('📊 RESUMO DA IMPORTAÇÃO:');
        $this->line("   ✅ Novos membros importados: {$importados}");
        $this->line("   🔄 Membros já existentes (mantidos): {$atualizados}");
        $this->line("   ❌ Erros: {$erros}");
        $this->line("   📋 Total processado: {$total}");
        
        if ($erros > 0) {
            $this->newLine();
            $this->warn('⚠️ Erros encontrados:');
            
            foreach (array_slice($errosLista, 0, 10) as $erro) {
                $this->line("   ❌ Matrícula {$erro['matricula']}: {$erro['erro']}");
            }
            
            if (count($errosLista) > 10) {
                $this->line("   ... e mais " . (count($errosLista) - 10) . " erros");
            }
            
            $this->newLine();
            $this->warn('⚠️ Verifique os logs para mais detalhes: storage/logs/laravel.log');
        }

        // ============================================================
        // ESTATÍSTICAS FINAIS
        // ============================================================
        $this->newLine();
        $this->info('📊 ESTATÍSTICAS FINAIS:');
        
        $totalNovo = Filiado::count();
        $ativos = Filiado::where('status', 'ativo')->count();
        $inativos = Filiado::where('status', '!=', 'ativo')->count();
        $comSenha = Filiado::whereNotNull('password')->count();
        $semSenha = Filiado::whereNull('password')->count();
        
        $this->line("   Total no banco novo: {$totalNovo}");
        $this->line("   Membros ativos: {$ativos}");
        $this->line("   Membros inativos: {$inativos}");
        $this->line("   Com senha: {$comSenha}");
        $this->line("   Sem senha: {$semSenha}");
        
        $this->newLine();
        $this->info('✅ IMPORTAÇÃO CONCLUÍDA COM SUCESSO! 🎉');
        $this->info('📝 Senha padrão para todos os membros: 123456');
    }
}