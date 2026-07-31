@extends('layouts.app')

@section('title', 'Sair da Igreja')

@section('content')
<div style="max-width: 500px; margin: 40px auto; text-align: center;">
    <div style="background: var(--card-bg); border-radius: 16px; padding: 40px; box-shadow: var(--shadow); border: 1px solid var(--border-color);">
        <div style="font-size: 4rem; margin-bottom: 20px;">😢</div>
        <h2 style="color: #f44336; margin-bottom: 15px;">Sair da Igreja</h2>
        <p style="margin-bottom: 10px;">Sentimos muito que você esteja saindo.</p>
        <p style="margin-bottom: 25px; opacity: 0.7; font-size: 0.9rem;">
            Todos os seus dados serão anonimizados e suas publicações removidas.
            Esta ação é irreversível.
        </p>

        <form method="POST" action="{{ route('sair.igreja.confirmar') }}">
            @csrf
            <div style="margin-bottom: 20px; text-align: left;">
                <label style="display: block; font-weight: 600; margin-bottom: 5px;">
                    Digite seu nome completo para confirmar:
                </label>
                <input type="text" name="confirmacao" placeholder="{{ $membro->nome }}"
                       style="width: 100%; padding: 12px; border: 2px solid #f44336; border-radius: 10px; background: var(--bg); color: var(--text);">
            </div>

            <div style="display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                <a href="{{ route('feed') }}" style="padding: 12px 30px; background: #2c3e50; color: white; border-radius: 10px; text-decoration: none; font-weight: 600;">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
                <button type="submit" style="padding: 12px 30px; background: #f44336; color: white; border: none; border-radius: 10px; font-weight: 700; cursor: pointer;">
                    <i class="fas fa-sign-out-alt"></i> Confirmar Saída
                </button>
            </div>
        </form>
    </div>
</div>
@endsection