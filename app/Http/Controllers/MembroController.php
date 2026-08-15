<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Membro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MembroController extends Controller
{
    /**
     * Listar membros ativos (usuário comum)
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Faça login para continuar.');
        }

        $query = Membro::on('mysql')->where('status', 'ativo');

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

        if ($request->filled('funcao')) {
            $query->where('funcao', $request->funcao);
        }

        if ($request->filled('cidade')) {
            $query->where('cidade', 'LIKE', "%{$request->cidade}%");
        }

        if ($request->filled('congregacao')) {
            $query->where('congregacao', 'LIKE', "%{$request->congregacao}%");
        }

        $ordenar = $request->get('ordenar', 'nome');
        $direcao = $request->get('direcao', 'asc');
        
        if (in_array($ordenar, ['nome', 'matricula', 'cidade', 'funcao', 'dataNascimento'])) {
            $query->orderBy($ordenar, $direcao);
        } else {
            $query->orderBy('nome', 'asc');
        }

        $membros = $query->paginate(20)->withQueryString();

        $funcoes = Membro::on('mysql')
            ->where('status', 'ativo')
            ->whereNotNull('funcao')
            ->distinct()
            ->pluck('funcao')
            ->sort();

        $cidades = Membro::on('mysql')
            ->where('status', 'ativo')
            ->whereNotNull('cidade')
            ->distinct()
            ->pluck('cidade')
            ->sort();

        $congregacoes = Membro::on('mysql')
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
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Não autenticado.'], 401);
        }

        $request->validate([
            'termo' => 'required|string|min:2'
        ]);

        $termo = $request->termo;
        
        $membros = Membro::on('mysql')
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
                    'url' => route('membros.show', $membro->matricula)
                ];
            })
        ]);
    }

    /**
     * Autocomplete para busca rápida
     */
    public function autocomplete(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json([]);
        }

        $termo = $request->get('q', '');
        
        if (strlen($termo) < 2) {
            return response()->json([]);
        }

        $membros = Membro::on('mysql')
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
        $user = Auth::user();
        
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
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Faça login para continuar.');
        }

        $membro = Membro::on('mysql')->where('matricula', $matricula)->firstOrFail();
        
        if (!$user->pode('ver_membro', $membro) && $user->matricula !== $membro->matricula) {
            abort(403, 'Você não tem permissão para ver esta carteira.');
        }

        return view('membros.cartao', compact('membro'));
    }

    /**
     * Buscar membros por proximidade (geolocalização)
     */
    public function buscarPorProximidade(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Não autenticado.'], 401);
        }

        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'raio' => 'nullable|numeric|min:1|max:100'
        ]);

        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $raio = $request->get('raio', 10);

        $membros = Membro::on('mysql')
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
                    'url' => route('membros.show', $membro->matricula)
                ];
            })
        ]);
    }

    /**
     * Atualizar localização do membro
     */
    public function atualizarLocalizacao(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Não autenticado.'], 401);
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
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Não autenticado.'], 401);
        }

        $seguindo = $user->seguindo()->pluck('matricula')->toArray();
        $seguindo[] = $user->matricula;

        $sugestoes = Membro::on('mysql')
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
                    'url' => route('membros.show', $membro->matricula)
                ];
            })
        ]);
    }

    /**
     * Mostrar perfil de um membro
     */
    public function show($matricula)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Faça login para continuar.');
        }

        $membro = Membro::on('mysql')
            ->with(['publicacoes' => function($query) {
                $query->orderBy('created_at', 'desc')->limit(10);
            }])
            ->where('matricula', $matricula)
            ->firstOrFail();

        $podeVerPrivado = $user->matricula === $membro->matricula || $user->pode('ver_membro', $membro);
        
        if (!$podeVerPrivado && $membro->privacidade) {
            $membro->makeHidden(['email', 'telefone', 'documento', 'endereco']);
        }

        return view('membros.show', compact('membro', 'podeVerPrivado'));
    }
}