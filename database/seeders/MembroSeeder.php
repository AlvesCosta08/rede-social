<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class MembroSeeder extends Seeder
{
    public function run(): void
    {
        // Pega a lista de colunas da tabela
        $colunas = DB::getSchemaBuilder()->getColumnListing('filiado');
        
        // Lista de membros para criar
        $membros = [
            [
                'matricula' => 1001,
                'nome' => 'João Silva',
                'nome_carteira' => 'João Silva',
                'email' => 'joao@teste.com',
                'telefone' => '(85) 99999-8888',
                'documento' => '222.222.222-22',
                'estadoCivil' => 'Casado(a)',
                'dataNascimento' => '1985-05-15',
                'mae' => 'Maria Silva',
                'pai' => 'José Silva',
                'endereco' => 'Rua das Flores, 200',
                'cidade' => 'Maranguape',
                'uf' => 'CE',
                'congregacao' => 'NOVO MARANGUAPE 3',
                'funcao' => 'Membro',
                'status' => 'ativo',
                'datCadastro' => Carbon::now(),
                'dataBatismo' => '1995-05-15',
                'password' => Hash::make('123456'),
                'bio' => 'Membro da igreja desde 1995. Amo servir ao Senhor!',
                'privacidade' => false,
            ],
            [
                'matricula' => 1002,
                'nome' => 'Maria Oliveira',
                'nome_carteira' => 'Maria Oliveira',
                'email' => 'maria@teste.com',
                'telefone' => '(85) 98888-7777',
                'documento' => '333.333.333-33',
                'estadoCivil' => 'Casada',
                'dataNascimento' => '1990-08-20',
                'mae' => 'Ana Oliveira',
                'pai' => 'Carlos Oliveira',
                'endereco' => 'Rua dos Sonhos, 150',
                'cidade' => 'Maranguape',
                'uf' => 'CE',
                'congregacao' => 'SEDE',
                'funcao' => 'Diácono',
                'status' => 'ativo',
                'datCadastro' => Carbon::now(),
                'dataBatismo' => '2000-08-20',
                'password' => Hash::make('123456'),
                'bio' => 'Diácona desde 2010. Servindo com alegria!',
                'privacidade' => false,
            ],
            [
                'matricula' => 1003,
                'nome' => 'Pedro Santos',
                'nome_carteira' => 'Pedro Santos',
                'email' => 'pedro@teste.com',
                'telefone' => '(85) 97777-6666',
                'documento' => '444.444.444-44',
                'estadoCivil' => 'Solteiro',
                'dataNascimento' => '1995-12-01',
                'mae' => 'Lúcia Santos',
                'pai' => 'Francisco Santos',
                'endereco' => 'Av. Central, 300',
                'cidade' => 'Maranguape',
                'uf' => 'CE',
                'congregacao' => 'NOVO MARANGUAPE 3',
                'funcao' => 'Obreiro',
                'status' => 'ativo',
                'datCadastro' => Carbon::now(),
                'dataBatismo' => '2005-12-01',
                'password' => Hash::make('123456'),
                'bio' => 'Jovem obreiro, apaixonado pela palavra de Deus.',
                'privacidade' => true,
            ],
            [
                'matricula' => 1004,
                'nome' => 'Ana Costa',
                'nome_carteira' => 'Ana Costa',
                'email' => 'ana@teste.com',
                'telefone' => '(85) 96666-5555',
                'documento' => '555.555.555-55',
                'estadoCivil' => 'Viúva',
                'dataNascimento' => '1970-03-10',
                'mae' => 'Rosa Costa',
                'pai' => 'Antônio Costa',
                'endereco' => 'Rua da Paz, 50',
                'cidade' => 'Maranguape',
                'uf' => 'CE',
                'congregacao' => 'SEDE',
                'funcao' => 'Presbítero',
                'status' => 'ativo',
                'datCadastro' => Carbon::now(),
                'dataBatismo' => '1985-03-10',
                'password' => Hash::make('123456'),
                'bio' => 'Presbítera há 20 anos. Servindo com dedicação.',
                'privacidade' => false,
            ],
            [
                'matricula' => 1005,
                'nome' => 'Carlos Lima',
                'nome_carteira' => 'Carlos Lima',
                'email' => 'carlos@teste.com',
                'telefone' => '(85) 95555-4444',
                'documento' => '666.666.666-66',
                'estadoCivil' => 'Solteiro',
                'dataNascimento' => '2000-07-25',
                'mae' => 'Sandra Lima',
                'pai' => 'Roberto Lima',
                'endereco' => 'Rua Nova, 80',
                'cidade' => 'Maranguape',
                'uf' => 'CE',
                'congregacao' => 'NOVO MARANGUAPE 3',
                'funcao' => 'Membro',
                'status' => 'ativo',
                'datCadastro' => Carbon::now(),
                'dataBatismo' => '2010-07-25',
                'password' => Hash::make('123456'),
                'bio' => 'Jovem membro, participante do grupo de jovens.',
                'privacidade' => true,
            ],
            [
                'matricula' => 1006,
                'nome' => 'Beatriz Nunes',
                'nome_carteira' => 'Beatriz Nunes',
                'email' => 'beatriz@teste.com',
                'telefone' => '(85) 94444-3333',
                'documento' => '777.777.777-77',
                'estadoCivil' => 'Casada',
                'dataNascimento' => '1988-11-30',
                'mae' => 'Helena Nunes',
                'pai' => 'Paulo Nunes',
                'endereco' => 'Rua das Palmeiras, 120',
                'cidade' => 'Maranguape',
                'uf' => 'CE',
                'congregacao' => 'SEDE',
                'funcao' => 'Evangelista',
                'status' => 'ativo',
                'datCadastro' => Carbon::now(),
                'dataBatismo' => '1998-11-30',
                'password' => Hash::make('123456'),
                'bio' => 'Evangelista, missionária e amante das almas.',
                'privacidade' => false,
            ],
            [
                'matricula' => 1007,
                'nome' => 'Felipe Araújo',
                'nome_carteira' => 'Felipe Araújo',
                'email' => 'felipe@teste.com',
                'telefone' => '(85) 93333-2222',
                'documento' => '888.888.888-88',
                'estadoCivil' => 'Solteiro',
                'dataNascimento' => '1992-04-18',
                'mae' => 'Cristina Araújo',
                'pai' => 'Marcos Araújo',
                'endereco' => 'Av. das Águas, 45',
                'cidade' => 'Maranguape',
                'uf' => 'CE',
                'congregacao' => 'NOVO MARANGUAPE 3',
                'funcao' => 'Membro',
                'status' => 'inativo',
                'datCadastro' => Carbon::now(),
                'dataBatismo' => '2002-04-18',
                'password' => Hash::make('123456'),
                'bio' => 'Membro ausente, orando por seu retorno.',
                'privacidade' => true,
            ],
            [
                'matricula' => 1008,
                'nome' => 'Patrícia Rocha',
                'nome_carteira' => 'Patrícia Rocha',
                'email' => 'patricia@teste.com',
                'telefone' => '(85) 92222-1111',
                'documento' => '999.999.999-99',
                'estadoCivil' => 'Divorciada',
                'dataNascimento' => '1980-09-05',
                'mae' => 'Teresa Rocha',
                'pai' => 'Jorge Rocha',
                'endereco' => 'Rua do Sol, 200',
                'cidade' => 'Maranguape',
                'uf' => 'CE',
                'congregacao' => 'SEDE',
                'funcao' => 'Auxiliar',
                'status' => 'ativo',
                'datCadastro' => Carbon::now(),
                'dataBatismo' => '1990-09-05',
                'password' => Hash::make('123456'),
                'bio' => 'Auxiliar na obra, sempre disposta a ajudar.',
                'privacidade' => false,
            ],
            [
                'matricula' => 1009,
                'nome' => 'Rafael Sousa',
                'nome_carteira' => 'Rafael Sousa',
                'email' => 'rafael@teste.com',
                'telefone' => '(85) 91111-0000',
                'documento' => '000.000.000-00',
                'estadoCivil' => 'Casado',
                'dataNascimento' => '1975-06-12',
                'mae' => 'Elisabete Sousa',
                'pai' => 'José Sousa',
                'endereco' => 'Rua da Igreja, 10',
                'cidade' => 'Maranguape',
                'uf' => 'CE',
                'congregacao' => 'NOVO MARANGUAPE 3',
                'funcao' => 'Pastor',
                'status' => 'ativo',
                'datCadastro' => Carbon::now(),
                'dataBatismo' => '1985-06-12',
                'password' => Hash::make('123456'),
                'bio' => 'Pastor da igreja há 15 anos. Amando o rebanho.',
                'privacidade' => false,
            ],
            [
                'matricula' => 1010,
                'nome' => 'Cristina Alves',
                'nome_carteira' => 'Cristina Alves',
                'email' => 'cristina@teste.com',
                'telefone' => '(85) 99999-1111',
                'documento' => '111.222.333-44',
                'estadoCivil' => 'Casada',
                'dataNascimento' => '1993-02-28',
                'mae' => 'Marta Alves',
                'pai' => 'Luiz Alves',
                'endereco' => 'Rua das Margaridas, 60',
                'cidade' => 'Maranguape',
                'uf' => 'CE',
                'congregacao' => 'SEDE',
                'funcao' => 'Membro',
                'status' => 'ativo',
                'datCadastro' => Carbon::now(),
                'dataBatismo' => '2003-02-28',
                'password' => Hash::make('123456'),
                'bio' => 'Membro ativa, líder do grupo de oração.',
                'privacidade' => false,
            ],
        ];

        // Insere cada membro
        foreach ($membros as $membro) {
            // Verifica se já existe
            $existe = DB::table('filiado')->where('matricula', $membro['matricula'])->exists();
            
            if (!$existe) {
                // Filtra apenas as colunas que existem
                $dadosFiltrados = [];
                foreach ($membro as $campo => $valor) {
                    if (in_array($campo, $colunas)) {
                        $dadosFiltrados[$campo] = $valor;
                    }
                }
                
                DB::table('filiado')->insert($dadosFiltrados);
            }
        }

        $this->command->info('✅ ' . count($membros) . ' membros criados com sucesso!');
        $this->command->info('📧 Todos os membros têm a senha: 123456');
        
        // Mostra os membros criados
        $this->command->table(
            ['Matrícula', 'Nome', 'Email', 'Função', 'Status'],
            array_map(function($m) {
                return [$m['matricula'], $m['nome'], $m['email'], $m['funcao'], $m['status']];
            }, $membros)
        );
    }
}