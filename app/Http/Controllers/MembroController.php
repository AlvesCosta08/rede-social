<?php

namespace App\Http\Controllers;

use App\Models\Filiado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MembroController extends Controller
{
    /**
     * Listar membros com busca
     */
    public function index(Request $request)
    {
        $query = Filiado::on('mysql')->where('status', 'ativo');

        // Busca por nome, matrícula, email, cidade
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nome', 'LIKE', "%{$search}%")
                  ->orWhere('matricula', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('cidade', 'LIKE', "%{$search}%")
                  ->orWhere('telefone', 'LIKE', "%{$search}%");
            });
        }

        // Filtro por função
        if ($request->filled('funcao')) {
            $query->where('funcao', $request->funcao);
        }

        // Filtro por cidade
        if ($request->filled('cidade')) {
            $query->where('cidade', 'LIKE', "%{$request->cidade}%");
        }

        // Filtro por congregação
        if ($request->filled('congregacao')) {
            $query->where('congregacao', 'LIKE', "%{$request->congregacao}%");
        }

        // Ordenação
        $ordenar = $request->get('ordenar', 'nome');
        $direcao = $request->get('direcao', 'asc');
        
        if (in_array($ordenar, ['nome', 'matricula', 'cidade', 'funcao', 'dataNascimento'])) {
            $query->orderBy($ordenar, $direcao);
        } else {
            $query->orderBy('nome', 'asc');
        }

        $membros = $query->paginate(20)->withQueryString();

        // Buscar funções disponíveis para filtro
        $funcoes = Filiado::on('mysql')
            ->where('status', 'ativo')
            ->whereNotNull('funcao')
            ->distinct()
            ->pluck('funcao')
            ->sort();

        $cidades = Filiado::on('mysql')
            ->where('status', 'ativo')
            ->whereNotNull('cidade')
            ->distinct()
            ->pluck('cidade')
            ->sort();

        $congregacoes = Filiado::on('mysql')
            ->where('status', 'ativo')
            ->whereNotNull('congregacao')
            ->distinct()
            ->pluck('congregacao')
            ->sort();

        return view('membros.index', compact('membros', 'funcoes', 'cidades', 'congregacoes'));
    }

    /**
     * Buscar membros (AJAX)
     */
    public function buscar(Request $request)
    {
        $request->validate([
            'termo' => 'required|string|min:2'
        ]);

        $termo = $request->termo;
        
        $membros = Filiado::on('mysql')
            ->where('status', 'ativo')
            ->where(function($q) use ($termo) {
                $q->where('nome', 'LIKE', "%{$termo}%")
                  ->orWhere('matricula', 'LIKE', "%{$termo}%")
                  ->orWhere('email', 'LIKE', "%{$termo}%")
                  ->orWhere('telefone', 'LIKE', "%{$termo}%")
                  ->orWhere('cidade', 'LIKE', "%{$termo}%");
            })
            ->limit(10)
            ->get(['matricula', 'nome', 'foto', 'funcao', 'cidade']);

        return response()->json([
            'success' => true,
            'membros' => $membros->map(function($membro) {
                return [
                    'matricula' => $membro->matricula,
                    'nome' => $membro->nome,
                    'foto_url' => $membro->foto_url,
                    'funcao' => $membro->funcao ?? 'Membro',
                    'cidade' => $membro->cidade,
                    'url' => route('perfil.show', $membro->matricula)
                ];
            })
        ]);
    }

    /**
     * Autocomplete para busca rápida
     */
    public function autocomplete(Request $request)
    {
        $termo = $request->get('q', '');
        
        if (strlen($termo) < 2) {
            return response()->json([]);
        }

        $membros = Filiado::on('mysql')
            ->where('status', 'ativo')
            ->where(function($q) use ($termo) {
                $q->where('nome', 'LIKE', "%{$termo}%")
                  ->orWhere('matricula', 'LIKE', "%{$termo}%");
            })
            ->limit(10)
            ->get(['matricula', 'nome', 'foto']);

        return response()->json(
            $membros->map(function($membro) {
                return [
                    'id' => $membro->matricula,
                    'text' => "{$membro->matricula} - {$membro->nome}",
                    'nome' => $membro->nome,
                    'foto' => $membro->foto_url
                ];
            })
        );
    }

    /**
     * Mostrar carteira digital do usuário logado
     */
    public function meuCartao()
    {
        $user = Auth::user(); // ✅ USA AUTH
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Faça login para ver sua carteira.');
        }

        return $this->cartao($user->matricula);
    }

    /**
     * Mostrar carteira digital de um membro específico
     */
    public function cartao($matricula)
    {
        $membro = Filiado::on('mysql')->where('matricula', $matricula)->firstOrFail();
        
        // ✅ VERIFICA PERMISSÃO: só o próprio membro ou admin pode ver
        $user = Auth::user();
        
        if (!$user || ($user->matricula !== $membro->matricula && !$user->isAdmin())) {
            abort(403, 'Você não tem permissão para ver esta carteira.');
        }

        return view('membros.cartao', compact('membro'));
    }

    /**
     * Buscar membros por proximidade (geolocalização)
     */
    public function buscarPorProximidade(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'raio' => 'nullable|numeric|min:1|max:100'
        ]);

        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $raio = $request->get('raio', 10); // km

        // Busca membros com coordenadas
        $membros = Filiado::on('mysql')
            ->where('status', 'ativo')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->selectRaw("
                matricula, 
                nome, 
                foto, 
                funcao,
                cidade,
                latitude,
                longitude,
                (6371 * ACOS(
                    COS(RADIANS(?)) * 
                    COS(RADIANS(latitude)) * 
                    COS(RADIANS(longitude) - RADIANS(?)) + 
                    SIN(RADIANS(?)) * 
                    SIN(RADIANS(latitude))
                )) AS distancia
            ", [$latitude, $longitude, $latitude])
            ->having('distancia', '<=', $raio)
            ->orderBy('distancia')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'membros' => $membros->map(function($membro) {
                return [
                    'matricula' => $membro->matricula,
                    'nome' => $membro->nome,
                    'foto_url' => $membro->foto_url,
                    'funcao' => $membro->funcao ?? 'Membro',
                    'cidade' => $membro->cidade,
                    'distancia' => round($membro->distancia, 2) . ' km',
                    'url' => route('perfil.show', $membro->matricula)
                ];
            })
        ]);
    }

    /**
     * Atualizar localização do membro
     */
    public function atualizarLocalizacao(Request $request)
    {
        $user = Auth::user(); // ✅ USA AUTH
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Usuário não autenticado.'], 401);
        }

        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180'
        ]);

        try {
            $user->latitude = $request->latitude;
            $user->longitude = $request->longitude;
            $user->save();

            Log::info('Localização atualizada', [
                'matricula' => $user->matricula,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Localização atualizada com sucesso!'
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao atualizar localização', [
                'matricula' => $user->matricula,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar localização.'
            ], 500);
        }
    }

    /**
     * Sugestões de membros para seguir
     */
    public function sugestoes()
    {
        $user = Auth::user(); // ✅ USA AUTH
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Usuário não autenticado.'], 401);
        }

        // Busca membros que o usuário ainda não segue
        $seguindo = $user->seguindo()->pluck('matricula')->toArray();
        $seguindo[] = $user->matricula; // Não sugerir ele mesmo

        $sugestoes = Filiado::on('mysql')
            ->where('status', 'ativo')
            ->whereNotIn('matricula', $seguindo)
            ->inRandomOrder()
            ->limit(10)
            ->get(['matricula', 'nome', 'foto', 'funcao', 'cidade']);

        return response()->json([
            'success' => true,
            'sugestoes' => $sugestoes->map(function($membro) {
                return [
                    'matricula' => $membro->matricula,
                    'nome' => $membro->nome,
                    'foto_url' => $membro->foto_url,
                    'funcao' => $membro->funcao ?? 'Membro',
                    'cidade' => $membro->cidade,
                    'url' => route('perfil.show', $membro->matricula)
                ];
            })
        ]);
    }

    /**
     * Estatísticas de membros
     */
    public function estatisticas()
    {
        $total = Filiado::on('mysql')->count();
        $ativos = Filiado::on('mysql')->where('status', 'ativo')->count();
        $inativos = Filiado::on('mysql')->where('status', 'inativo')->count();
        
        $porFuncao = Filiado::on('mysql')
            ->where('status', 'ativo')
            ->selectRaw('funcao, count(*) as total')
            ->groupBy('funcao')
            ->orderBy('total', 'desc')
            ->get();

        $porCidade = Filiado::on('mysql')
            ->where('status', 'ativo')
            ->selectRaw('cidade, count(*) as total')
            ->whereNotNull('cidade')
            ->groupBy('cidade')
            ->orderBy('total', 'desc')
            ->limit(10)
            ->get();

        $porCongregacao = Filiado::on('mysql')
            ->where('status', 'ativo')
            ->selectRaw('congregacao, count(*) as total')
            ->whereNotNull('congregacao')
            ->groupBy('congregacao')
            ->orderBy('total', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'estatisticas' => [
                'total' => $total,
                'ativos' => $ativos,
                'inativos' => $inativos,
                'por_funcao' => $porFuncao,
                'por_cidade' => $porCidade,
                'por_congregacao' => $porCongregacao
            ]
        ]);
    }

    /**
     * Exportar membros (CSV)
     */
    public function exportar(Request $request)
    {
        $user = Auth::user(); // ✅ USA AUTH
        
        if (!$user || !$user->isAdmin()) {
            abort(403, 'Apenas administradores podem exportar dados.');
        }

        $query = Filiado::on('mysql')->where('status', 'ativo');

        if ($request->filled('funcao')) {
            $query->where('funcao', $request->funcao);
        }

        if ($request->filled('cidade')) {
            $query->where('cidade', 'LIKE', "%{$request->cidade}%");
        }

        $membros = $query->orderBy('nome')->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="membros_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function() use ($membros) {
            $file = fopen('php://output', 'w');
            
            // Cabeçalho
            fputcsv($file, [
                'Matrícula', 
                'Nome', 
                'Função', 
                'Email', 
                'Telefone',
                'Cidade',
                'UF',
                'Congregação',
                'Status',
                'Data Nascimento',
                'Data Cadastro'
            ]);

            // Dados
            foreach ($membros as $membro) {
                fputcsv($file, [
                    $membro->matricula,
                    $membro->nome,
                    $membro->funcao ?? 'Membro',
                    $membro->email ?? '',
                    $membro->telefone ?? '',
                    $membro->cidade ?? '',
                    $membro->uf ?? '',
                    $membro->congregacao ?? '',
                    $membro->status ?? 'ativo',
                    $membro->dataNascimento?->format('d/m/Y') ?? '',
                    $membro->datCadastro?->format('d/m/Y') ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Mostrar perfil de um membro (show)
     */
    public function show($matricula)
    {
        $membro = Filiado::on('mysql')
            ->with(['publicacoes' => function($query) {
                $query->orderBy('created_at', 'desc')->limit(10);
            }])
            ->where('matricula', $matricula)
            ->firstOrFail();

        // Verifica permissão para ver dados privados
        $user = Auth::user();
        $podeVerPrivado = $user && ($user->matricula === $membro->matricula || $user->isAdmin());
        
        // Se não for admin nem o próprio, mostra apenas dados públicos
        if (!$podeVerPrivado && !$membro->privacidade) {
            // Esconde dados sensíveis
            $membro->makeHidden(['email', 'telefone', 'documento', 'endereco']);
        }

        return view('membros.show', compact('membro', 'podeVerPrivado'));
    }
}