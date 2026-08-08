@extends('layouts.app')

@section('title', 'Busca Avançada - Membros')
@section('page-title', 'Busca Avançada de Membros')
@section('page-subtitle', 'Filtre membros por múltiplos critérios')

@section('styles')
<style>
    .busca-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 15px;
    }

    .busca-header {
        text-align: center;
        margin-bottom: 30px;
        padding: 20px 0;
    }

    .busca-header h1 {
        font-size: 2rem;
        color: #1a1a2e;
        margin-bottom: 10px;
    }

    .busca-header p {
        color: #666;
        font-size: 1.05rem;
    }

    /* Formulário de Busca */
    .filtros-form {
        background: white;
        padding: 25px;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        margin-bottom: 30px;
        border: 1px solid #e8e4db;
    }

    .filtros-form .row {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 15px;
        margin-bottom: 15px;
    }

    .filtros-form .campo {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .filtros-form .campo label {
        font-weight: 600;
        font-size: 0.85rem;
        color: #333;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .filtros-form .campo label i {
        color: #c9a84c;
        font-size: 0.9rem;
        width: 18px;
    }

    .filtros-form .campo input,
    .filtros-form .campo select {
        padding: 10px 14px;
        border: 2px solid #e8e4db;
        border-radius: 10px;
        font-size: 0.9rem;
        background: #faf8f5;
        transition: all 0.3s ease;
        color: #1a1a2e;
        width: 100%;
    }

    .filtros-form .campo input:focus,
    .filtros-form .campo select:focus {
        outline: none;
        border-color: #c9a84c;
        background: white;
        box-shadow: 0 0 0 3px rgba(201, 168, 76, 0.1);
    }

    .filtros-form .campo input::placeholder {
        color: #aaa;
        font-size: 0.85rem;
    }

    .filtros-acoes {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        padding-top: 15px;
        border-top: 1px solid #e8e4db;
        margin-top: 5px;
    }

    .filtros-acoes .btn {
        padding: 12px 30px;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        color: white;
    }

    .btn-buscar {
        background: linear-gradient(145deg, #c9a84c, #b8960f);
    }

    .btn-buscar:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(201, 168, 76, 0.4);
    }

    .btn-limpar {
        background: #6c757d;
    }

    .btn-limpar:hover {
        background: #5a6268;
        transform: translateY(-2px);
    }

    .btn-exportar {
        background: linear-gradient(145deg, #28a745, #1e7e34);
    }

    .btn-exportar:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.4);
    }

    .btn-excel {
        background: linear-gradient(145deg, #217346, #1a5c38);
    }

    .btn-excel:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(33, 115, 70, 0.4);
    }

    /* Resultados */
    .resultados-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 20px;
        padding: 15px 20px;
        background: #faf8f5;
        border-radius: 12px;
        border: 1px solid #e8e4db;
    }

    .resultados-header .total {
        font-weight: 600;
        color: #1a1a2e;
    }

    .resultados-header .total span {
        color: #c9a84c;
        font-size: 1.2rem;
    }

    .resultados-header .opcoes {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
    }

    .resultados-header .opcoes select {
        padding: 8px 14px;
        border: 2px solid #e8e4db;
        border-radius: 8px;
        font-size: 0.85rem;
        background: white;
        cursor: pointer;
    }

    .resultados-header .opcoes select:focus {
        outline: none;
        border-color: #c9a84c;
    }

    /* Tabela de Resultados */
    .tabela-wrapper {
        overflow-x: auto;
        background: white;
        border-radius: 16px;
        border: 1px solid #e8e4db;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .tabela-resultados {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
        min-width: 700px;
    }

    .tabela-resultados thead {
        background: #faf8f5;
        border-bottom: 2px solid #e8e4db;
    }

    .tabela-resultados thead th {
        padding: 14px 16px;
        text-align: left;
        font-weight: 700;
        color: #1a1a2e;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    .tabela-resultados thead th i {
        margin-right: 4px;
        color: #c9a84c;
    }

    .tabela-resultados tbody tr {
        border-bottom: 1px solid #f0ede8;
        transition: background 0.2s ease;
    }

    .tabela-resultados tbody tr:hover {
        background: #faf8f5;
    }

    .tabela-resultados tbody tr:last-child {
        border-bottom: none;
    }

    .tabela-resultados tbody td {
        padding: 12px 16px;
        vertical-align: middle;
        color: #333;
    }

    .tabela-resultados .avatar-mini {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(145deg, #c9a84c, #b8960f);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        font-weight: 700;
        color: white;
        overflow: hidden;
        flex-shrink: 0;
    }

    .tabela-resultados .avatar-mini img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .tabela-resultados .badge-funcao {
        display: inline-block;
        padding: 3px 12px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 600;
        background: #f5f0e0;
        color: #c9a84c;
        white-space: nowrap;
    }

    .tabela-resultados .status-badge {
        display: inline-block;
        padding: 3px 12px;
        border-radius: 12px;
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-ativo { background: #e8f5e9; color: #2e7d32; }
    .status-inativo { background: #ffebee; color: #c62828; }
    .status-pendente { background: #fff3e0; color: #e65100; }
    .status-transferido { background: #e3f2fd; color: #0d47a1; }

    .tabela-resultados .btn-acao {
        padding: 6px 12px;
        border: none;
        border-radius: 8px;
        font-size: 0.75rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        color: white;
    }

    .btn-ver {
        background: #c9a84c;
    }

    .btn-ver:hover {
        background: #b8960f;
        transform: translateY(-1px);
    }

    .btn-editar {
        background: #ff9800;
    }

    .btn-editar:hover {
        background: #e65100;
        transform: translateY(-1px);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #999;
    }

    .empty-state .empty-icon {
        font-size: 3.5rem;
        display: block;
        margin-bottom: 15px;
        opacity: 0.3;
    }

    .empty-state p {
        margin: 8px 0;
        font-size: 1.05rem;
    }

    .empty-state .empty-sub {
        font-size: 0.9rem;
        color: #bbb;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .filtros-form .row {
            grid-template-columns: 1fr 1fr;
        }

        .filtros-acoes {
            flex-direction: column;
        }

        .filtros-acoes .btn {
            justify-content: center;
            width: 100%;
        }

        .resultados-header {
            flex-direction: column;
            gap: 10px;
            align-items: stretch;
        }

        .resultados-header .opcoes {
            flex-direction: column;
        }

        .resultados-header .opcoes select {
            width: 100%;
        }

        .tabela-resultados {
            font-size: 0.8rem;
            min-width: 600px;
        }

        .tabela-resultados thead th,
        .tabela-resultados tbody td {
            padding: 10px 12px;
        }
    }

    @media (max-width: 480px) {
        .filtros-form .row {
            grid-template-columns: 1fr;
        }

        .filtros-form {
            padding: 15px;
        }

        .busca-header h1 {
            font-size: 1.5rem;
        }

        .tabela-resultados {
            min-width: 500px;
            font-size: 0.75rem;
        }

        .tabela-resultados thead th,
        .tabela-resultados tbody td {
            padding: 8px 10px;
        }

        .tabela-resultados .btn-acao {
            padding: 4px 8px;
            font-size: 0.65rem;
        }
    }
</style>
@endsection

@section('content')
<div class="busca-container">
    <div class="busca-header">
        <h1>🔍 Busca Avançada</h1>
        <p>Encontre membros usando múltiplos critérios de filtro</p>
    </div>

    <!-- Formulário de Filtros -->
    <form method="GET" action="{{ route('membros.buscar.avancado') }}" class="filtros-form" id="formBusca">
        <div class="row">
            <div class="campo">
                <label><i class="fas fa-user"></i> Nome</label>
                <input type="text" name="nome" placeholder="Digite o nome..." value="{{ request('nome') }}">
            </div>

            <div class="campo">
                <label><i class="fas fa-id-card"></i> Matrícula</label>
                <input type="text" name="matricula" placeholder="Nº da matrícula..." value="{{ request('matricula') }}">
            </div>

            <div class="campo">
                <label><i class="fas fa-envelope"></i> Email</label>
                <input type="email" name="email" placeholder="Email..." value="{{ request('email') }}">
            </div>

            <div class="campo">
                <label><i class="fas fa-id-card"></i> Documento</label>
                <input type="text" name="documento" placeholder="CPF/RG..." value="{{ request('documento') }}">
            </div>

            <div class="campo">
                <label><i class="fas fa-city"></i> Cidade</label>
                <input type="text" name="cidade" placeholder="Cidade..." value="{{ request('cidade') }}">
            </div>

            <div class="campo">
                <label><i class="fas fa-map-marker-alt"></i> UF</label>
                <select name="uf">
                    <option value="">Todos</option>
                    @php
                        $ufs = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'];
                    @endphp
                    @foreach($ufs as $uf)
                        <option value="{{ $uf }}" {{ request('uf') == $uf ? 'selected' : '' }}>{{ $uf }}</option>
                    @endforeach
                </select>
            </div>

            <div class="campo">
                <label><i class="fas fa-church"></i> Congregação</label>
                <input type="text" name="congregacao" placeholder="Congregação..." value="{{ request('congregacao') }}">
            </div>

            <div class="campo">
                <label><i class="fas fa-user-tie"></i> Função</label>
                <select name="funcao">
                    <option value="">Todas</option>
                    @php
                        $funcoes = App\Models\Filiado::on('mysql')->distinct()->pluck('funcao')->filter()->values();
                    @endphp
                    @foreach($funcoes as $funcao)
                        <option value="{{ $funcao }}" {{ request('funcao') == $funcao ? 'selected' : '' }}>{{ $funcao }}</option>
                    @endforeach
                </select>
            </div>

            <div class="campo">
                <label><i class="fas fa-circle"></i> Status</label>
                <select name="status">
                    <option value="">Todos</option>
                    <option value="Ativo" {{ request('status') == 'Ativo' ? 'selected' : '' }}>Ativo</option>
                    <option value="Inativo" {{ request('status') == 'Inativo' ? 'selected' : '' }}>Inativo</option>
                    <option value="Pendente" {{ request('status') == 'Pendente' ? 'selected' : '' }}>Pendente</option>
                    <option value="Transferido" {{ request('status') == 'Transferido' ? 'selected' : '' }}>Transferido</option>
                </select>
            </div>

            <div class="campo">
                <label><i class="fas fa-calendar-alt"></i> Nascimento (Início)</label>
                <input type="date" name="data_nascimento_inicio" value="{{ request('data_nascimento_inicio') }}">
            </div>

            <div class="campo">
                <label><i class="fas fa-calendar-alt"></i> Nascimento (Fim)</label>
                <input type="date" name="data_nascimento_fim" value="{{ request('data_nascimento_fim') }}">
            </div>

            <div class="campo">
                <label><i class="fas fa-calendar-plus"></i> Cadastro (Início)</label>
                <input type="date" name="data_cadastro_inicio" value="{{ request('data_cadastro_inicio') }}">
            </div>

            <div class="campo">
                <label><i class="fas fa-calendar-plus"></i> Cadastro (Fim)</label>
                <input type="date" name="data_cadastro_fim" value="{{ request('data_cadastro_fim') }}">
            </div>
        </div>

        <div class="filtros-acoes">
            <button type="submit" class="btn btn-buscar">
                <i class="fas fa-search"></i> Buscar
            </button>

            <a href="{{ route('membros.buscar.avancado') }}" class="btn btn-limpar">
                <i class="fas fa-eraser"></i> Limpar Filtros
            </a>

            {{-- ⭐ BOTÕES DE EXPORTAÇÃO - APENAS QUEM TEM PERMISSÃO --}}
            @if(auth()->user()?->pode('exportar_membros') && request()->anyFilled(['nome', 'matricula', 'email', 'documento', 'cidade', 'uf', 'congregacao', 'funcao', 'status', 'data_nascimento_inicio', 'data_nascimento_fim', 'data_cadastro_inicio', 'data_cadastro_fim']))
                <button type="submit" name="exportar" value="1" class="btn btn-exportar">
                    <i class="fas fa-file-export"></i> Exportar CSV
                </button>
                <button type="submit" name="excel" value="1" class="btn btn-excel">
                    <i class="fas fa-file-excel"></i> Exportar Excel
                </button>
            @endif
        </div>
    </form>

    <!-- Resultados -->
    @if(isset($membros))
        <div class="resultados-header">
            <div class="total">
                <i class="fas fa-users"></i> 
                Total: <span>{{ $membros instanceof \Illuminate\Pagination\LengthAwarePaginator ? $membros->total() : $membros->count() }}</span> membros encontrados
            </div>
            <div class="opcoes">
                <select id="limit" name="limit" onchange="document.getElementById('formBusca').submit()">
                    <option value="10" {{ request('limit') == 10 ? 'selected' : '' }}>10 por página</option>
                    <option value="20" {{ request('limit') == 20 ? 'selected' : '' }}>20 por página</option>
                    <option value="50" {{ request('limit') == 50 ? 'selected' : '' }}>50 por página</option>
                    <option value="100" {{ request('limit') == 100 ? 'selected' : '' }}>100 por página</option>
                    <option value="all" {{ request('limit') == 'all' ? 'selected' : '' }}>Todos</option>
                </select>
            </div>
        </div>

        @if($membros->count() > 0)
            <div class="tabela-wrapper">
                <table class="tabela-resultados">
                    <thead>
                        <tr>
                            <th><i class="fas fa-user"></i> Membro</th>
                            <th><i class="fas fa-id-card"></i> Matrícula</th>
                            <th><i class="fas fa-user-tie"></i> Função</th>
                            <th><i class="fas fa-map-marker-alt"></i> Cidade/UF</th>
                            <th><i class="fas fa-church"></i> Congregação</th>
                            <th><i class="fas fa-circle"></i> Status</th>
                            <th><i class="fas fa-cog"></i> Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($membros as $membro)
                            <tr>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div class="avatar-mini">
                                            @php
                                                $fotoNome = $membro->foto ?? null;
                                                $fotoUrl = $fotoNome ? route('imagem.foto', ['filename' => $fotoNome]) : null;
                                                $inicial = substr($membro->nome, 0, 1);
                                            @endphp
                                            
                                            @if($fotoUrl)
                                                <img src="{{ $fotoUrl }}" 
                                                     alt="{{ $membro->nome }}"
                                                     onerror="this.style.display='none'; this.parentElement.textContent='{{ $inicial }}';">
                                            @else
                                                {{ $inicial }}
                                            @endif
                                        </div>
                                        <span>{{ $membro->nome }}</span>
                                    </div>
                                </td>
                                <td><strong>#{{ $membro->matricula }}</strong></td>
                                <td>
                                    @if($membro->funcao)
                                        <span class="badge-funcao">{{ $membro->funcao }}</span>
                                    @else
                                        <span style="color: #999; font-size: 0.8rem;">Membro</span>
                                    @endif
                                </td>
                                <td>
                                    @if($membro->cidade && $membro->uf)
                                        {{ $membro->cidade }}/{{ $membro->uf }}
                                    @else
                                        <span style="color: #999; font-size: 0.8rem;">—</span>
                                    @endif
                                </td>
                                <td>{{ $membro->congregacao ?? '—' }}</td>
                                <td>
                                    <span class="status-badge status-{{ strtolower($membro->status ?? 'inativo') }}">
                                        {{ $membro->status ?? 'INATIVO' }}
                                    </span>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 4px; flex-wrap: wrap;">
                                        <a href="{{ route('membros.show', $membro->matricula) }}" class="btn-acao btn-ver">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                        
                                        {{-- ⭐ BOTÃO EDITAR - APENAS QUEM TEM PERMISSÃO --}}
                                        @if(auth()->user()?->pode('editar_membro', $membro))
                                            <a href="{{ route('admin.membros.edit', $membro->matricula) }}" class="btn-acao btn-editar">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            @if($membros instanceof \Illuminate\Pagination\LengthAwarePaginator && $membros->hasPages())
                <div style="padding: 20px 0; display: flex; justify-content: center;">
                    {{ $membros->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <span class="empty-icon">🔍</span>
                <p><strong>Nenhum membro encontrado</strong></p>
                <p class="empty-sub">Tente ajustar os filtros de busca</p>
            </div>
        @endif
    @endif
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔍 Busca Avançada de Membros');

    // Auto-submit ao mudar o limit
    document.getElementById('limit')?.addEventListener('change', function() {
        document.getElementById('formBusca').submit();
    });

    // Máscara para matrícula (apenas números)
    document.querySelector('input[name="matricula"]')?.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '');
    });

    // Máscara para documento (apenas números)
    document.querySelector('input[name="documento"]')?.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '');
    });
});
</script>
@endpush
@endsection