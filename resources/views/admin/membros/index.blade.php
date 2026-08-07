@extends('layouts.app')

@section('title', 'Gerenciar Membros - Admin')
@section('page-title', 'Gerenciar Membros')
@section('page-subtitle', 'Cadastre, edite e gerencie todos os membros da igreja')

@section('styles')
<style>
    :root {
        --gold: #c9a84c;
        --gold-dark: #b8960f;
        --gold-light: #f5f0e0;
        --text-dark: #1a1a2e;
        --text-gray: #666;
        --border-color: #e8e4db;
        --bg-light: #faf8f5;
        --shadow: 0 2px 10px rgba(0,0,0,0.05);
        --radius: 12px;
    }

    .admin-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 15px;
    }

    .admin-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 25px;
        padding: 0 5px;
    }

    .admin-header h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .admin-header h2 .icon {
        font-size: 1.8rem;
        line-height: 1;
    }

    .admin-header .actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .btn-admin {
        padding: 10px 20px;
        border: none;
        border-radius: var(--radius);
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        color: white;
        white-space: nowrap;
    }

    .btn-admin-primary {
        background: linear-gradient(145deg, var(--gold), var(--gold-dark));
    }

    .btn-admin-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(201, 168, 76, 0.4);
        color: white;
    }

    .btn-admin-success {
        background: linear-gradient(145deg, #4caf50, #388e3c);
    }

    .btn-admin-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.4);
        color: white;
    }

    .btn-admin-danger {
        background: linear-gradient(145deg, #f44336, #c62828);
    }

    .btn-admin-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(244, 67, 54, 0.4);
        color: white;
    }

    .btn-admin-outline {
        background: transparent;
        color: var(--text-dark);
        border: 2px solid var(--border-color);
    }

    .btn-admin-outline:hover {
        border-color: var(--gold);
        color: var(--gold);
        transform: translateY(-2px);
        background: transparent;
    }

    .stats-bar {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        padding: 18px 20px;
        background: white;
        border-radius: var(--radius);
        margin-bottom: 25px;
        box-shadow: var(--shadow);
    }

    .stats-bar .stat-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 5px 0;
    }

    .stats-bar .stat-item .number {
        font-weight: 700;
        font-size: 1.3rem;
        color: var(--text-dark);
        min-width: 30px;
    }

    .stats-bar .stat-item .label {
        font-size: 0.8rem;
        color: var(--text-gray);
        font-weight: 500;
    }

    .stats-bar .stat-item .dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        flex-shrink: 0;
    }

    .dot-ativo { background: #4caf50; }
    .dot-inativo { background: #f44336; }
    .dot-pendente { background: #ff9800; }
    .dot-transferido { background: #2196F3; }

    .search-box {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        background: white;
        padding: 15px 20px;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        margin-bottom: 25px;
        align-items: center;
    }

    .search-box input,
    .search-box select {
        padding: 10px 14px;
        border: 2px solid var(--border-color);
        border-radius: 10px;
        font-size: 0.9rem;
        background: var(--bg-light);
        transition: all 0.3s ease;
        flex: 1;
        min-width: 150px;
        color: var(--text-dark);
    }

    .search-box input:focus,
    .search-box select:focus {
        outline: none;
        border-color: var(--gold);
        background: white;
        box-shadow: 0 0 0 3px rgba(201, 168, 76, 0.1);
    }

    .search-box .btn-admin {
        min-width: 100px;
        justify-content: center;
    }

    .table-container {
        background: white;
        border-radius: var(--radius);
        overflow: hidden;
        box-shadow: var(--shadow);
    }

    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        min-width: 700px;
    }

    thead {
        background: var(--bg-light);
        border-bottom: 2px solid var(--border-color);
    }

    th {
        padding: 14px 16px;
        text-align: left;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: var(--text-gray);
        border-bottom: 2px solid var(--border-color);
        white-space: nowrap;
    }

    td {
        padding: 12px 16px;
        border-bottom: 1px solid #f0ece6;
        font-size: 0.9rem;
        vertical-align: middle;
        color: var(--text-dark);
    }

    tr:last-child td {
        border-bottom: none;
    }

    tr:hover td {
        background: var(--bg-light);
    }

    .status-badge {
        display: inline-block;
        padding: 4px 14px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    .status-ativo { background: #e8f5e9; color: #2e7d32; }
    .status-inativo { background: #ffebee; color: #c62828; }
    .status-pendente { background: #fff3e0; color: #e65100; }
    .status-transferido { background: #e3f2fd; color: #0d47a1; }
    .status-saida { background: #f5f5f5; color: #757575; }

    .action-buttons {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .action-buttons .btn-sm {
        padding: 6px 10px;
        border: none;
        border-radius: 8px;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        text-decoration: none;
        min-width: 32px;
        min-height: 32px;
    }

    .btn-sm-view {
        background: #e3f2fd;
        color: #0d47a1;
    }

    .btn-sm-view:hover {
        background: #0d47a1;
        color: white;
        transform: translateY(-2px);
    }

    .btn-sm-edit {
        background: #fff3e0;
        color: #e65100;
    }

    .btn-sm-edit:hover {
        background: #e65100;
        color: white;
        transform: translateY(-2px);
    }

    .btn-sm-delete {
        background: #ffebee;
        color: #c62828;
    }

    .btn-sm-delete:hover {
        background: #c62828;
        color: white;
        transform: translateY(-2px);
    }

    .btn-sm-posts {
        background: #f3e5f5;
        color: #6a1b9a;
    }

    .btn-sm-posts:hover {
        background: #6a1b9a;
        color: white;
        transform: translateY(-2px);
    }

    .checkbox-column {
        width: 44px;
        text-align: center;
        padding: 0 8px !important;
    }

    .checkbox-column input[type="checkbox"] {
        width: 18px;
        height: 18px;
        cursor: pointer;
        accent-color: var(--gold);
        margin: 0;
    }

    .bulk-actions {
        display: none;
        align-items: center;
        gap: 15px;
        padding: 15px 20px;
        background: var(--bg-light);
        border-radius: var(--radius);
        margin: 15px 0;
        flex-wrap: wrap;
        border: 2px solid var(--gold-light);
    }

    .bulk-actions.show {
        display: flex;
    }

    .bulk-actions .selected-info {
        font-size: 0.9rem;
        color: var(--text-dark);
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .bulk-actions select {
        padding: 8px 14px;
        border-radius: 8px;
        border: 2px solid var(--border-color);
        background: white;
        font-size: 0.9rem;
        min-width: 180px;
        color: var(--text-dark);
    }

    .bulk-actions select:focus {
        outline: none;
        border-color: var(--gold);
    }

    .pagination-wrapper {
        padding: 20px;
        display: flex;
        justify-content: center;
        align-items: center;
        border-top: 1px solid var(--border-color);
        background: white;
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
        min-width: 36px;
        height: 36px;
        padding: 0 10px;
        border-radius: 8px;
        border: 1px solid transparent;
        text-decoration: none;
        color: var(--text-gray);
        transition: all 0.2s ease;
        font-size: 0.85rem;
        background: transparent;
        font-weight: 500;
        cursor: pointer;
    }

    .pagination .page-link:hover {
        background: var(--bg-light);
        border-color: var(--border-color);
        color: var(--text-dark);
        transform: none;
    }

    .pagination .active .page-link {
        background: var(--gold);
        color: white;
        border-color: var(--gold);
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

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-gray);
    }

    .empty-state .empty-icon {
        font-size: 4rem;
        display: block;
        margin-bottom: 20px;
        opacity: 0.3;
    }

    .empty-state p {
        margin: 10px 0;
        font-size: 1rem;
    }

    .empty-state .empty-sub {
        font-size: 0.85rem;
        color: #999;
    }

    .empty-state a {
        color: var(--gold);
        text-decoration: none;
        font-weight: 600;
    }

    .empty-state a:hover {
        text-decoration: underline;
    }

    .alert-success {
        background: #e8f5e9;
        color: #2e7d32;
        padding: 15px 20px;
        border-radius: var(--radius);
        margin-bottom: 20px;
        border-left: 4px solid #4caf50;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .alert-error {
        background: #ffebee;
        color: #c62828;
        padding: 15px 20px;
        border-radius: var(--radius);
        margin-bottom: 20px;
        border-left: 4px solid #f44336;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    @media (max-width: 768px) {
        .admin-header {
            flex-direction: column;
            align-items: stretch;
            gap: 15px;
        }

        .admin-header .actions {
            width: 100%;
        }

        .admin-header .actions .btn-admin {
            flex: 1;
            justify-content: center;
            min-width: 120px;
        }

        .search-box {
            flex-direction: column;
            padding: 15px;
        }

        .search-box input,
        .search-box select {
            width: 100%;
            min-width: unset;
        }

        .search-box .btn-admin {
            width: 100%;
        }

        .stats-bar {
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            padding: 15px;
        }

        .stats-bar .stat-item {
            padding: 5px;
        }

        .bulk-actions {
            flex-direction: column;
            align-items: stretch;
        }

        .bulk-actions select {
            width: 100%;
        }

        .bulk-actions .btn-admin {
            width: 100%;
            justify-content: center;
        }

        table {
            font-size: 0.8rem;
            min-width: 600px;
        }

        th, td {
            padding: 10px 12px;
        }

        .action-buttons .btn-sm {
            padding: 4px 8px;
            min-width: 28px;
            min-height: 28px;
            font-size: 0.7rem;
        }

        .status-badge {
            font-size: 0.6rem;
            padding: 3px 10px;
        }

        .checkbox-column {
            width: 36px;
        }
    }

    @media (max-width: 480px) {
        .stats-bar {
            grid-template-columns: 1fr 1fr;
        }

        .admin-header h2 {
            font-size: 1.2rem;
        }

        .btn-admin {
            font-size: 0.8rem;
            padding: 8px 14px;
        }
    }
</style>
@endsection

@section('content')
<div class="admin-container">
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="alert-success">
            <span>✅</span> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert-error">
            <span>❌</span> {{ session('error') }}
        </div>
    @endif

    <!-- Header -->
    <div class="admin-header">
        <h2>
            <span class="icon">⚙️</span> 
            Gerenciar Membros
        </h2>
        <div class="actions">
            <a href="{{ route('admin.membros.create') }}" class="btn-admin btn-admin-primary">
                <span>➕</span> Novo Membro
            </a>
            <button type="button" class="btn-admin btn-admin-outline" onclick="toggleBulkActions()">
                <span>☑️</span> Ações em Massa
            </button>
            <a href="{{ route('admin.membros.exportar', request()->query()) }}" class="btn-admin btn-admin-outline">
                <span>📥</span> Exportar
            </a>
            <a href="{{ route('admin.estatisticas') }}" class="btn-admin btn-admin-outline">
                <span>📊</span> Estatísticas
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="stats-bar">
        <div class="stat-item">
            <span class="number">{{ $stats['total'] ?? $membros->total() }}</span>
            <span class="label">Total</span>
        </div>
        <div class="stat-item">
            <span class="dot dot-ativo"></span>
            <span class="number">{{ $stats['ativos'] ?? 0 }}</span>
            <span class="label">Ativos</span>
        </div>
        <div class="stat-item">
            <span class="dot dot-inativo"></span>
            <span class="number">{{ $stats['inativos'] ?? 0 }}</span>
            <span class="label">Inativos</span>
        </div>
        <div class="stat-item">
            <span class="dot dot-transferido"></span>
            <span class="number">{{ $stats['transferidos'] ?? 0 }}</span>
            <span class="label">Transferidos</span>
        </div>
        <div class="stat-item">
            <span class="dot" style="background:#9e9e9e;"></span>
            <span class="number">{{ $stats['saida'] ?? 0 }}</span>
            <span class="label">Saída</span>
        </div>
    </div>

    <!-- Search -->
    <form method="GET" class="search-box" action="{{ route('admin.membros.index') }}">
        <input type="text" name="search" placeholder="Buscar por nome, matrícula, email, documento ou telefone..." value="{{ request('search') }}">
        
        <select name="status">
            <option value="">Todos os Status</option>
            <option value="ativo" {{ request('status') == 'ativo' ? 'selected' : '' }}>Ativo</option>
            <option value="inativo" {{ request('status') == 'inativo' ? 'selected' : '' }}>Inativo</option>
            <option value="transferido" {{ request('status') == 'transferido' ? 'selected' : '' }}>Transferido</option>
            <option value="saida" {{ request('status') == 'saida' ? 'selected' : '' }}>Saída</option>
        </select>
        
        <select name="funcao">
            <option value="">Todas as Funções</option>
            @foreach($funcoes as $funcao)
                <option value="{{ $funcao }}" {{ request('funcao') == $funcao ? 'selected' : '' }}>{{ $funcao }}</option>
            @endforeach
        </select>
        
        <select name="congregacao">
            <option value="">Todas as Congregações</option>
            @foreach($congregacoes as $congregacao)
                <option value="{{ $congregacao }}" {{ request('congregacao') == $congregacao ? 'selected' : '' }}>{{ $congregacao }}</option>
            @endforeach
        </select>
        
        <button type="submit" class="btn-admin btn-admin-primary">
            <span>🔍</span> Filtrar
        </button>
        
        @if(request()->filled('search') || request()->filled('status') || request()->filled('funcao') || request()->filled('congregacao'))
            <a href="{{ route('admin.membros.index') }}" class="btn-admin btn-admin-outline">
                <span>✖</span> Limpar
            </a>
        @endif
    </form>

    <!-- Bulk Actions -->
    <div class="bulk-actions" id="bulkActions">
        <div class="selected-info">
            <span>✅</span> 
            <span id="selectedCount">0</span> selecionado(s)
        </div>
        
        <form action="{{ route('admin.membros.bulk') }}" method="POST" id="bulkForm" style="display: flex; gap: 10px; flex-wrap: wrap; align-items: center; flex: 1;">
            @csrf
            <input type="hidden" name="ids" id="bulkIds" value="">
            
            <select name="action" required>
                <option value="">Selecione uma ação...</option>
                <option value="ativar">✅ Ativar</option>
                <option value="inativar">⛔ Inativar</option>
                <option value="transferir">🔄 Transferir</option>
                <option value="deletar">🗑️ Deletar</option>
            </select>
            
            <button type="submit" class="btn-admin btn-admin-success" onclick="return confirmBulkAction()">
                <span>▶</span> Executar
            </button>
            
            <button type="button" class="btn-admin btn-admin-outline" onclick="toggleBulkActions()">
                <span>✖</span> Fechar
            </button>
        </form>
    </div>

    <!-- Table -->
    <div class="table-container">
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th class="checkbox-column">
                            <input type="checkbox" id="selectAll" onchange="toggleAllCheckboxes(this)">
                        </th>
                        <th>Matrícula</th>
                        <th>Nome</th>
                        <th>Função</th>
                        <th>Congregação</th>
                        <th>Telefone</th>
                        <th>Status</th>
                        <th style="text-align: center; min-width: 140px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($membros as $membro)
                        <tr>
                            <td class="checkbox-column">
                                <input type="checkbox" class="member-checkbox" value="{{ $membro->matricula }}" onchange="updateSelectedCount()">
                            </td>
                            <td>
                                <strong>#{{ $membro->matricula }}</strong>
                            </td>
                            <td>
                                {{ $membro->nome }}
                                @if($membro->email)
                                    <br><small style="color: #999; font-size: 0.7rem;">{{ $membro->email }}</small>
                                @endif
                            </td>
                            <td>{{ $membro->funcao ?? '—' }}</td>
                            <td>{{ $membro->congregacao ?? '—' }}</td>
                            <td>{{ $membro->telefone ?? '—' }}</td>
                            <td>
                                <span class="status-badge status-{{ strtolower($membro->status ?? 'inativo') }}">
                                    {{ $membro->status ?? 'INATIVO' }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.membros.show', $membro->matricula) }}" class="btn-sm btn-sm-view" title="Visualizar">
                                        <span>👁️</span>
                                    </a>
                                    <a href="{{ route('admin.membros.edit', $membro->matricula) }}" class="btn-sm btn-sm-edit" title="Editar">
                                        <span>✏️</span>
                                    </a>
                                    <a href="{{ route('admin.membros.posts', $membro->matricula) }}" class="btn-sm btn-sm-posts" title="Ver Posts">
                                        <span>📝</span>
                                    </a>
                                    <form action="{{ route('admin.membros.destroy', $membro->matricula) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-sm btn-sm-delete" title="Remover" onclick="return confirm('Tem certeza que deseja remover o membro {{ addslashes($membro->nome) }}?')">
                                            <span>🗑️</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="empty-state">
                                    <span class="empty-icon">👥</span>
                                    <p><strong>Nenhum membro encontrado</strong></p>
                                    <p class="empty-sub">
                                        Tente ajustar os filtros ou 
                                        <a href="{{ route('admin.membros.create') }}">cadastre um novo membro</a>
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginação -->
        @if($membros->hasPages())
            <div class="pagination-wrapper">
                <nav class="pagination" role="navigation" aria-label="Pagination">
                    {{-- Botão Anterior --}}
                    @if($membros->onFirstPage())
                        <span class="page-item disabled">
                            <span class="page-link">‹ Anterior</span>
                        </span>
                    @else
                        <a class="page-item" href="{{ $membros->previousPageUrl() }}">
                            <span class="page-link">‹ Anterior</span>
                        </a>
                    @endif

                    {{-- Números das páginas --}}
                    @php
                        $currentPage = $membros->currentPage();
                        $lastPage = $membros->lastPage();
                        $start = max(1, $currentPage - 2);
                        $end = min($lastPage, $currentPage + 2);
                    @endphp

                    @if($start > 1)
                        <a class="page-item" href="{{ $membros->url(1) }}"><span class="page-link">1</span></a>
                        @if($start > 2)
                            <span class="page-item disabled"><span class="page-link">…</span></span>
                        @endif
                    @endif

                    @for($i = $start; $i <= $end; $i++)
                        @if($i == $currentPage)
                            <span class="page-item active"><span class="page-link">{{ $i }}</span></span>
                        @else
                            <a class="page-item" href="{{ $membros->url($i) }}"><span class="page-link">{{ $i }}</span></a>
                        @endif
                    @endfor

                    @if($end < $lastPage)
                        @if($end < $lastPage - 1)
                            <span class="page-item disabled"><span class="page-link">…</span></span>
                        @endif
                        <a class="page-item" href="{{ $membros->url($lastPage) }}"><span class="page-link">{{ $lastPage }}</span></a>
                    @endif

                    {{-- Botão Próximo --}}
                    @if($membros->hasMorePages())
                        <a class="page-item" href="{{ $membros->nextPageUrl() }}">
                            <span class="page-link">Próximo ›</span>
                        </a>
                    @else
                        <span class="page-item disabled">
                            <span class="page-link">Próximo ›</span>
                        </span>
                    @endif
                </nav>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function toggleBulkActions() {
        const bulkActions = document.getElementById('bulkActions');
        if (bulkActions) {
            bulkActions.classList.toggle('show');
            
            if (!bulkActions.classList.contains('show')) {
                document.querySelectorAll('.member-checkbox').forEach(cb => cb.checked = false);
                const selectAll = document.getElementById('selectAll');
                if (selectAll) selectAll.checked = false;
                updateSelectedCount();
            }
        }
    }

    function toggleAllCheckboxes(master) {
        const checkboxes = document.querySelectorAll('.member-checkbox');
        checkboxes.forEach(cb => cb.checked = master.checked);
        updateSelectedCount();
    }

    function updateSelectedCount() {
        const checked = document.querySelectorAll('.member-checkbox:checked');
        const count = checked.length;
        const selectedCount = document.getElementById('selectedCount');
        if (selectedCount) {
            selectedCount.textContent = count;
        }
        
        const ids = Array.from(checked).map(cb => cb.value);
        const bulkIds = document.getElementById('bulkIds');
        if (bulkIds) {
            bulkIds.value = ids.join(',');
        }
        
        const bulkActions = document.getElementById('bulkActions');
        if (bulkActions) {
            if (count > 0) {
                bulkActions.classList.add('show');
            } else {
                bulkActions.classList.remove('show');
            }
        }
    }

    function confirmBulkAction() {
        const action = document.querySelector('select[name="action"]');
        if (!action || !action.value) {
            alert('Selecione uma ação para executar.');
            return false;
        }
        
        const count = document.querySelectorAll('.member-checkbox:checked').length;
        if (count === 0) {
            alert('Selecione pelo menos um membro.');
            return false;
        }
        
        const actionLabels = {
            'ativar': 'ativar',
            'inativar': 'inativar',
            'transferir': 'transferir',
            'deletar': 'deletar'
        };
        
        return confirm(`Tem certeza que deseja ${actionLabels[action.value] || 'executar'} ${count} membro(s)?`);
    }

    // Prevenir submissão de formulário vazio
    document.addEventListener('DOMContentLoaded', function() {
        const bulkForm = document.getElementById('bulkForm');
        if (bulkForm) {
            bulkForm.addEventListener('submit', function(e) {
                const ids = document.getElementById('bulkIds').value;
                const action = this.querySelector('select[name="action"]').value;
                
                if (!ids) {
                    e.preventDefault();
                    alert('Selecione pelo menos um membro.');
                    return false;
                }
                
                if (!action) {
                    e.preventDefault();
                    alert('Selecione uma ação.');
                    return false;
                }
            });
        }
        
        // Inicializa contagem
        updateSelectedCount();
        
        @if(app()->environment('local'))
            console.log('🛠️ Painel Administrativo carregado');
            console.log('📊 Total de membros: {{ $stats['total'] ?? $membros->total() }}');
            console.log('📊 Ativos: {{ $stats['ativos'] ?? 0 }}');
            console.log('📊 Inativos: {{ $stats['inativos'] ?? 0 }}');
            console.log('📊 Transferidos: {{ $stats['transferidos'] ?? 0 }}');
            console.log('📊 Saída: {{ $stats['saida'] ?? 0 }}');
        @endif
    });
</script>
@endpush
@endsection