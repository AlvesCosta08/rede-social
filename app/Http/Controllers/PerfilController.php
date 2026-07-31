<?php

namespace App\Http\Controllers;

use App\Models\Filiado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class PerfilController extends Controller
{
    public function show($matricula)
    {
        $membroLogado = Filiado::find(Session::get('membro_logado'));
        $perfil = Filiado::with('publicacoes')->findOrFail($matricula);

        return view('perfil.show', compact('perfil', 'membroLogado'));
    }

    public function edit()
    {
        $membro = Filiado::find(Session::get('membro_logado'));
        return view('perfil.edit', compact('membro'));
    }

    public function update(Request $request)
    {
        $membro = Filiado::find(Session::get('membro_logado'));

        $request->validate([
            'nome' => 'required|string|max:255',
            'telefone' => 'required|string|max:20',
            'endereco' => 'required|string|max:255',
            'cidade' => 'required|string|max:100',
            'uf' => 'required|string|max:2',
            'bio' => 'nullable|string|max:500',
        ]);

        $membro->update([
            'nome' => $request->nome,
            'nome_carteira' => $request->nome,
            'telefone' => $request->telefone,
            'endereco' => $request->endereco,
            'cidade' => $request->cidade,
            'uf' => $request->uf,
            'bio' => $request->bio,
            'privacidade' => $request->has('privacidade'),
        ]);

        Session::put('membro_nome', $membro->nome);

        return redirect()->route('perfil.show', $membro->matricula)
                         ->with('success', 'Perfil atualizado com sucesso!');
    }

    public function uploadFoto(Request $request)
    {
        $membro = Filiado::find(Session::get('membro_logado'));

        $request->validate([
            'foto' => 'required|image|max:2048|mimes:jpeg,png,jpg'
        ]);

        if ($request->hasFile('foto')) {
            if ($membro->foto && Storage::exists('public/fotos/' . $membro->foto)) {
                Storage::delete('public/fotos/' . $membro->foto);
            }

            $foto = $request->file('foto');
            $nome = time() . '_' . $membro->matricula . '.' . $foto->getClientOriginalExtension();
            $path = $foto->storeAs('public/fotos', $nome);

            $membro->update(['foto' => $nome]);

            return back()->with('success', 'Foto atualizada com sucesso!');
        }

        return back()->with('error', 'Erro ao fazer upload da foto.');
    }
}