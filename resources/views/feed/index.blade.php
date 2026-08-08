@extends('layouts.app')

@section('title', 'Feed - Conexão Igreja')
@section('page-title', 'Feed')
@section('page-subtitle', 'Veja as publicações das pessoas que você segue')

@section('styles')
<style>
    .feed-container { max-width: 700px; margin: 0 auto; }

    .feed-nav {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        background: var(--bg-card);
        border-radius: var(--radius);
        padding: 8px;
        border: 1px solid var(--border);
        box-shadow: var(--shadow);
    }

    .feed-nav a {
        flex: 1;
        text-align: center;
        padding: 10px 16px;
        border-radius: var(--radius-sm);
        text-decoration: none;
        color: var(--text-secondary);
        font-weight: 600;
        font-size: 0.9rem;
        transition: var(--transition);
    }

    .feed-nav a:hover {
        background: rgba(108, 60, 225, 0.05);
        color: var(--primary);
    }

    .feed-nav a.active {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        box-shadow: 0 4px 16px rgba(108, 60, 225, 0.3);
    }

    .feed-nav a i {
        margin-right: 6px;
    }

    .feed-nav .badge-seguindo {
        background: var(--primary);
        color: white;
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 0.7rem;
        margin-left: 6px;
    }

    .busca-membros {
        background: var(--bg-card);
        border-radius: var(--radius);
        padding: 16px 20px;
        box-shadow: var(--shadow);
        margin-bottom: 24px;
        border: 1px solid var(--border);
        transition: var(--transition);
    }

    .busca-membros .input-group {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .busca-membros .input-group input {
        flex: 1;
        padding: 10px 16px;
        border: 2px solid var(--border);
        border-radius: var(--radius-sm);
        background: var(--bg);
        color: var(--text);
        font-size: 0.9rem;
        transition: var(--transition);
    }

    .busca-membros .input-group input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(108, 60, 225, 0.1);
    }

    .busca-membros .input-group .btn-buscar {
        padding: 10px 20px;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        border: none;
        border-radius: var(--radius-sm);
        color: white;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        white-space: nowrap;
    }

    .busca-membros .input-group .btn-buscar:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(108, 60, 225, 0.3);
    }

    .resultados-busca {
        display: none;
        background: var(--bg-card);
        border-radius: var(--radius);
        border: 1px solid var(--border);
        margin-top: 10px;
        max-height: 350px;
        overflow-y: auto;
        box-shadow: var(--shadow);
        animation: fadeIn 0.3s ease;
    }

    .resultados-busca.show {
        display: block;
    }

    .resultados-busca .item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border-bottom: 1px solid var(--border);
        cursor: pointer;
        transition: var(--transition);
        text-decoration: none;
        color: var(--text);
    }

    .resultados-busca .item:hover {
        background: rgba(108, 60, 225, 0.05);
    }

    .resultados-busca .item .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1rem;
        color: white;
        flex-shrink: 0;
        overflow: hidden;
    }

    .resultados-busca .item .avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .resultados-busca .item .info {
        flex: 1;
    }

    .resultados-busca .item .info .nome {
        font-weight: 600;
        font-size: 0.9rem;
    }

    .resultados-busca .item .info .detalhes {
        font-size: 0.75rem;
        color: var(--text-secondary);
    }

    .resultados-busca .item .info .badge-funcao {
        display: inline-block;
        margin-left: 8px;
        background: var(--primary);
        color: white;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 0.65rem;
    }

    .resultados-busca .sem-resultados {
        padding: 30px 20px;
        text-align: center;
        color: var(--text-secondary);
    }

    .resultados-busca .sem-resultados i {
        font-size: 2rem;
        opacity: 0.3;
        margin-bottom: 8px;
        display: block;
    }

    .nova-publicacao {
        background: var(--bg-card);
        border-radius: var(--radius);
        padding: 24px;
        box-shadow: var(--shadow);
        margin-bottom: 24px;
        border: 1px solid var(--border);
        transition: var(--transition);
    }

    .nova-publicacao .autor-info {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 14px;
    }

    .nova-publicacao .autor-info .avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.2rem;
        color: white;
        flex-shrink: 0;
        overflow: hidden;
    }

    .nova-publicacao .autor-info .avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .nova-publicacao .autor-info .nome { font-weight: 600; font-size: 0.95rem; }
    .nova-publicacao .autor-info .funcao { font-size: 0.8rem; color: var(--text-secondary); }

    .nova-publicacao textarea {
        width: 100%;
        min-height: 90px;
        padding: 14px 16px;
        border: 2px solid var(--border);
        border-radius: var(--radius-sm);
        background: var(--bg);
        color: var(--text);
        font-size: 0.95rem;
        resize: vertical;
        font-family: inherit;
        transition: var(--transition);
    }

    .nova-publicacao textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(108, 60, 225, 0.1);
    }

    .nova-publicacao .btn-publicar {
        margin-top: 14px;
        padding: 10px 28px;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        border: none;
        border-radius: var(--radius-sm);
        color: white;
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        float: right;
        transition: var(--transition);
        box-shadow: 0 4px 16px rgba(108, 60, 225, 0.3);
    }

    .nova-publicacao .btn-publicar:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(108, 60, 225, 0.4);
    }

    .nova-publicacao .btn-publicar:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    .clearfix::after {
        content: "";
        clear: both;
        display: table;
    }

    .publicacao {
        background: var(--bg-card);
        border-radius: var(--radius);
        padding: 24px;
        box-shadow: var(--shadow);
        margin-bottom: 20px;
        border: 1px solid var(--border);
        transition: var(--transition);
        animation: fadeIn 0.4s ease;
    }

    .publicacao:hover {
        box-shadow: var(--shadow-hover);
        transform: translateY(-2px);
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .publicacao .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 14px;
        flex-wrap: wrap;
        gap: 8px;
    }

    .publicacao .header .autor {
        display: flex;
        align-items: center;
        gap: 14px;
        text-decoration: none;
        color: var(--text);
        transition: var(--transition);
        flex: 1;
    }

    .publicacao .header .autor:hover { color: var(--primary); }

    .publicacao .header .autor .avatar {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.1rem;
        color: white;
        flex-shrink: 0;
        overflow: hidden;
    }

    .publicacao .header .autor .avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .publicacao .header .autor .nome { font-weight: 600; font-size: 0.95rem; }
    .publicacao .header .autor .funcao { font-size: 0.75rem; color: var(--text-secondary); }
    .publicacao .header .data { font-size: 0.75rem; color: var(--text-secondary); }

    .publicacao .header .btn-delete {
        background: none;
        border: none;
        color: #e74c3c;
        cursor: pointer;
        font-size: 0.9rem;
        opacity: 0.4;
        transition: var(--transition);
        padding: 4px 8px;
        border-radius: 4px;
    }

    .publicacao .header .btn-delete:hover {
        opacity: 1;
        background: rgba(231, 76, 60, 0.1);
    }

    .publicacao .conteudo {
        font-size: 0.95rem;
        line-height: 1.7;
        margin-bottom: 16px;
        word-wrap: break-word;
        white-space: pre-wrap;
        padding: 4px 0;
    }

    .publicacao .acoes {
        display: flex;
        gap: 20px;
        padding-top: 14px;
        border-top: 1px solid var(--border);
        flex-wrap: wrap;
    }

    .publicacao .acoes button {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 6px;
        color: var(--text-secondary);
        transition: var(--transition);
        padding: 6px 14px;
        border-radius: 20px;
        font-weight: 500;
    }

    .publicacao .acoes button:hover {
        background: rgba(108, 60, 225, 0.08);
        color: var(--primary);
    }

    .publicacao .acoes button.curtido {
        color: #e74c3c;
    }

    .publicacao .acoes button.curtido i {
        animation: heartBurst 0.4s ease;
    }

    @keyframes heartBurst {
        0% { transform: scale(1); }
        25% { transform: scale(1.4); }
        50% { transform: scale(0.9); }
        75% { transform: scale(1.2); }
        100% { transform: scale(1); }
    }

    .publicacao .acoes button .curtidas-count {
        min-width: 20px;
        display: inline-block;
    }

    .publicacao .comentarios {
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid var(--border);
        display: none;
        animation: slideDown 0.3s ease;
    }

    .publicacao .comentarios.open {
        display: block;
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .publicacao .comentarios .comentario {
        display: flex;
        gap: 12px;
        margin-bottom: 10px;
        padding: 10px 14px;
        background: var(--bg);
        border-radius: var(--radius-sm);
        animation: fadeIn 0.3s ease;
    }

    .publicacao .comentarios .comentario .avatar-mini {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        font-weight: 700;
        color: white;
        flex-shrink: 0;
        overflow: hidden;
    }

    .publicacao .comentarios .comentario .avatar-mini img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .publicacao .comentarios .comentario .conteudo {
        font-size: 0.85rem;
        line-height: 1.4;
        margin: 0;
        padding: 0;
    }

    .publicacao .comentarios .comentario .conteudo .nome-autor {
        font-weight: 600;
        margin-right: 6px;
        color: var(--primary);
    }

    .publicacao .comentarios .form-comentario {
        display: flex;
        gap: 10px;
        margin-top: 12px;
    }

    .publicacao .comentarios .form-comentario input {
        flex: 1;
        padding: 10px 16px;
        border: 2px solid var(--border);
        border-radius: 20px;
        background: var(--bg);
        color: var(--text);
        font-size: 0.85rem;
        transition: var(--transition);
    }

    .publicacao .comentarios .form-comentario input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(108, 60, 225, 0.08);
    }

    .publicacao .comentarios .form-comentario button {
        padding: 10px 18px;
        border: none;
        border-radius: 20px;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        font-weight: 600;
        font-size: 0.8rem;
        cursor: pointer;
        transition: var(--transition);
    }

    .publicacao .comentarios .form-comentario button:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 16px rgba(108, 60, 225, 0.3);
    }

    .sem-publicacoes {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-secondary);
    }

    .sem-publicacoes i {
        font-size: 4rem;
        margin-bottom: 20px;
        color: var(--primary);
        opacity: 0.3;
    }

    .sem-publicacoes h3 { font-size: 1.3rem; margin-bottom: 8px; }
    .sem-publicacoes p { font-size: 0.9rem; opacity: 0.7; }

    .sem-publicacoes .btn-descobrir {
        display: inline-block;
        margin-top: 16px;
        padding: 10px 24px;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        border-radius: var(--radius-sm);
        text-decoration: none;
        font-weight: 600;
        transition: var(--transition);
    }

    .sem-publicacoes .btn-descobrir:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(108, 60, 225, 0.3);
    }

    .toast-curtida {
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: rgba(0, 0, 0, 0.85);
        color: white;
        padding: 12px 24px;
        border-radius: var(--radius-sm);
        font-weight: 500;
        font-size: 0.9rem;
        z-index: 9999;
        backdrop-filter: blur(10px);
        animation: slideUp 0.4s ease;
        display: none;
        border: 1px solid rgba(255,255,255,0.1);
    }

    .toast-curtida i { margin-right: 8px; }
    .toast-curtida.curtido { border-left: 4px solid #e74c3c; }
    .toast-curtida.descurtido { border-left: 4px solid #95a5a6; }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .loading-spinner {
        display: none;
        text-align: center;
        padding: 20px;
    }

    .loading-spinner i {
        font-size: 2rem;
        color: var(--primary);
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

    .pagination-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }

    @media (max-width: 480px) {
        .publicacao .header { flex-direction: column; align-items: flex-start; }
        .publicacao .acoes { gap: 10px; }
        .nova-publicacao textarea { min-height: 70px; }
        .toast-curtida { bottom: 20px; right: 20px; left: 20px; text-align: center; }
        .busca-membros .input-group { flex-direction: column; }
        .busca-membros .input-group .btn-buscar { width: 100%; }
        .feed-nav { flex-direction: column; }
    }
</style>
@endsection

@section('content')
<div class="feed-container">
    <!-- ===== NAVEGAÇÃO DO FEED ===== -->
    <div class="feed-nav">
        <a href="{{ route('feed.index') }}" class="{{ request()->routeIs('feed.index') ? 'active' : '' }}">
            <i class="fas fa-rss"></i> Seguindo
            <span class="badge-seguindo">{{ $membro->seguindo_count ?? 0 }}</span>
        </a>
        <a href="{{ route('feed.global') }}" class="{{ request()->routeIs('feed.global') ? 'active' : '' }}">
            <i class="fas fa-globe"></i> Global
        </a>
    </div>

    <!-- ===== BUSCA DE MEMBROS ===== -->
    <div class="busca-membros">
        <div class="input-group">
            <input type="text" 
                   id="buscaMembros" 
                   placeholder="🔍 Buscar membros por nome, matrícula, cidade..." 
                   autocomplete="off">
            <button type="button" class="btn-buscar" id="btnBuscarMembros">
                <i class="fas fa-search"></i> Buscar
            </button>
        </div>
        <div class="resultados-busca" id="resultadosBusca">
            <div class="loading-spinner" id="loadingBusca">
                <i class="fas fa-spinner"></i>
                <p style="margin-top: 8px; font-size: 0.85rem;">Buscando membros...</p>
            </div>
            <div id="listaResultados"></div>
        </div>
    </div>

    <!-- ===== NOVA PUBLICAÇÃO ===== -->
    <div class="nova-publicacao">
        <div class="autor-info">
            @php
                $fotoNome = $membro->foto ?? null;
                $fotoUrl = $fotoNome ? route('imagem.foto', ['filename' => $fotoNome]) : null;
                $inicial = substr($membro->nome, 0, 1);
            @endphp
            
            @if($fotoUrl)
                <div class="avatar">
                    <img src="{{ $fotoUrl }}" 
                         alt="{{ $membro->nome }}"
                         onerror="this.style.display='none'; this.parentElement.textContent='{{ $inicial }}';">
                </div>
            @else
                <div class="avatar">{{ $inicial }}</div>
            @endif
            <div>
                <div class="nome">{{ $membro->nome }}</div>
                <div class="funcao">{{ $membro->funcao_formatada ?? 'Membro' }}</div>
            </div>
        </div>
        <form method="POST" action="{{ route('feed.publicar') }}" id="formPublicacao">
            @csrf
            <textarea name="conteudo" id="conteudoPublicacao" placeholder="O que você está pensando? Compartilhe com a comunidade..." required></textarea>
            <div class="clearfix">
                <button type="submit" class="btn-publicar" id="btnPublicar">
                    <i class="fas fa-paper-plane"></i> Publicar
                </button>
            </div>
        </form>
    </div>

    <!-- ===== PUBLICAÇÕES ===== -->
    <div id="feedContainer">
        @if($publicacoes->count() > 0)
            @foreach($publicacoes as $publicacao)
                <div class="publicacao" id="publicacao-{{ $publicacao->id }}" data-id="{{ $publicacao->id }}">
                    <div class="header">
                        <a href="{{ route('perfil.show', $publicacao->autor->matricula ?? $publicacao->filiado_matricula) }}" class="autor">
                            @php
                                $autorFotoNome = $publicacao->autor->foto ?? null;
                                $autorFotoUrl = $autorFotoNome ? route('imagem.foto', ['filename' => $autorFotoNome]) : null;
                                $autorInicial = substr($publicacao->autor->nome ?? 'U', 0, 1);
                            @endphp
                            
                            @if($autorFotoUrl)
                                <div class="avatar">
                                    <img src="{{ $autorFotoUrl }}" 
                                         alt="{{ $publicacao->autor->nome ?? 'Usuário' }}"
                                         onerror="this.style.display='none'; this.parentElement.textContent='{{ $autorInicial }}';">
                                </div>
                            @else
                                <div class="avatar">{{ $autorInicial }}</div>
                            @endif
                            <div>
                                <div class="nome">{{ $publicacao->autor->nome ?? 'Usuário' }}</div>
                                <div class="funcao">{{ $publicacao->autor->funcao ?? 'Membro' }}</div>
                            </div>
                        </a>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <span class="data">{{ $publicacao->created_at->diffForHumans() }}</span>
                            {{-- ⭐ DELETAR: PRÓPRIO USUÁRIO OU ADMIN/SECRETÁRIO --}}
                            @if($publicacao->filiado_matricula == $membro->matricula || auth()->user()?->pode('excluir_membro'))
                                <button type="button" class="btn-delete" data-id="{{ $publicacao->id }}" title="Remover publicação">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="conteudo">{{ $publicacao->conteudo }}</div>

                    <div class="acoes">
                        <button type="button" 
                                class="btn-curtir {{ $publicacao->isCurtidoPor($membro) ? 'curtido' : '' }}" 
                                data-id="{{ $publicacao->id }}">
                            <i class="fas fa-heart"></i>
                            <span class="curtidas-count" id="curtidas-{{ $publicacao->id }}">{{ $publicacao->curtidas->count() }}</span>
                        </button>
                        <button type="button" class="btn-comentar" data-id="{{ $publicacao->id }}">
                            <i class="fas fa-comment"></i>
                            <span id="comentarios-count-{{ $publicacao->id }}">{{ $publicacao->comentarios->count() }}</span>
                        </button>
                    </div>

                    <div class="comentarios" id="comentarios-{{ $publicacao->id }}">
                        <div id="comentarios-lista-{{ $publicacao->id }}">
                            @foreach($publicacao->comentarios as $comentario)
                                <div class="comentario" id="comentario-{{ $comentario->id }}">
                                    @php
                                        $comentFotoNome = $comentario->autor->foto ?? null;
                                        $comentFotoUrl = $comentFotoNome ? route('imagem.foto', ['filename' => $comentFotoNome]) : null;
                                        $comentInicial = substr($comentario->autor->nome ?? '?', 0, 1);
                                    @endphp
                                    
                                    @if($comentFotoUrl)
                                        <div class="avatar-mini">
                                            <img src="{{ $comentFotoUrl }}" 
                                                 alt="{{ $comentario->autor->nome ?? 'Usuário' }}"
                                                 onerror="this.style.display='none'; this.parentElement.textContent='{{ $comentInicial }}';">
                                        </div>
                                    @elseif($comentario->autor)
                                        <div class="avatar-mini">{{ $comentInicial }}</div>
                                    @else
                                        <div class="avatar-mini">?</div>
                                    @endif
                                    <div class="conteudo">
                                        <span class="nome-autor">{{ $comentario->autor?->nome ?? 'Usuário removido' }}</span>
                                        {{ $comentario->conteudo }}
                                        <div style="font-size: 0.7rem; color: var(--text-secondary); opacity: 0.5; margin-top: 2px;">
                                            {{ $comentario->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <form method="POST" action="{{ route('feed.comentar', $publicacao->id) }}" class="form-comentario" data-id="{{ $publicacao->id }}">
                            @csrf
                            <input type="text" name="conteudo" placeholder="Escreva um comentário..." required>
                            <button type="submit"><i class="fas fa-paper-plane"></i></button>
                        </form>
                    </div>
                </div>
            @endforeach

            <!-- ===== PAGINAÇÃO ===== -->
            <div class="pagination-wrapper">
                {{ $publicacoes->links() }}
            </div>

        @else
            <div class="sem-publicacoes" id="semPublicacoes">
                <i class="fas fa-comment-dots"></i>
                <h3>Nenhuma publicação ainda</h3>
                <p>
                    @if($membro->seguindo_count == 0)
                        Você ainda não segue ninguém. 
                        <br>Use a busca acima para encontrar membros da comunidade!
                    @else
                        Ninguém que você segue publicou nada ainda.
                    @endif
                </p>
                @if($membro->seguindo_count == 0)
                    <a href="{{ route('membros.index') }}" class="btn-descobrir">
                        <i class="fas fa-users"></i> Descobrir membros
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- ===== TOAST CURTIDA ===== -->
<div class="toast-curtida" id="toastCurtida">
    <i class="fas fa-heart" style="color: #e74c3c;"></i>
    <span id="toastCurtidaMessage">Você curtiu esta publicação!</span>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    async function checkJsonResponse(response) {
        const contentType = response.headers.get('content-type');
        if (contentType && contentType.includes('application/json')) {
            return response.json();
        }
        const text = await response.text();
        console.error('Resposta HTML recebida:', text.substring(0, 200));
        throw new Error(`Erro do servidor (${response.status}). Faça login novamente.`);
    }

    // ============================================================
    // BUSCA DE MEMBROS
    // ============================================================
    const buscaInput = document.getElementById('buscaMembros');
    const resultadosDiv = document.getElementById('resultadosBusca');
    const listaResultados = document.getElementById('listaResultados');
    const loadingBusca = document.getElementById('loadingBusca');
    let timeoutBusca = null;
    let ultimaBusca = '';

    function buscarMembros(query) {
        if (query.length < 2) {
            resultadosDiv.classList.remove('show');
            return;
        }

        if (query === ultimaBusca && resultadosDiv.classList.contains('show')) {
            return;
        }
        ultimaBusca = query;

        loadingBusca.style.display = 'block';
        listaResultados.innerHTML = '';
        resultadosDiv.classList.add('show');

        const url = '{{ route("membros.autocomplete") }}' + '?q=' + encodeURIComponent(query);

        fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(checkJsonResponse)
        .then(data => {
            loadingBusca.style.display = 'none';
            
            if (!data || data.length === 0) {
                listaResultados.innerHTML = `
                    <div class="sem-resultados">
                        <i class="fas fa-user-slash"></i>
                        Nenhum membro encontrado
                    </div>
                `;
                return;
            }

            let html = '';
            data.forEach(membro => {
                // ⭐ CORRIGIDO: Usa a URL correta da imagem
                const fotoHtml = membro.foto 
                    ? `<img src="${membro.foto}" alt="${membro.nome}">` 
                    : membro.nome.charAt(0);
                
                html += `
                    <a href="{{ url('/') }}/perfil/${membro.matricula}" class="item">
                        <div class="avatar">${fotoHtml}</div>
                        <div class="info">
                            <div class="nome">${membro.nome}</div>
                            <div class="detalhes">
                                <i class="fas fa-id-card"></i> Mat: ${membro.matricula}
                                ${membro.cidade ? ` • <i class="fas fa-map-marker-alt"></i> ${membro.cidade}` : ''}
                                <span class="badge-funcao">${membro.funcao || 'Membro'}</span>
                            </div>
                        </div>
                    </a>
                `;
            });
            listaResultados.innerHTML = html;
        })
        .catch(error => {
            loadingBusca.style.display = 'none';
            console.error('❌ Erro ao buscar membros:', error);
            listaResultados.innerHTML = `
                <div class="sem-resultados">
                    <i class="fas fa-exclamation-triangle" style="color: #e74c3c;"></i>
                    Erro ao buscar membros. Tente novamente.
                </div>
            `;
        });
    }

    buscaInput.addEventListener('input', function() {
        clearTimeout(timeoutBusca);
        const query = this.value.trim();
        
        if (query.length < 2) {
            resultadosDiv.classList.remove('show');
            ultimaBusca = '';
            return;
        }
        
        timeoutBusca = setTimeout(() => {
            buscarMembros(query);
        }, 300);
    });

    buscaInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            clearTimeout(timeoutBusca);
            buscarMembros(this.value.trim());
        }
    });

    document.getElementById('btnBuscarMembros').addEventListener('click', function() {
        buscarMembros(buscaInput.value.trim());
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('.busca-membros')) {
            setTimeout(() => {
                resultadosDiv.classList.remove('show');
            }, 200);
        }
    });

    // ============================================================
    // TOGGLE COMENTÁRIOS
    // ============================================================
    document.querySelectorAll('.btn-comentar').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const div = document.getElementById('comentarios-' + id);
            div.classList.toggle('open');
            div.style.display = div.classList.contains('open') ? 'block' : 'none';
            
            if (div.classList.contains('open')) {
                setTimeout(() => {
                    div.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }, 100);
            }
        });
    });

    // ============================================================
    // CURTIR PUBLICAÇÃO
    // ============================================================
    document.querySelectorAll('.btn-curtir').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const span = document.getElementById(`curtidas-${id}`);
            const toast = document.getElementById('toastCurtida');
            const toastMessage = document.getElementById('toastCurtidaMessage');
            
            this.style.pointerEvents = 'none';
            this.style.opacity = '0.6';

            const url = '{{ route("feed.curtir", ["id" => 0]) }}'.replace('0', id);

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(checkJsonResponse)
            .then(data => {
                if (data.success) {
                    span.textContent = data.curtidas;
                    this.classList.toggle('curtido');

                    if (this.classList.contains('curtido')) {
                        toastMessage.textContent = 'Você curtiu esta publicação! ❤️';
                        toast.className = 'toast-curtida curtido';
                    } else {
                        toastMessage.textContent = 'Você descurtiu esta publicação.';
                        toast.className = 'toast-curtida descurtido';
                    }
                    
                    toast.style.display = 'block';
                    clearTimeout(toast._timeout);
                    toast._timeout = setTimeout(() => { toast.style.display = 'none'; }, 2000);
                }
            })
            .catch(error => {
                console.error('❌ Erro ao curtir:', error);
                alert(error.message);
            })
            .finally(() => {
                this.style.pointerEvents = 'auto';
                this.style.opacity = '1';
            });
        });
    });

    // ============================================================
    // COMENTAR PUBLICAÇÃO
    // ============================================================
    document.querySelectorAll('.form-comentario').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const id = this.dataset.id;
            const input = this.querySelector('input[name="conteudo"]');
            const conteudo = input.value.trim();
            
            if (!conteudo) return;

            const formData = new FormData(this);
            const url = '{{ route("feed.comentar", ["id" => 0]) }}'.replace('0', id);

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(checkJsonResponse)
            .then(data => {
                if (data.success) {
                    const lista = document.getElementById('comentarios-lista-' + id);
                    const novoComentario = document.createElement('div');
                    novoComentario.className = 'comentario';
                    novoComentario.style.animation = 'fadeIn 0.3s ease';
                    
                    const fotoUrl = data.autor_foto ? `/imagens/fotos/${data.autor_foto}` : null;
                    const fotoHtml = fotoUrl 
                        ? `<img src="${fotoUrl}" alt="${data.autor_nome}">` 
                        : data.autor_inicial;
                    
                    novoComentario.innerHTML = `
                        <div class="avatar-mini">${fotoHtml}</div>
                        <div class="conteudo">
                            <span class="nome-autor">${data.autor_nome}</span>
                            ${data.conteudo}
                            <div style="font-size: 0.7rem; color: var(--text-secondary); opacity: 0.5; margin-top: 2px;">
                                Agora mesmo
                            </div>
                        </div>
                    `;
                    
                    lista.appendChild(novoComentario);
                    input.value = '';

                    const spanComentarios = document.getElementById(`comentarios-count-${id}`);
                    spanComentarios.textContent = lista.querySelectorAll('.comentario').length;

                    const container = document.getElementById('comentarios-' + id);
                    container.classList.add('open');
                    container.style.display = 'block';
                    
                    setTimeout(() => {
                        novoComentario.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }, 200);
                }
            })
            .catch(error => {
                console.error('❌ Erro ao comentar:', error);
                alert(error.message);
            });
        });
    });

    // ============================================================
    // DELETAR PUBLICAÇÃO
    // ============================================================
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            if (!confirm('Remover esta publicação?')) return;

            const url = '{{ route("feed.delete", ["id" => 0]) }}'.replace('0', id);

            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(checkJsonResponse)
            .then(data => {
                if (data.success) {
                    const elemento = document.getElementById(`publicacao-${id}`);
                    elemento.style.transition = 'all 0.3s ease';
                    elemento.style.opacity = '0';
                    elemento.style.transform = 'scale(0.9)';
                    setTimeout(() => {
                        elemento.remove();
                        const restantes = document.querySelectorAll('.publicacao').length;
                        if (restantes === 0) {
                            document.getElementById('feedContainer').innerHTML = `
                                <div class="sem-publicacoes" id="semPublicacoes">
                                    <i class="fas fa-comment-dots"></i>
                                    <h3>Nenhuma publicação ainda</h3>
                                    <p>Ninguém publicou nada ainda. Seja o primeiro!</p>
                                </div>
                            `;
                        }
                    }, 300);
                }
            })
            .catch(error => {
                console.error('❌ Erro ao deletar:', error);
                alert(error.message);
            });
        });
    });

    // ============================================================
    // PUBLICAR NOVA
    // ============================================================
    const formPublicacao = document.getElementById('formPublicacao');
    const btnPublicar = document.getElementById('btnPublicar');
    
    if (formPublicacao) {
        formPublicacao.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const textarea = document.getElementById('conteudoPublicacao');
            const conteudo = textarea.value.trim();
            
            if (!conteudo) {
                textarea.focus();
                textarea.style.borderColor = '#e74c3c';
                setTimeout(() => textarea.style.borderColor = '', 2000);
                return;
            }

            btnPublicar.disabled = true;
            btnPublicar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Publicando...';

            const formData = new FormData(this);
            const url = '{{ route("feed.publicar") }}';

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(checkJsonResponse)
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(error => {
                console.error('❌ Erro ao publicar:', error);
                alert(error.message);
            })
            .finally(() => {
                btnPublicar.disabled = false;
                btnPublicar.innerHTML = '<i class="fas fa-paper-plane"></i> Publicar';
            });
        });
    }

    // ============================================================
    // AUTO-FECHAR TOASTS
    // ============================================================
    document.querySelectorAll('.toast, .alert').forEach(el => {
        setTimeout(() => {
            el.style.transition = 'all 0.5s ease';
            el.style.opacity = '0';
            el.style.transform = 'translateY(-20px)';
            setTimeout(() => el.remove(), 500);
        }, 5000);
    });

    console.log('📱 Feed carregado');
    console.log('👤 Usuário: {{ $membro->nome }}');
    console.log('📊 Seguindo: {{ $membro->seguindo_count ?? 0 }}');
    console.log('📄 Publicações: {{ $publicacoes->count() }}');
});
</script>
@endpush