@extends('layouts.app')

@section('title', 'Membros - Conexão Igreja')
@section('page-title', '👥 Membros')
@section('page-subtitle', 'Conheça os membros da nossa comunidade')

@section('content')
<div class="card-modern">
    <!-- Barra de Busca -->
    <form method="GET" action="{{ route('membros.index') }}" style="display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 20px;">
        <input type="text" name="search" placeholder="Buscar por nome, matrícula, cidade..." value="{{ request('search') }}" 
               style="flex: 1; min-width: 200px; padding: 10px 14px; border-radius: var(--radius-sm); border: 2px solid var(--border); background: var(--bg-card); color: var(--text);">
        
        <select name="funcao" style="padding: 10px 14px; border-radius: var(--radius-sm); border: 2px solid var(--border); background: var(--bg-card); color: var(--text);">
            <option value="">Todas Funções</option>
            @foreach($funcoes as $funcao)
                <option value="{{ $funcao }}" {{ request('funcao') == $funcao ? 'selected' : '' }}>{{ $funcao }}</option>
            @endforeach
        </select>
        
        <select name="cidade" style="padding: 10px 14px; border-radius: var(--radius-sm); border: 2px solid var(--border); background: var(--bg-card); color: var(--text);">
            <option value="">Todas Cidades</option>
            @foreach($cidades as $cidade)
                <option value="{{ $cidade }}" {{ request('cidade') == $cidade ? 'selected' : '' }}>{{ $cidade }}</option>
            @endforeach
        </select>
        
        <button type="submit" class="btn-modern btn-modern-primary">
            <i class="fas fa-search"></i> Buscar
        </button>
        
        @if(request()->has('search') || request()->has('funcao') || request()->has('cidade'))
            <a href="{{ route('membros.index') }}" class="btn-modern btn-modern-outline">
                <i class="fas fa-times"></i> Limpar
            </a>
        @endif
    </form>
    
    <!-- Lista de Membros -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px;">
        @forelse($membros as $membroItem)
            <div class="card-modern" style="padding: 16px; display: flex; align-items: center; gap: 14px;">
                <div style="width: 50px; height: 50px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--primary-light)); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 1.2rem; flex-shrink: 0;">
                    {{ substr($membroItem->nome, 0, 1) }}
                </div>
                <div style="flex: 1; min-width: 0;">
                    <div style="font-weight: 600; font-size: 1rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                        {{ $membroItem->nome }}
                    </div>
                    <div style="font-size: 0.8rem; color: var(--text-secondary);">
                        <span style="background: rgba(108, 60, 225, 0.08); padding: 2px 8px; border-radius: 12px;">
                            {{ $membroItem->funcao ?? 'Membro' }}
                        </span>
                        @if($membroItem->cidade)
                            <span style="margin-left: 8px;">
                                <i class="fas fa-map-marker-alt"></i> {{ $membroItem->cidade }}
                            </span>
                        @endif
                    </div>
                </div>
                <a href="{{ route('membros.show', $membroItem->matricula) }}" class="btn-modern btn-modern-outline" style="padding: 6px 14px; font-size: 0.8rem;">
                    <i class="fas fa-eye"></i>
                </a>
            </div>
        @empty
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px 20px; color: var(--text-secondary);">
                <i class="fas fa-users" style="font-size: 2.5rem; display: block; margin-bottom: 12px; opacity: 0.3;"></i>
                <p style="font-size: 1rem;">Nenhum membro encontrado</p>
            </div>
        @endforelse
    </div>
    
    <div style="margin-top: 20px;">
        {{ $membros->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection