@extends('layouts.admin')

@section('title', 'Estatísticas de Secretários - Conexão Igreja')
@section('page-title', '📊 Estatísticas de Secretários')
@section('page-subtitle', 'Análise dos secretários da igreja')

@section('content')
<div class="grid-2">
    <!-- Cards de Estatísticas -->
    <div class="card-modern">
        <h5 style="font-weight: 600; color: var(--text); margin-bottom: 16px;">
            <i class="fas fa-user-tie" style="color: var(--primary);"></i> 
            Visão Geral
        </h5>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(100px, 1fr)); gap: 12px;">
            <div style="background: var(--bg); padding: 16px; border-radius: var(--radius-sm); text-align: center;">
                <div style="font-size: 1.8rem; font-weight: 800; color: var(--primary);">{{ $totalSecretarios ?? 0 }}</div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">Total</div>
            </div>
            <div style="background: var(--bg); padding: 16px; border-radius: var(--radius-sm); text-align: center;">
                <div style="font-size: 1.8rem; font-weight: 800; color: var(--success);">{{ $secretariosAtivos ?? 0 }}</div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">Ativos</div>
            </div>
            <div style="background: var(--bg); padding: 16px; border-radius: var(--radius-sm); text-align: center;">
                <div style="font-size: 1.8rem; font-weight: 800; color: var(--danger);">{{ $secretariosInativos ?? 0 }}</div>
                <div style="font-size: 0.75rem; color: var(--text-secondary);">Inativos</div>
            </div>
        </div>
    </div>

    <!-- Por Congregação -->
    <div class="card-modern">
        <h5 style="font-weight: 600; color: var(--text); margin-bottom: 16px;">
            <i class="fas fa-church" style="color: var(--primary);"></i> 
            Distribuição por Congregação
        </h5>
        
        @if(isset($porCongregacao) && $porCongregacao->count() > 0)
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

    <!-- Por Função -->
    <div class="card-modern" style="grid-column: 1 / -1;">
        <h5 style="font-weight: 600; color: var(--text); margin-bottom: 16px;">
            <i class="fas fa-briefcase" style="color: var(--primary);"></i> 
            Distribuição por Função
        </h5>
        
        @if(isset($porFuncao) && $porFuncao->count() > 0)
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