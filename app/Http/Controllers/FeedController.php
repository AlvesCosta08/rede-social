<?php

namespace App\Http\Controllers;

use App\Models\Filiado;
use App\Models\Publicacao;
use App\Models\Comentario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class FeedController extends Controller
{
    private function getMembroLogado()
    {
        return Filiado::find(Session::get('membro_logado'));
    }

    public function index()
    {
        $membro = $this->getMembroLogado();
        if (!$membro) return redirect()->route('login');

        $publicacoes = Publicacao::with(['autor', 'comentarios.autor'])
                                ->orderBy('created_at', 'desc')
                                ->get();

        return view('feed.index', compact('membro', 'publicacoes'));
    }

    public function store(Request $request)
    {
        $membro = $this->getMembroLogado();
        if (!$membro) return redirect()->route('login');

        $request->validate(['conteudo' => 'required|string|max:500']);

        Publicacao::create([
            'filiado_matricula' => $membro->matricula,
            'conteudo' => $request->conteudo
        ]);

        return back()->with('success', 'Publicação criada com sucesso!');
    }

    public function curtir($id)
    {
        $membro = $this->getMembroLogado();
        if (!$membro) return redirect()->route('login');

        $publicacao = Publicacao::findOrFail($id);

        if ($publicacao->isCurtidoPor($membro)) {
            $publicacao->curtidas()->detach($membro->matricula);
            $publicacao->decrement('curtidas_count');
        } else {
            $publicacao->curtidas()->attach($membro->matricula);
            $publicacao->increment('curtidas_count');
        }

        return back();
    }

    public function comentar(Request $request, $id)
    {
        $membro = $this->getMembroLogado();
        if (!$membro) return redirect()->route('login');

        $request->validate(['conteudo' => 'required|string|max:300']);

        Comentario::create([
            'filiado_matricula' => $membro->matricula,
            'publicacao_id' => $id,
            'conteudo' => $request->conteudo
        ]);

        return back()->with('success', 'Comentário adicionado!');
    }

    public function delete($id)
    {
        $membro = $this->getMembroLogado();
        if (!$membro) return redirect()->route('login');

        $publicacao = Publicacao::findOrFail($id);

        if ($publicacao->filiado_matricula == $membro->matricula) {
            $publicacao->delete();
            return back()->with('success', 'Publicação removida!');
        }

        return back()->with('error', 'Você não pode remover esta publicação.');
    }
}