@extends('layouts.app')

@section('title', 'Estatísticas - Admin')
@section('page-title', '📊 Estatísticas da Igreja')
@section('page-subtitle', 'Visualize dados e métricas dos membros')

@section('styles')
<style>
    .stats-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 20px 24px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        text-align: center;
        transition: all 0.3s ease;
        border: 1px solid #e8e4db;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }

    .stat-card .number {
        font-size: 2.5rem;
        font-weight: 800;
        color: #1a1a2e;
        line-height: 1.2;
    }

    .stat-card .label {
        font-size: 0.85rem;
        color: #999;
        font-weight: 500;
        margin-top: 5px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-card .icon {
        font-size: 2rem;
        display: block;
        margin-bottom: 10px;
    }

    .stat-card.total .number { color: #6c757d; }
    .stat-card.ativos .number { color: #4caf50; }
    .stat-card.inativos .number { color: #f44336; }
    .stat-card.transferidos .number { color: #2196F3; }
    .stat-card.saida .number { color: #9e9e9e; }

    .charts-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 25px;
        margin-bottom: 30px;
    }

    .chart-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border: 1px solid #e8e4db;
    }

    .chart-card h3 {
        font-size: 1rem;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .chart-card .chart-icon {
        font-size: 1.2rem;
    }

    .chart-card .chart-container {
        max-height: 300px;
        overflow-y: auto;
    }

    .chart-card .chart-container::-webkit-scrollbar {
        width: 4px;
    }

    .chart-card .chart-container::-webkit-scrollbar-thumb {
        background: #c9a84c;
        border-radius: 10px;
    }

    .bar-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 8px 0;
        border-bottom: 1px solid #f0ece6;
    }

    .bar-item:last-child {
        border-bottom: none;
    }

    .bar-item .bar-label {
        font-size: 0.85rem;
        color: #1a1a2e;
        font-weight: 500;
        min-width: 120px;
        flex-shrink: 0;
    }

    .bar-item .bar-track {
        flex: 1;
        height: 24px;
        background: #f0ece6;
        border-radius: 12px;
        overflow: hidden;
        position: relative;
    }

    .bar-item .bar-fill {
        height: 100%;
        border-radius: 12px;
        transition: width 1s ease;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        padding-right: 8px;
        font-size: 0.7rem;
        font-weight: 600;
        color: white;
        min-width: 30px;
    }

    .bar-fill.gold { background: linear-gradient(90deg, #c9a84c, #b8960f); }
    .bar-fill.green { background: linear-gradient(90deg, #4caf50, #388e3c); }
    .bar-fill.blue { background: linear-gradient(90deg, #2196F3, #0d47a1); }
    .bar-fill.purple { background: linear-gradient(90deg, #9c27b0, #6a1b9a); }
    .bar-fill.red { background: linear-gradient(90deg, #f44336, #c62828); }
    .bar-fill.orange { background: linear-gradient(90deg, #ff9800, #e65100); }
    .bar-fill.teal { background: linear-gradient(90deg, #009688, #004d40); }
    .bar-fill.pink { background: linear-gradient(90deg, #e91e63, #880e4f); }

    .aniversariantes-section {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border: 1px solid #e8e4db;
        margin-top: 25px;
    }

    .aniversariantes-section h3 {
        font-size: 1rem;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .aniversariantes-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 15px;
    }

    .aniversariante-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 14px;
        background: #faf8f5;
        border-radius: 12px;
        border: 1px solid #e8e4db;
        transition: all 0.3s ease;
    }

    .aniversariante-item:hover {
        border-color: #c9a84c;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .aniversariante-item .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #c9a84c, #b8960f);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1rem;
        flex-shrink: 0;
        overflow: hidden;
    }

    .aniversariante-item .avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .aniversariante-item .avatar .avatar-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
    }

    .aniversariante-item .info {
        flex: 1;
        min-width: 0;
    }

    .aniversariante-item .info .nome {
        font-weight: 600;
        font-size: 0.9rem;
        color: #1a1a2e;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .aniversariante-item .info .data {
        font-size: 0.75rem;
        color: #999;
    }

    .aniversariante-item .info .funcao {
        font-size: 0.7rem;
        color: #999;
        background: #e8e4db;
        padding: 1px 10px;
        border-radius: 10px;
        display: inline-block;
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: #999;
    }

    .empty-state .empty-icon {
        font-size: 3rem;
        display: block;
        margin-bottom: 15px;
        opacity: 0.3;
    }

    /* ⭐ ALERTA DE ACESSO NEGADO */
    .alert-danger {
        background: #ffebee;
        color: #c62828;
        padding: 20px 25px;
        border-radius: 12px;
        border-left: 4px solid #c62828;
        margin-bottom: 20px;
    }

    .alert-danger .btn-admin {
        margin-top: 10px;
    }

    .btn-admin {
        padding: 10px 20px;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        color: white;
    }

    .btn-admin-secondary {
        background: #6c757d;
    }

    .btn-admin-secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(108, 117, 125, 0.4);
        color: white;
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
        }

        .stat-card {
            padding: 15px;
        }

        .stat-card .number {
            font-size: 1.8rem;
        }

        .charts-grid {
            grid-template-columns: 1fr;
        }

        .bar-item .bar-label {
            min-width: 80px;
            font-size: 0.75rem;
        }

        .aniversariantes-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr 1fr;
        }

        .stat-card .number {
            font-size: 1.4rem;
        }

        .stat-card .label {
            font-size: 0.7rem;
        }
    }
</style>
@endsection

@section('content')
<div class="stats-container">
    {{-- ⭐ VERIFICA PERMISSÃO --}}
    @if(!auth()->user()?->pode('dashboard'))
        <div class="alert-danger">
            <strong>⛔ Acesso Negado</strong>
            <p>Você não tem permissão para acessar as estatísticas.</p>
            <a href="{{ route('admin.membros.index') }}" class="btn-admin btn-admin-secondary" style="display: inline-block;">
                <span>🔙</span> Voltar
            </a>
        </div>
    @else
        <!-- Cards de Estatísticas -->
        <div class="stats-grid">
            <div class="stat-card total">
                <span class="icon">👥</span>
                <div class="number">{{ $stats['total'] ?? 0 }}</div>
                <div class="label">Total de Membros</div>
            </div>
            <div class="stat-card ativos">
                <span class="icon">✅</span>
                <div class="number">{{ $stats['ativos'] ?? 0 }}</div>
                <div class="label">Ativos</div>
            </div>
            <div class="stat-card inativos">
                <span class="icon">❌</span>
                <div class="number">{{ $stats['inativos'] ?? 0 }}</div>
                <div class="label">Inativos</div>
            </div>
            <div class="stat-card transferidos">
                <span class="icon">🔄</span>
                <div class="number">{{ $stats['transferidos'] ?? 0 }}</div>
                <div class="label">Transferidos</div>
            </div>
            <div class="stat-card saida">
                <span class="icon">🚪</span>
                <div class="number">{{ $stats['saida'] ?? 0 }}</div>
                <div class="label">Saída</div>
            </div>
        </div>

        <!-- Gráficos -->
        <div class="charts-grid">
            <!-- Por Função -->
            <div class="chart-card">
                <h3>
                    <span class="chart-icon">📌</span>
                    Membros por Função
                </h3>
                <div class="chart-container">
                    @if($porFuncao->count() > 0)
                        @php
                            $maxTotal = $porFuncao->max('total');
                            $colors = ['gold', 'green', 'blue', 'purple', 'red', 'orange', 'teal', 'pink'];
                            $colorIndex = 0;
                        @endphp
                        @foreach($porFuncao as $item)
                            @php
                                $percent = $maxTotal > 0 ? ($item->total / $maxTotal) * 100 : 0;
                                $color = $colors[$colorIndex % count($colors)];
                                $colorIndex++;
                            @endphp
                            <div class="bar-item">
                                <span class="bar-label">{{ $item->funcao ?? 'Não definido' }}</span>
                                <div class="bar-track">
                                    <div class="bar-fill {{ $color }}" style="width: {{ max($percent, 5) }}%;">
                                        {{ $item->total }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <span class="empty-icon">📭</span>
                            <p>Nenhuma função cadastrada</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Por Cidade -->
            <div class="chart-card">
                <h3>
                    <span class="chart-icon">🏙️</span>
                    Membros por Cidade (Top 10)
                </h3>
                <div class="chart-container">
                    @if($porCidade->count() > 0)
                        @php
                            $maxTotal = $porCidade->max('total');
                            $colors = ['gold', 'green', 'blue', 'purple', 'red', 'orange', 'teal', 'pink'];
                            $colorIndex = 0;
                        @endphp
                        @foreach($porCidade as $item)
                            @php
                                $percent = $maxTotal > 0 ? ($item->total / $maxTotal) * 100 : 0;
                                $color = $colors[$colorIndex % count($colors)];
                                $colorIndex++;
                            @endphp
                            <div class="bar-item">
                                <span class="bar-label">{{ $item->cidade ?? 'Não definido' }}</span>
                                <div class="bar-track">
                                    <div class="bar-fill {{ $color }}" style="width: {{ max($percent, 5) }}%;">
                                        {{ $item->total }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="empty-state">
                            <span class="empty-icon">📭</span>
                            <p>Nenhuma cidade cadastrada</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Por Congregação (gráfico maior) -->
        <div class="chart-card" style="margin-bottom: 25px;">
            <h3>
                <span class="chart-icon">⛪</span>
                Membros por Congregação
            </h3>
            <div class="chart-container">
                @if($porCongregacao->count() > 0)
                    @php
                        $maxTotal = $porCongregacao->max('total');
                        $colors = ['gold', 'green', 'blue', 'purple', 'red', 'orange', 'teal', 'pink'];
                        $colorIndex = 0;
                    @endphp
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                        @foreach($porCongregacao as $item)
                            @php
                                $percent = $maxTotal > 0 ? ($item->total / $maxTotal) * 100 : 0;
                                $color = $colors[$colorIndex % count($colors)];
                                $colorIndex++;
                            @endphp
                            <div class="bar-item">
                                <span class="bar-label">{{ $item->congregacao ?? 'Não definido' }}</span>
                                <div class="bar-track">
                                    <div class="bar-fill {{ $color }}" style="width: {{ max($percent, 5) }}%;">
                                        {{ $item->total }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <span class="empty-icon">📭</span>
                        <p>Nenhuma congregação cadastrada</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Aniversariantes do Mês -->
        <div class="aniversariantes-section">
            <h3>
                <span>🎂</span>
                Aniversariantes do Mês
                <span style="font-size: 0.8rem; color: #999; font-weight: 400; margin-left: 10px;">
                    {{ now()->format('F') }}
                </span>
            </h3>
            @if($aniversariantes->count() > 0)
                <div class="aniversariantes-grid">
                    @foreach($aniversariantes as $aniversariante)
                        <div class="aniversariante-item">
                            <div class="avatar">
                                @php
                                    $fotoNome = $aniversariante->foto ?? null;
                                    $fotoUrl = $fotoNome ? route('imagem.foto', ['filename' => $fotoNome]) : null;
                                    $inicial = substr($aniversariante->nome, 0, 1);
                                @endphp
                                
                                @if($fotoUrl)
                                    <img src="{{ $fotoUrl }}" 
                                         alt="{{ $aniversariante->nome }}"
                                         onerror="this.style.display='none'; this.parentElement.innerHTML='<span class=\'avatar-placeholder\'>{{ $inicial }}</span>';">
                                @else
                                    <span class="avatar-placeholder">{{ $inicial }}</span>
                                @endif
                            </div>
                            <div class="info">
                                <div class="nome">{{ $aniversariante->nome }}</div>
                                <div class="data">
                                    🎈 {{ \Carbon\Carbon::parse($aniversariante->dataNascimento)->format('d/m') }}
                                    @if($aniversariante->funcao)
                                        <span class="funcao">{{ $aniversariante->funcao }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state" style="padding: 20px;">
                    <span class="empty-icon" style="font-size: 2rem;">🎉</span>
                    <p>Nenhum aniversariante este mês</p>
                </div>
            @endif
        </div>
    @endif {{-- Fim da verificação de permissão --}}
</div>

@push('scripts')
<script>
    console.log('📊 Página de estatísticas carregada');
    console.log('📈 Total de membros: {{ $stats['total'] ?? 0 }}');
    console.log('✅ Ativos: {{ $stats['ativos'] ?? 0 }}');
    console.log('❌ Inativos: {{ $stats['inativos'] ?? 0 }}');
    console.log('🔄 Transferidos: {{ $stats['transferidos'] ?? 0 }}');
    console.log('🚪 Saída: {{ $stats['saida'] ?? 0 }}');
    console.log('🎂 Aniversariantes: {{ $aniversariantes->count() }}');

    // Animar barras ao carregar
    document.addEventListener('DOMContentLoaded', function() {
        const bars = document.querySelectorAll('.bar-fill');
        bars.forEach((bar, index) => {
            const width = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.width = width;
            }, 100 + (index * 50));
        });
    });
</script>
@endpush
@endsection