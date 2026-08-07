@extends('layouts.app')

@section('title', 'Membros - Conexão Igreja')
@section('page-title', 'Membros da Igreja')
@section('page-subtitle', 'Conheça todos os membros da nossa comunidade')

@section('styles')
<style>
    .members-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 15px;
    }

    .members-header {
        text-align: center;
        margin-bottom: 40px;
        padding: 20px 0;
    }

    .members-header h1 {
        font-size: 2.2rem;
        color: #1a1a2e;
        margin-bottom: 10px;
    }

    .members-header p {
        color: #666;
        font-size: 1.1rem;
    }

    /* Search Box */
    .search-box-members {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        background: white;
        padding: 20px;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 30px;
        align-items: center;
        border: 1px solid #e8e4db;
    }

    .search-box-members input,
    .search-box-members select {
        padding: 12px 16px;
        border: 2px solid #e8e4db;
        border-radius: 10px;
        font-size: 0.95rem;
        background: #faf8f5;
        transition: all 0.3s ease;
        flex: 1;
        min-width: 180px;
        color: #1a1a2e;
    }

    .search-box-members input:focus,
    .search-box-members select:focus {
        outline: none;
        border-color: #c9a84c;
        background: white;
        box-shadow: 0 0 0 3px rgba(201, 168, 76, 0.1);
    }

    .search-box-members .btn-search {
        padding: 12px 30px;
        border: none;
        border-radius: 10px;
        background: linear-gradient(145deg, #c9a84c, #b8960f);
        color: white;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }

    .search-box-members .btn-search:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(201, 168, 76, 0.4);
    }

    .search-box-members .btn-clear {
        padding: 12px 24px;
        border: 2px solid #e8e4db;
        border-radius: 10px;
        background: transparent;
        color: #666;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }

    .search-box-members .btn-clear:hover {
        border-color: #c9a84c;
        color: #c9a84c;
        background: #faf8f5;
    }

    /* Members Grid */
    .members-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 25px;
        margin-bottom: 30px;
    }

    .member-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 25px 20px;
        transition: all 0.3s ease;
        border: 1px solid #e8e4db;
        text-align: center;
        position: relative;
        cursor: pointer;
    }

    .member-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        border-color: #c9a84c;
    }

    .member-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(145deg, #c9a84c, #b8960f);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: white;
        margin: 0 auto 15px;
        flex-shrink: 0;
        overflow: hidden;
    }

    .member-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .member-card h3 {
        font-size: 1.1rem;
        color: #1a1a2e;
        margin: 0 0 5px;
        font-weight: 700;
    }

    .member-card .matricula {
        font-size: 0.8rem;
        color: #999;
        margin-bottom: 10px;
    }

    .member-card .funcao {
        display: inline-block;
        background: #f5f0e0;
        color: #c9a84c;
        padding: 4px 16px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        margin-bottom: 12px;
    }

    .member-card .info-item {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        font-size: 0.85rem;
        color: #666;
        margin: 5px 0;
        padding: 4px 0;
    }

    .member-card .info-item .icon {
        font-size: 1rem;
    }

    .member-card .status-badge {
        display: inline-block;
        padding: 4px 16px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 12px;
    }

    .status-ativo { background: #e8f5e9; color: #2e7d32; }
    .status-inativo { background: #ffebee; color: #c62828; }
    .status-pendente { background: #fff3e0; color: #e65100; }
    .status-transferido { background: #e3f2fd; color: #0d47a1; }

    /* Pagination */
    .pagination-wrapper {
        padding: 25px 0 10px;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .pagination {
        display: flex;
        gap: 4px;
        align-items: center;
        flex-wrap: wrap;
        justify-content: center;
    }

    .pagination .page-item {
        display: inline-flex;
        margin: 0;
    }

    .pagination .page-link {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 38px;
        height: 38px;
        padding: 0 12px;
        border-radius: 8px;
        border: 1px solid transparent;
        text-decoration: none;
        color: #666;
        transition: all 0.2s ease;
        font-size: 0.85rem;
        background: transparent;
        font-weight: 500;
        cursor: pointer;
    }

    .pagination .page-link:hover {
        background: #faf8f5;
        border-color: #e8e4db;
        color: #1a1a2e;
        transform: none;
    }

    .pagination .active .page-link {
        background: #c9a84c;
        color: white;
        border-color: #c9a84c;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(201, 168, 76, 0.3);
    }

    .pagination .disabled .page-link {
        opacity: 0.4;
        cursor: not-allowed;
        pointer-events: none;
        background: transparent;
        border-color: transparent;
    }

    .pagination .page-link .pagination-icon {
        font-size: 0.75rem;
        line-height: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .pagination .page-item:first-child .page-link,
    .pagination .page-item:last-child .page-link {
        background: transparent;
        border: 1px solid #e8e4db;
        padding: 0 14px;
        min-width: 90px;
        font-weight: 500;
        font-size: 0.85rem;
    }

    .pagination .page-item:first-child .page-link:hover,
    .pagination .page-item:last-child .page-link:hover {
        background: #faf8f5;
        border-color: #c9a84c;
        color: #c9a84c;
    }

    .pagination .page-item:first-child .page-link .pagination-icon {
        margin-right: 4px;
    }

    .pagination .page-item:last-child .page-link .pagination-icon {
        margin-left: 4px;
    }

    /* Empty State */
    .empty-state {
        grid-column: 1 / -1;
        text-align: center;
        padding: 80px 20px;
        color: #999;
    }

    .empty-state .empty-icon {
        font-size: 4rem;
        display: block;
        margin-bottom: 20px;
        opacity: 0.3;
    }

    .empty-state p {
        margin: 10px 0;
        font-size: 1.1rem;
    }

    .empty-state .empty-sub {
        font-size: 0.9rem;
        color: #bbb;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .search-box-members {
            flex-direction: column;
            padding: 15px;
        }

        .search-box-members input,
        .search-box-members select {
            width: 100%;
            min-width: unset;
        }

        .search-box-members .btn-search,
        .search-box-members .btn-clear {
            width: 100%;
            justify-content: center;
        }

        .members-grid {
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .member-card {
            padding: 20px 15px;
        }

        .member-avatar {
            width: 60px;
            height: 60px;
            font-size: 2rem;
        }

        .member-card h3 {
            font-size: 1rem;
        }

        .pagination .page-item:first-child .page-link,
        .pagination .page-item:last-child .page-link {
            min-width: 70px;
            padding: 0 10px;
            font-size: 0.8rem;
        }

        .pagination .page-link {
            min-width: 34px;
            height: 34px;
            font-size: 0.8rem;
            padding: 0 8px;
        }
    }

    @media (max-width: 480px) {
        .members-grid {
            grid-template-columns: 1fr;
        }

        .members-header h1 {
            font-size: 1.6rem;
        }

        .pagination .page-item:first-child .page-link,
        .pagination .page-item:last-child .page-link {
            min-width: 60px;
            font-size: 0.75rem;
            padding: 0 8px;
        }

        .pagination .page-link {
            min-width: 30px;
            height: 30px;
            font-size: 0.75rem;
            padding: 0 6px;
        }

        .pagination {
            gap: 3px;
        }
    }
</style>
@endsection

@section('content')
<div class="members-container">
    <div class="members-header">
        <h1>👥 Nossos Membros</h1>
        <p>Conheça todos os membros da nossa comunidade</p>
    </div>

    <!-- Search -->
    <form method="GET" class="search-box-members" action="{{ route('membros.index') }}">
        <input type="text" name="search" placeholder="Buscar por nome, matrícula, função..." value="{{ request('search') }}">
        
        <select name="funcao">
            <option value="">Todas as Funções</option>
            @php
                $funcoes = App\Models\Filiado::on('mysql')->where('status', 'Ativo')->distinct()->pluck('funcao')->filter()->values();
            @endphp
            @foreach($funcoes as $funcao)
                <option value="{{ $funcao }}" {{ request('funcao') == $funcao ? 'selected' : '' }}>{{ $funcao }}</option>
            @endforeach
        </select>
        
        <select name="congregacao">
            <option value="">Todas as Congregações</option>
            @php
                $congregacoes = App\Models\Filiado::on('mysql')->where('status', 'Ativo')->distinct()->pluck('congregacao')->filter()->values();
            @endphp
            @foreach($congregacoes as $congregacao)
                <option value="{{ $congregacao }}" {{ request('congregacao') == $congregacao ? 'selected' : '' }}>{{ $congregacao }}</option>
            @endforeach
        </select>
        
        <button type="submit" class="btn-search">
            🔍 Buscar
        </button>
        
        @if(request()->anyFilled(['search', 'funcao', 'congregacao']))
            <a href="{{ route('membros.index') }}" class="btn-clear">
                ✖ Limpar
            </a>
        @endif
    </form>

    <!-- Members Grid -->
    <div class="members-grid">
        @forelse($membros as $membro)
            <div class="member-card" onclick="window.location='{{ route('membros.show', $membro->matricula) }}'">
                <div class="member-avatar">
                    @if($membro->foto)
                        <img src="{{ route('imagem.foto', ['filename' => $membro->foto]) }}" alt="{{ $membro->nome }}">
                    @else
                        👤
                    @endif
                </div>
                <h3>{{ $membro->nome }}</h3>
                <div class="matricula">Matrícula #{{ $membro->matricula }}</div>
                
                @if($membro->funcao)
                    <div class="funcao">{{ $membro->funcao }}</div>
                @endif
                
                @if($membro->email)
                    <div class="info-item">
                        <span class="icon">📧</span> {{ $membro->email }}
                    </div>
                @endif
                
                @if($membro->telefone)
                    <div class="info-item">
                        <span class="icon">📱</span> {{ $membro->telefone }}
                    </div>
                @endif
                
                @if($membro->congregacao)
                    <div class="info-item">
                        <span class="icon">⛪</span> {{ $membro->congregacao }}
                    </div>
                @endif
                
                @if($membro->cidade && $membro->uf)
                    <div class="info-item">
                        <span class="icon">📍</span> {{ $membro->cidade }}/{{ $membro->uf }}
                    </div>
                @endif
                
                <span class="status-badge status-{{ strtolower($membro->status ?? 'inativo') }}">
                    {{ $membro->status ?? 'INATIVO' }}
                </span>
            </div>
        @empty
            <div class="empty-state">
                <span class="empty-icon">👥</span>
                <p><strong>Nenhum membro encontrado</strong></p>
                <p class="empty-sub">Tente ajustar os filtros de busca</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($membros->hasPages())
        <div class="pagination-wrapper">
            <nav class="pagination" role="navigation" aria-label="Pagination">
                {{-- Previous --}}
                @if($membros->onFirstPage())
                    <span class="page-item disabled">
                        <span class="page-link">
                            <span class="pagination-icon">‹</span> Anterior
                        </span>
                    </span>
                @else
                    <a class="page-item" href="{{ $membros->previousPageUrl() }}">
                        <span class="page-link">
                            <span class="pagination-icon">‹</span> Anterior
                        </span>
                    </a>
                @endif

                {{-- Page numbers --}}
                @php
                    $currentPage = $membros->currentPage();
                    $lastPage = $membros->lastPage();
                    $start = max(1, $currentPage - 2);
                    $end = min($lastPage, $currentPage + 2);
                    
                    if ($start > 1) {
                        echo '<a class="page-item" href="' . $membros->url(1) . '"><span class="page-link">1</span></a>';
                        if ($start > 2) {
                            echo '<span class="page-item disabled"><span class="page-link">…</span></span>';
                        }
                    }
                    
                    for ($i = $start; $i <= $end; $i++) {
                        if ($i == $currentPage) {
                            echo '<span class="page-item active"><span class="page-link">' . $i . '</span></span>';
                        } else {
                            echo '<a class="page-item" href="' . $membros->url($i) . '"><span class="page-link">' . $i . '</span></a>';
                        }
                    }
                    
                    if ($end < $lastPage) {
                        if ($end < $lastPage - 1) {
                            echo '<span class="page-item disabled"><span class="page-link">…</span></span>';
                        }
                        echo '<a class="page-item" href="' . $membros->url($lastPage) . '"><span class="page-link">' . $lastPage . '</span></a>';
                    }
                @endphp

                {{-- Next --}}
                @if($membros->hasMorePages())
                    <a class="page-item" href="{{ $membros->nextPageUrl() }}">
                        <span class="page-link">
                            Próximo <span class="pagination-icon">›</span>
                        </span>
                    </a>
                @else
                    <span class="page-item disabled">
                        <span class="page-link">
                            Próximo <span class="pagination-icon">›</span>
                        </span>
                    </span>
                @endif
            </nav>
        </div>
    @endif
</div>

@push('scripts')
<script>
    console.log('📋 Lista de membros carregada');
    console.log('📊 Total: {{ $membros->total() }}');
    
    document.querySelectorAll('.member-card').forEach(card => {
        card.addEventListener('click', function(e) {
            if (e.target.closest('a') || e.target.closest('button')) {
                e.stopPropagation();
                return;
            }
        });
    });
</script>
@endpush
@endsection