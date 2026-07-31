@extends('layouts.app')

@section('title', 'Perfil - ' . $perfil->nome)

@section('styles')
<style>
    .perfil-container { max-width: 700px; margin: 0 auto; }
    .perfil-header {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 30px;
        box-shadow: var(--shadow);
        border: 1px solid var(--border-color);
        text-align: center;
        margin-bottom: 25px;
    }
    .perfil-header .avatar-grande {
        width: 100px; height: 100px; border-radius: 50%;
        background: linear-gradient(135deg, var(--destaque), #d4af37);
        display: flex; align-items: center; justify-content: center;
        font-size: 2.5rem; font-weight: 700; color: #1a1a2e;
        margin: 0 auto 15px; border: 4px solid var(--destaque);
        overflow: hidden;
    }
    .perfil-header .avatar-grande img { width: 100%; height: 100%; object-fit: cover; }
    .perfil-header h1 { font-size: 1.5rem; margin-bottom: 4px; }
    .perfil-header .funcao { color: var(--destaque); font-weight: 600; font-size: 1rem; }
    .perfil-header .status {
        display: inline-block; padding: 4px 16px; border-radius: 20px;
        font-size: 0.8rem; font-weight: 600; margin-top: 8px;
        background: {{ $perfil->status_icon }}; color: white;
    }
    .perfil-header .bio { margin-top: 15px; padding: 15px; background: var(--bg); border-radius: 10px; font-style: italic; opacity: 0.8; }
    .perfil-header .info-grid {
        display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 15px; margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-color);
    }
    .perfil-header .info-grid .item { text-align: center; }
    .perfil-header .info-grid .item .label { font-size: 0.7rem; opacity: 0.5; text-transform: uppercase; letter-spacing: 1px; }
    .perfil-header .info-grid .item .value { font-weight: 600; font-size: 0.9rem; margin-top: 2px; }
    .perfil-header .btn-cartao {
        display: inline-block; margin-top: 15px; padding: 10px 25px;
        background: var(--destaque); color: #1a1a2e; text-decoration: none;
        border-radius: 10px; font-weight: 600; transition: 0.3s;
    }
    .perfil-header .btn-cartao:hover { transform: scale(1.05); box-shadow: 0 4px 15px rgba(201, 168, 76, 0.3); }
    .perfil-header .btn-editar {
        display: inline-block; margin-top: 15px; padding: 10px 25px;
        background: #2c3e50; color: white; text-decoration: none;
        border-radius: 10px; font-weight: 600; transition: 0.3s; margin-left: 10px;
    }
    .perfil-header .btn-editar:hover { transform: scale(1.05); }

    .perfil-publicacoes h3 { margin-bottom: 15px; display: flex; align-items: center; gap: 8px; }
    .perfil-publicacoes .publicacao-mini {
        background: var(--card-bg); border-radius: 12px; padding: 15px 18px;
        box-shadow: var(--shadow); border: 1px solid var(--border-color);
        margin-bottom: 12px;
    }
    .perfil-publicacoes .publicacao-mini .conteudo { font-size: 0.9rem; line-height: 1.5; }
    .perfil-publicacoes .publicacao-mini .data { font-size: 0.7rem; opacity: 0.5; margin-top: 6px; }
    .perfil-publicacoes .publicacao-mini .stats { display: flex; gap: 15px; margin-top: 8px; font-size: 0.8rem; opacity: 0.6; }
    .sem-publicacoes { text-align: center; padding: 40px; opacity: 0.5; }
    .sem-publicacoes i { font-size: 2rem; margin-bottom: 10px; color: var(--destaque); }

    @media (max-width: 480px) { .perfil-header .info-grid { grid-template-columns: 1fr 1fr; } }
</style>
@endsection

@section('content')
<div class="perfil-container">
    <div class="perfil-header">
        <div class="avatar-grande">
            @if($perfil->foto)
                <img src="{{ asset('storage/fotos/' . $perfil->foto) }}" alt="Foto">
            @else
                {{ substr($perfil->nome, 0, 1) }}
            @endif
        </div>
        <h1>{{ $perfil->nome }}</h1>
        <div class="funcao"><i class="fas fa-user-tie"></i> {{ $perfil->funcao_formatada }}</div>
        <div class="status"><i class="fas fa-circle" style="font-size: 0.6rem;"></i> {{ strtoupper($perfil->status ?? 'INATIVO') }}</div>

        @if($perfil->bio)
            <div class="bio"><i class="fas fa-quote-left" style="opacity: 0.5;"></i> {{ $perfil->bio }}</div>
        @endif

        <div class="info-grid">
            <div class="item"><div class="label">Matrícula</div><div class="value">#{{ $perfil->matricula }}</div></div>
            <div class="item"><div class="label">Congregação</div><div class="value">{{ $perfil->congregacao ?: 'Não informada' }}</div></div>
            <div class="item"><div class="label">Ingresso</div><div class="value">{{ $perfil->datCadastro ? $perfil->datCadastro->format('d/m/Y') : '-' }}</div></div>
            <div class="item"><div class="label">Publicações</div><div class="value">{{ $perfil->publicacoes->count() }}</div></div>
        </div>

        <div>
            <a href="{{ route('membro.cartao', $perfil->matricula) }}" class="btn-cartao">
                <i class="fas fa-id-card"></i> Ver Cartão
            </a>
            @if($membroLogado && $membroLogado->matricula == $perfil->matricula)
                <a href="{{ route('perfil.edit') }}" class="btn-editar">
                    <i class="fas fa-edit"></i> Editar Perfil
                </a>
            @endif
        </div>
    </div>

    <div class="perfil-publicacoes">
        <h3><i class="fas fa-comment-dots"></i> Publicações de {{ $perfil->nome }}</h3>
        @if($perfil->publicacoes->count() > 0)
            @foreach($perfil->publicacoes as $pub)
                <div class="publicacao-mini">
                    <div class="conteudo">{{ $pub->conteudo }}</div>
                    <div class="data">{{ $pub->created_at->format('d/m/Y H:i') }}</div>
                    <div class="stats">
                        <span><i class="fas fa-heart"></i> {{ $pub->curtidas_count }}</span>
                        <span><i class="fas fa-comment"></i> {{ $pub->comentarios->count() }}</span>
                    </div>
                </div>
            @endforeach
        @else
            <div class="sem-publicacoes"><i class="fas fa-comment-slash"></i><p>Este membro ainda não publicou nada.</p></div>
        @endif
    </div>
</div>
@endsection