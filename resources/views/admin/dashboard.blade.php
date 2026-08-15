@extends('layouts.admin')

@section('title', 'Dashboard - Conexão Igreja')
@section('page-title', '📊 Dashboard')
@section('page-subtitle', 'Visão geral da sua igreja')

@section('styles')
<style>
    .stat-card {
        background: var(--bg-card);
        border-radius: var(--radius);
        padding: 20px 24px;
        border: 1px solid var(--border);
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-hover);
    }
    
    .stat-card .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        color: white;
        flex-shrink: 0;
    }
    
    .stat-card .stat-icon.primary { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); }
    .stat-card .stat-icon.success { background: linear-gradient(135deg, var(--success), #27AE60); }
    .stat-card .stat-icon.danger { background: linear-gradient(135deg, var(--danger), #C0392B); }
    .stat-card .stat-icon.warning { background: linear-gradient(135deg, var(--warning), #E67E22); }
    .stat-card .stat-icon.info { background: linear-gradient(135deg, var(--info), #2980B9); }
    .stat-card .stat-icon.accent { background: linear-gradient(135deg, var(--accent), #F39C12); }
    
    .stat-card .stat-number {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1.2;
    }
    
    .stat-card .stat-label {
        font-size: 0.85rem;
        color: var(--text-secondary);
        font-weight: 500;
    }
    
    .stat-card .stat-change {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 2px 10px;
        border-radius: 20px;
        display: inline-block;
    }
    
    .stat-card .stat-change.positive {
        background: rgba(46, 204, 113, 0.15);
        color: var(--success);
    }
    
    .stat-card .stat-change.negative {
        background: rgba(231, 76, 60, 0.15);
        color: var(--danger);
    }
    
    .grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }
    
    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    
    .chart-container {
        position: relative;
        height: 280px;
    }
    
    .activity-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 12px 16px;
        background: var(--bg);
        border-radius: var(--radius-sm);
        border-left: 3px solid var(--primary);
        transition: var(--transition);
    }
    
    .activity-item:hover {
        background: var(--border);
    }
    
    .activity-item .activity-icon {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }
    
    .activity-item .activity-icon.post { background: rgba(108, 60, 225, 0.15); color: var(--primary); }
    .activity-item .activity-icon.user { background: rgba(46, 204, 113, 0.15); color: var(--success); }
    .activity-item .activity-icon.comment { background: rgba(52, 152, 219, 0.15); color: var(--info); }
    .activity-item .activity-icon.like { background: rgba(231, 76, 60, 0.15); color: var(--danger); }
    
    .activity-item .activity-content {
        flex: 1;
    }
    
    .activity-item .activity-content .title {
        font-weight: 600;
        font-size: 0.9rem;
    }
    
    .activity-item .activity-content .subtitle {
        font-size: 0.75rem;
        color: var(--text-secondary);
    }
    
    .activity-item .activity-time {
        font-size: 0.7rem;
        color: var(--text-secondary);
        white-space: nowrap;
    }
    
    @media (max-width: 992px) {
        .grid-3 {
            grid-template-columns: repeat(2, 1fr);
        }
        .grid-2 {
            grid-template-columns: 1fr;
        }
    }
    
    @media (max-width: 600px) {
        .grid-3 {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="grid-3" style="margin-bottom: 24px;">
    <!-- Total de Membros -->
    <div class="stat-card">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div class="stat-icon primary">
                <i class="fas fa-users"></i>
            </div>
            <div style="flex: 1;">
                <div class="stat-number">{{ $stats['total_membros'] ?? 0 }}</div>
                <div class="stat-label">Total de Membros</div>
            </div>
        </div>
        <div style="margin-top: 12px; display: flex; gap: 8px;">
            <span class="stat-change positive">
                <i class="fas fa-arrow-up"></i> {{ $stats['ativos'] ?? 0 }} ativos
            </span>
            <span class="stat-change negative">
                <i class="fas fa-arrow-down"></i> {{ $stats['inativos'] ?? 0 }} inativos
            </span>
        </div>
    </div>

    <!-- Secretários -->
    <div class="stat-card">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div class="stat-icon warning">
                <i class="fas fa-user-tie"></i>
            </div>
            <div style="flex: 1;">
                <div class="stat-number">{{ $stats['secretarios'] ?? 0 }}</div>
                <div class="stat-label">Secretários</div>
            </div>
        </div>
        <div style="margin-top: 12px;">
            <span class="stat-change positive">
                <i class="fas fa-check-circle"></i> Gerenciando a igreja
            </span>
        </div>
    </div>

    <!-- Publicações -->
    <div class="stat-card">
        <div style="display: flex; align-items: center; gap: 16px;">
            <div class="stat-icon info">
                <i class="fas fa-newspaper"></i>
            </div>
            <div style="flex: 1;">
                <div class="stat-number">{{ $stats['total_publicacoes'] ?? 0 }}</div>
                <div class="stat-label">Publicações</div>
            </div>
        </div>
        <div style="margin-top: 12px; display: flex; gap: 8px;">
            <span class="stat-change positive">
                <i class="fas fa-comment"></i> {{ $stats['total_comentarios'] ?? 0 }} comentários
            </span>
        </div>
    </div>
</div>

<div class="grid-2">
    <!-- Distribuição por Função -->
    <div class="stat-card">
        <h5 style="font-weight: 600; color: var(--text); margin-bottom: 16px;">
            <i class="fas fa-briefcase" style="color: var(--primary);"></i> 
            Distribuição por Função
        </h5>
        
        @if(isset($porFuncao) && $porFuncao->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 8px;">
                @foreach($porFuncao->take(8) as $item)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; background: var(--bg); border-radius: var(--radius-sm);">
                        <span style="font-weight: 500;">{{ $item->funcao ?? 'Não informado' }}</span>
                        <span style="font-weight: 700; color: var(--primary);">{{ $item->total }}</span>
                    </div>
                @endforeach
            </div>
            @if($porFuncao->count() > 8)
                <div style="text-align: center; margin-top: 12px;">
                    <span style="font-size: 0.8rem; color: var(--text-secondary);">
                        + {{ $porFuncao->count() - 8 }} outras funções
                    </span>
                </div>
            @endif
        @else
            <p style="color: var(--text-secondary); text-align: center; padding: 20px;">Nenhum dado disponível</p>
        @endif
    </div>

    <!-- Distribuição por Congregação -->
    <div class="stat-card">
        <h5 style="font-weight: 600; color: var(--text); margin-bottom: 16px;">
            <i class="fas fa-church" style="color: var(--primary);"></i> 
            Distribuição por Congregação
        </h5>
        
        @if(isset($porCongregacao) && $porCongregacao->count() > 0)
            <div style="display: flex; flex-direction: column; gap: 8px;">
                @foreach($porCongregacao->take(6) as $item)
                    <div style="display: flex; justify-content: space-between; align-items: center; padding: 8px 12px; background: var(--bg); border-radius: var(--radius-sm);">
                        <span style="font-weight: 500;">{{ $item->congregacao ?? 'Não informado' }}</span>
                        <span style="font-weight: 700; color: var(--primary);">{{ $item->total }}</span>
                    </div>
                @endforeach
            </div>
            @if($porCongregacao->count() > 6)
                <div style="text-align: center; margin-top: 12px;">
                    <span style="font-size: 0.8rem; color: var(--text-secondary);">
                        + {{ $porCongregacao->count() - 6 }} outras congregações
                    </span>
                </div>
            @endif
        @else
            <p style="color: var(--text-secondary); text-align: center; padding: 20px;">Nenhum dado disponível</p>
        @endif
    </div>
</div>

<!-- Aniversariantes do Mês -->
<div class="stat-card" style="margin-top: 24px;">
    <h5 style="font-weight: 600; color: var(--text); margin-bottom: 16px;">
        <i class="fas fa-birthday-cake" style="color: var(--accent);"></i> 
        Aniversariantes do Mês ({{ now()->format('F') }})
    </h5>
    
    @if(isset($aniversariantes) && $aniversariantes->count() > 0)
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 12px;">
            @foreach($aniversariantes as $membro)
                <div style="display: flex; align-items: center; gap: 12px; padding: 12px; background: var(--bg); border-radius: var(--radius-sm);">
                    <div style="width: 40px; height: 40px; border-radius: 50%; background: linear-gradient(135deg, var(--accent), #f39c12); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 0.9rem; flex-shrink: 0;">
                        {{ substr($membro->nome, 0, 1) }}
                    </div>
                    <div>
                        <div style="font-weight: 600; font-size: 0.9rem;">{{ $membro->nome }}</div>
                        <div style="font-size: 0.75rem; color: var(--text-secondary);">
                            {{ \Carbon\Carbon::parse($membro->dataNascimento)->format('d/m/Y') }}
                            <span style="margin-left: 8px; background: var(--accent); color: #1A1A2E; padding: 1px 8px; border-radius: 10px; font-size: 0.6rem; font-weight: 700;">
                                {{ \Carbon\Carbon::parse($membro->dataNascimento)->age }} anos
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <p style="color: var(--text-secondary); text-align: center; padding: 20px;">
            <i class="fas fa-calendar-alt" style="opacity: 0.3; display: block; font-size: 1.5rem; margin-bottom: 8px;"></i>
            Nenhum aniversariante este mês
        </p>
    @endif
</div>

<!-- Atividades Recentes -->
<div class="stat-card" style="margin-top: 24px;">
    <h5 style="font-weight: 600; color: var(--text); margin-bottom: 16px;">
        <i class="fas fa-clock" style="color: var(--primary);"></i> 
        Atividades Recentes
    </h5>
    
    @if(isset($atividades) && $atividades->count() > 0)
        <div style="display: flex; flex-direction: column; gap: 10px;">
            @foreach($atividades->take(10) as $atividade)
                <div class="activity-item">
                    <div class="activity-icon {{ $atividade['tipo'] ?? 'post' }}">
                        <i class="fas fa-{{ $atividade['icone'] ?? 'newspaper' }}"></i>
                    </div>
                    <div class="activity-content">
                        <div class="title">{{ $atividade['titulo'] ?? 'Atividade' }}</div>
                        <div class="subtitle">{{ $atividade['subtitle'] ?? '' }}</div>
                    </div>
                    <div class="activity-time">{{ $atividade['tempo'] ?? 'agora' }}</div>
                </div>
            @endforeach
        </div>
    @else
        <p style="color: var(--text-secondary); text-align: center; padding: 20px;">
            <i class="fas fa-inbox" style="opacity: 0.3; display: block; font-size: 1.5rem; margin-bottom: 8px;"></i>
            Nenhuma atividade recente
        </p>
    @endif
</div>
@endsection

@push('scripts')
<script>
    // Auto-fechar toasts
    document.querySelectorAll('.toast').forEach(toast => {
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-20px)';
            setTimeout(() => toast.remove(), 400);
        }, 5000);
    });
</script>
@endpush