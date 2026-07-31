@extends('layouts.app')

@section('title', 'Feed - Conexão Igreja')

@section('styles')
<style>
    .feed-container { max-width: 700px; margin: 0 auto; }
    
    .nova-publicacao {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 20px;
        box-shadow: var(--shadow);
        margin-bottom: 25px;
        border: 1px solid var(--border-color);
    }
    .nova-publicacao .autor-info { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
    .nova-publicacao .autor-info .avatar {
        width: 45px; height: 45px; border-radius: 50%;
        background: var(--destaque);
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 1.2rem; color: #1a1a2e;
    }
    .nova-publicacao .autor-info .nome { font-weight: 600; font-size: 0.95rem; }
    .nova-publicacao .autor-info .funcao { font-size: 0.8rem; opacity: 0.7; }
    .nova-publicacao textarea {
        width: 100%; min-height: 80px; padding: 12px;
        border: 2px solid var(--border-color);
        border-radius: 12px; background: var(--bg);
        color: var(--text); font-size: 0.95rem; resize: vertical;
        transition: 0.3s; font-family: inherit;
    }
    .nova-publicacao textarea:focus { outline: none; border-color: var(--destaque); }
    .nova-publicacao .btn-publicar {
        margin-top: 12px; padding: 10px 25px;
        background: var(--destaque); border: none; border-radius: 10px;
        color: #1a1a2e; font-weight: 700; font-size: 0.9rem;
        cursor: pointer; transition: 0.3s; float: right;
    }
    .nova-publicacao .btn-publicar:hover { transform: scale(1.05); box-shadow: 0 4px 15px rgba(201, 168, 76, 0.3); }
    .clearfix::after { content: ""; clear: both; display: table; }

    .publicacao {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 20px;
        box-shadow: var(--shadow);
        margin-bottom: 20px;
        border: 1px solid var(--border-color);
        transition: 0.3s;
    }
    .publicacao:hover { box-shadow: 0 6px 30px rgba(0,0,0,0.12); }
    .publicacao .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 8px; }
    .publicacao .header .autor {
        display: flex; align-items: center; gap: 12px;
        cursor: pointer; text-decoration: none; color: var(--text);
    }
    .publicacao .header .autor .avatar {
        width: 42px; height: 42px; border-radius: 50%;
        background: var(--destaque);
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; color: #1a1a2e; flex-shrink: 0;
    }
    .publicacao .header .autor .nome { font-weight: 600; font-size: 0.95rem; }
    .publicacao .header .autor .funcao { font-size: 0.75rem; opacity: 0.7; }
    .publicacao .header .data { font-size: 0.75rem; opacity: 0.5; }
    .publicacao .header .btn-delete {
        background: none; border: none; color: #f44336;
        cursor: pointer; font-size: 0.9rem; opacity: 0.5;
        transition: 0.3s; padding: 4px 8px; border-radius: 4px;
    }
    .publicacao .header .btn-delete:hover { opacity: 1; background: rgba(244, 67, 54, 0.1); }
    .publicacao .conteudo { font-size: 0.95rem; line-height: 1.6; margin-bottom: 15px; word-wrap: break-word; white-space: pre-wrap; }
    .publicacao .acoes { display: flex; gap: 20px; padding-top: 12px; border-top: 1px solid var(--border-color); flex-wrap: wrap; }
    .publicacao .acoes button {
        background: none; border: none; cursor: pointer;
        font-size: 0.9rem; display: flex; align-items: center; gap: 6px;
        color: var(--text); opacity: 0.7; transition: 0.3s;
        padding: 4px 10px; border-radius: 8px;
    }
    .publicacao .acoes button:hover { opacity: 1; background: rgba(201, 168, 76, 0.1); }
    .publicacao .acoes button.curtido { color: #f44336; opacity: 1; }
    .publicacao .acoes button.curtido i { animation: likeAnim 0.3s ease; }
    @keyframes likeAnim { 0% { transform: scale(1); } 50% { transform: scale(1.3); } 100% { transform: scale(1); } }

    .publicacao .comentarios { margin-top: 15px; padding-top: 15px; border-top: 1px solid var(--border-color); }
    .publicacao .comentarios .comentario { display: flex; gap: 10px; margin-bottom: 10px; padding: 8px 12px; background: var(--bg); border-radius: 10px; }
    .publicacao .comentarios .comentario .avatar-mini {
        width: 28px; height: 28px; border-radius: 50%;
        background: var(--destaque);
        display: flex; align-items: center; justify-content: center;
        font-size: 0.7rem; font-weight: 700; color: #1a1a2e; flex-shrink: 0;
    }
    .publicacao .comentarios .comentario .conteudo { font-size: 0.85rem; line-height: 1.4; margin: 0; }
    .publicacao .comentarios .comentario .conteudo .nome-autor { font-weight: 600; margin-right: 6px; }
    .publicacao .comentarios .form-comentario { display: flex; gap: 10px; margin-top: 10px; }
    .publicacao .comentarios .form-comentario input {
        flex: 1; padding: 8px 14px;
        border: 2px solid var(--border-color); border-radius: 20px;
        background: var(--bg); color: var(--text); font-size: 0.85rem; transition: 0.3s;
    }
    .publicacao .comentarios .form-comentario input:focus { outline: none; border-color: var(--destaque); }
    .publicacao .comentarios .form-comentario button {
        padding: 8px 16px; border: none; border-radius: 20px;
        background: var(--destaque); color: #1a1a2e; font-weight: 600;
        font-size: 0.8rem; cursor: pointer; transition: 0.3s;
    }
    .publicacao .comentarios .form-comentario button:hover { transform: scale(1.05); }

    .sem-publicacoes { text-align: center; padding: 60px 20px; color: var(--text); opacity: 0.6; }
    .sem-publicacoes i { font-size: 3rem; margin-bottom: 15px; color: var(--destaque); }

    @media (max-width: 480px) { .publicacao .header { flex-direction: column; align-items: flex-start; } .publicacao .acoes { gap: 10px; } }
</style>
@endsection

@section('content')
<div class="feed-container">
    <!-- Nova Publicação -->
    <div class="nova-publicacao">
        <div class="autor-info">
            <div class="avatar">{{ substr($membro->nome, 0, 1) }}</div>
            <div>
                <div class="nome">{{ $membro->nome }}</div>
                <div class="funcao">{{ $membro->funcao_formatada }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('publicacao.store') }}">
            @csrf
            <textarea name="conteudo" placeholder="O que você está pensando? Compartilhe com a comunidade..." required></textarea>
            <div class="clearfix">
                <button type="submit" class="btn-publicar"><i class="fas fa-paper-plane"></i> Publicar</button>
            </div>
        </form>
    </div>

    <!-- Feed -->
    @if($publicacoes->count() > 0)
        @foreach($publicacoes as $publicacao)
            <div class="publicacao" id="publicacao-{{ $publicacao->id }}">
                <div class="header">
                    <a href="{{ route('perfil.show', $publicacao->autor->matricula) }}" class="autor">
                        <div class="avatar">{{ substr($publicacao->autor->nome, 0, 1) }}</div>
                        <div>
                            <div class="nome">{{ $publicacao->autor->nome }}</div>
                            <div class="funcao">{{ $publicacao->autor->funcao_formatada }}</div>
                        </div>
                    </a>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <span class="data">{{ $publicacao->created_at->diffForHumans() }}</span>
                        @if($publicacao->filiado_matricula == $membro->matricula)
                            <form method="POST" action="{{ route('publicacao.delete', $publicacao->id) }}" style="display: inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-delete" onclick="return confirm('Remover esta publicação?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
                <div class="conteudo">{{ $publicacao->conteudo }}</div>
                <div class="acoes">
                    <form method="POST" action="{{ route('publicacao.curtir', $publicacao->id) }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="{{ $publicacao->isCurtidoPor($membro) ? 'curtido' : '' }}">
                            <i class="fas fa-heart"></i> <span>{{ $publicacao->curtidas_count }}</span>
                        </button>
                    </form>
                    <button onclick="toggleComentarios({{ $publicacao->id }})">
                        <i class="fas fa-comment"></i> <span>{{ $publicacao->comentarios->count() }}</span>
                    </button>
                </div>
                <div class="comentarios" id="comentarios-{{ $publicacao->id }}" style="display: none;">
                    @foreach($publicacao->comentarios as $comentario)
                        <div class="comentario">
                            <div class="avatar-mini">{{ substr($comentario->autor->nome, 0, 1) }}</div>
                            <div class="conteudo">
                                <span class="nome-autor">{{ $comentario->autor->nome }}</span>
                                {{ $comentario->conteudo }}
                                <div style="font-size: 0.7rem; opacity: 0.4; margin-top: 2px;">
                                    {{ $comentario->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <form method="POST" action="{{ route('publicacao.comentar', $publicacao->id) }}" class="form-comentario">
                        @csrf
                        <input type="text" name="conteudo" placeholder="Escreva um comentário..." required>
                        <button type="submit"><i class="fas fa-paper-plane"></i></button>
                    </form>
                </div>
            </div>
        @endforeach
    @else
        <div class="sem-publicacoes">
            <i class="fas fa-comment-dots"></i>
            <h3>Nenhuma publicação ainda</h3>
            <p>Seja o primeiro a compartilhar algo com a comunidade!</p>
        </div>
    @endif
</div>

@push('scripts')
<script>
function toggleComentarios(id) {
    const div = document.getElementById('comentarios-' + id);
    div.style.display = div.style.display === 'none' ? 'block' : 'none';
}
</script>
@endpush
@endsection