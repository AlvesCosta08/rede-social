@extends('layouts.admin')

@section('title', 'Estatísticas - Conexão Igreja')
@section('page-title', '📊 Estatísticas')
@section('page-subtitle', 'Análise completa dos membros da igreja')

@section('content')
<div class="grid-2">
    <!-- Cards de Estatísticas -->
    <div class="card-modern">
        <h5 style="font-weight: 600; color: var(--text); margin-bottom: 16px;">
            <i class="fas fa-users" style="color: var(--primary);"></i> 
            Visão Geral
        </h5>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 12px;">
            <div style="background: var(--bg); padding: 16px; border-radius: var(--radius-sm); text-align: center;">
                <div style="font-size: 1.8rem; font-weight: 800; color: var(--primary);">{{ $stats['total'] }}</div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">Total</div>
            </div>
            <div style="background: var(--bg); padding: 16px; border-radius: var(--radius-sm); text-align: center;">
                <div style="font-size: 1.8rem; font-weight: 800; color: var(--success);">{{ $stats['ativos'] }}</div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">Ativos</div>
            </div>
            <div style="background: var(--bg); padding: 16px; border-radius: var(--radius-sm); text-align: center;">
                <div style="font-size: 1.8rem; font-weight: 800; color: var(--danger);">{{ $stats['inativos'] }}</div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">Inativos</div>
            </div>
            <div style="background: var(--bg); padding: 16px; border-radius: var(--radius-sm); text-align: center;">
                <div style="font-size: 1.8rem; font-weight: 800; color: var(--warning);">{{ $stats['transferidos'] }}</div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">Transferidos</div>
            </div>
            <div style="background: var(--bg); padding: 16px; border-radius: var(--radius-sm); text-align: center;">
                <div style="font-size: 1.8rem; font-weight: 800; color: var(--text-secondary);">{{ $stats['saida'] }}</div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">Saída</div>
            </div>
        </div>
    </div>

    <!-- Por Função -->
    <div class="card-modern">
        <h5 style="font-weight: 600; color: var(--text); margin-bottom: 16px;">
            <i class="fas fa-briefcase" style="color: var(--primary);"></i> 
            Distribuição por Função
        </h5>
        
        @if($porFuncao->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 8px;">
                @foreach($porFuncao as $item)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; background: var(--bg); border-radius: var(--radius-sm);">
                        <span style="font-weight: 500;">{{ $item->funcao ?? 'Não informado' }}</span>
                        <span style="font-weight: 700; color: var(--primary);">{{ $item->total }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p style="color: var(--text-secondary); text-align: center; padding: 20px;">Nenhum dado disponível</p>
        @endif
    </div>

    <!-- Por Cidade (Top 10) -->
    <div class="card-modern">
        <h5 style="font-weight: 600; color: var(--text); margin-bottom: 16px;">
            <i class="fas fa-city" style="color: var(--primary);"></i> 
            Top 10 Cidades
        </h5>
        
        @if($porCidade->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 8px;">
                @foreach($porCidade as $item)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; background: var(--bg); border-radius: var(--radius-sm);">
                        <span style="font-weight: 500;">{{ $item->cidade ?? 'Não informado' }}</span>
                        <span style="font-weight: 700; color: var(--primary);">{{ $item->total }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p style="color: var(--text-secondary); text-align: center; padding: 20px;">Nenhum dado disponível</p>
        @endif
    </div>

    <!-- Por Congregação -->
    <div class="card-modern">
        <h5 style="font-weight: 600; color: var(--text); margin-bottom: 16px;">
            <i class="fas fa-church" style="color: var(--primary);"></i> 
            Distribuição por Congregação
        </h5>
        
        @if($porCongregacao->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 8px;">
                @foreach($porCongregacao as $item)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; background: var(--bg); border-radius: var(--radius-sm);">
                        <span style="font-weight: 500;">{{ $item->congregacao ?? 'Não informado' }}</span>
                        <span style="font-weight: 700; color: var(--primary);">{{ $item->total }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p style="color: var(--text-secondary); text-align: center; padding: 20px;">Nenhum dado disponível</p>
        @endif
    </div>

    <!-- Por Nível -->
    <div class="card-modern">
        <h5 style="font-weight: 600; color: var(--text); margin-bottom: 16px;">
            <i class="fas fa-user-shield" style="color: var(--primary);"></i> 
            Distribuição por Nível
        </h5>
        
        @if($porNivel->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 8px;">
                @foreach($porNivel as $item)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; background: var(--bg); border-radius: var(--radius-sm);">
                        <span style="font-weight: 500;">
                            @switch($item->nivel)
                                @case('admin')
                                    👑 Administrador
                                    @break
                                @case('secretario')
                                    📋 Secretário
                                    @break
                                @default
                                    👤 Membro
                            @endswitch
                        </span>
                        <span style="font-weight: 700; color: var(--primary);">{{ $item->total }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p style="color: var(--text-secondary); text-align: center; padding: 20px;">Nenhum dado disponível</p>
        @endif
    </div>

    <!-- Aniversariantes do Mês -->
    <div class="card-modern" style="grid-column: 1 / -1;">
        <h5 style="font-weight: 600; color: var(--text); margin-bottom: 16px;">
            <i class="fas fa-birthday-cake" style="color: var(--accent);"></i> 
            Aniversariantes do Mês ({{ now()->format('F') }})
        </h5>
        
        @if($aniversariantes->count() > 0)
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 12px;">
                @foreach($aniversariantes as $membro)
                    <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background: var(--bg); border-radius: var(--radius-sm);">
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, var(--accent), #f39c12); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 0.9rem; flex-shrink: 0;">
                            {{ substr($membro->nome, 0, 1) }}
                        </div>
                        <div>
                            <div style="font-weight: 600; font-size: 0.9rem;">{{ $membro->nome }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-secondary);">
                                {{ \Carbon\Carbon::parse($membro->dataNascimento)->format('d/m/Y') }}
                                <span style="margin-left: 8px; background: var(--accent); color: #1A1A2E; padding: 1px 8px; border-radius: 10px; font-size: 0.65rem; font-weight: 700;">
                                    {{ \Carbon\Carbon::parse($membro->dataNascimento)->age }} anos
                                </span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p style="color: var(--text-secondary); text-align: center; padding: 20px;">Nenhum aniversariante este mês</p>
        @endif
    </div>
</div>

<style>
    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    
    @media (max-width: 768px) {
        .grid-2 {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection