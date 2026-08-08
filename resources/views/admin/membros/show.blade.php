@extends('layouts.app')

@section('title', 'Detalhes do Membro - Admin')
@section('page-title', 'Detalhes do Membro')
@section('page-subtitle', 'Visualize todas as informações do membro')

@section('styles')
<style>
    .member-detail-container {
        max-width: 1000px;
        margin: 0 auto;
    }

    .member-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        overflow: hidden;
    }

    .member-header {
        background: linear-gradient(145deg, #c9a84c, #b8960f);
        padding: 30px;
        color: white;
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }

    .member-avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: white;
        flex-shrink: 0;
        overflow: hidden;
    }

    .member-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .member-avatar .avatar-placeholder {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        font-size: 2.5rem;
    }

    .member-header-info {
        flex: 1;
    }

    .member-header-info h1 {
        margin: 0;
        font-size: 1.8rem;
        font-weight: 700;
    }

    .member-header-info .matricula {
        font-size: 0.9rem;
        opacity: 0.9;
        margin: 5px 0 0;
    }

    .member-header-info .status-badge {
        display: inline-block;
        padding: 4px 14px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        background: rgba(255,255,255,0.2);
        color: white;
        margin-top: 8px;
    }

    .member-header-info .nivel-badge {
        display: inline-block;
        padding: 4px 14px;
        border-radius: 20px;
        font-size: 0.65rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 8px;
        margin-left: 8px;
        background: rgba(255,255,255,0.3);
        color: white;
    }

    .member-body {
        padding: 30px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .info-item {
        padding: 15px;
        background: #faf8f5;
        border-radius: 12px;
        border: 1px solid #e8e4db;
    }

    .info-item .label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #999;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .info-item .value {
        font-size: 1rem;
        font-weight: 600;
        color: #1a1a2e;
    }

    .info-item .value .status-badge {
        display: inline-block;
        padding: 4px 14px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-ativo { background: #e8f5e9; color: #2e7d32; }
    .status-inativo { background: #ffebee; color: #c62828; }
    .status-pendente { background: #fff3e0; color: #e65100; }
    .status-transferido { background: #e3f2fd; color: #0d47a1; }

    .bio-section {
        background: #faf8f5;
        padding: 15px;
        border-radius: 12px;
        border: 1px solid #e8e4db;
        margin-bottom: 30px;
    }

    .bio-section .label {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #999;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .bio-section .value {
        font-size: 0.95rem;
        color: #1a1a2e;
        line-height: 1.6;
        white-space: pre-wrap;
    }

    .member-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        padding-top: 20px;
        border-top: 2px solid #e8e4db;
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

    .btn-admin-primary {
        background: linear-gradient(145deg, #c9a84c, #b8960f);
    }

    .btn-admin-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(201, 168, 76, 0.4);
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

    .btn-admin-danger {
        background: linear-gradient(145deg, #f44336, #c62828);
    }

    .btn-admin-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(244, 67, 54, 0.4);
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

    .alert-danger {
        background: #ffebee;
        color: #c62828;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 20px;
        border-left: 4px solid #c62828;
    }

    .alert-danger .btn-admin {
        margin-top: 10px;
    }

    @media (max-width: 768px) {
        .member-header {
            flex-direction: column;
            text-align: center;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .member-actions {
            flex-direction: column;
        }

        .member-actions .btn-admin {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endsection

@section('content')
<div class="member-detail-container">
    {{-- ⭐ VERIFICA PERMISSÃO --}}
    @if(!auth()->user()?->pode('ver_membro', $membro))
        <div class="alert-danger">
            <strong>⛔ Acesso Negado</strong>
            <p>Você não tem permissão para visualizar os detalhes deste membro.</p>
            <a href="{{ route('admin.membros.index') }}" class="btn-admin btn-admin-secondary" style="display: inline-block;">
                <span>🔙</span> Voltar
            </a>
        </div>
    @else
        <div class="member-card">
            <!-- Header -->
            <div class="member-header">
                <div class="member-avatar">
                    @php
                        $fotoNome = $membro->foto ?? null;
                        $fotoUrl = $fotoNome ? route('imagem.foto', ['filename' => $fotoNome]) : null;
                        $inicial = substr($membro->nome, 0, 1);
                    @endphp
                    
                    @if($fotoUrl)
                        <img src="{{ $fotoUrl }}" 
                             alt="{{ $membro->nome }}"
                             onerror="this.style.display='none'; this.parentElement.innerHTML='<span class=\'avatar-placeholder\'>{{ $inicial }}</span>';">
                    @else
                        <span class="avatar-placeholder">{{ $inicial }}</span>
                    @endif
                </div>
                <div class="member-header-info">
                    <h1>{{ $membro->nome }}</h1>
                    <p class="matricula">Matrícula #{{ $membro->matricula }}</p>
                    <div>
                        <span class="status-badge">{{ $membro->status ?? 'INATIVO' }}</span>
                        
                        {{-- ⭐ BADGE DE NÍVEL --}}
                        @if(isset($membro->nivel))
                            <span class="nivel-badge">
                                @if($membro->nivel === 'admin')
                                    👑 Administrador
                                @elseif($membro->nivel === 'secretario')
                                    📋 Secretário
                                @else
                                    👤 Membro
                                @endif
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Body -->
            <div class="member-body">
                <div class="info-grid">
                    <!-- Dados Pessoais -->
                    <div class="info-item">
                        <div class="label">📋 Matrícula</div>
                        <div class="value">#{{ $membro->matricula }}</div>
                    </div>

                    <div class="info-item">
                        <div class="label">👤 Nome Completo</div>
                        <div class="value">{{ $membro->nome }}</div>
                    </div>

                    @if($membro->nome_carteira)
                    <div class="info-item">
                        <div class="label">📛 Nome para Carteira</div>
                        <div class="value">{{ $membro->nome_carteira }}</div>
                    </div>
                    @endif

                    <div class="info-item">
                        <div class="label">📧 E-mail</div>
                        <div class="value">{{ $membro->email ?? '—' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="label">📱 Telefone</div>
                        <div class="value">{{ $membro->telefone ?? '—' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="label">🆔 Documento</div>
                        <div class="value">{{ $membro->documento ?? '—' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="label">🎂 Data de Nascimento</div>
                        <div class="value">{{ $membro->dataNascimento ? date('d/m/Y', strtotime($membro->dataNascimento)) : '—' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="label">💍 Estado Civil</div>
                        <div class="value">{{ $membro->estadoCivil ?? '—' }}</div>
                    </div>

                    @if($membro->mae)
                    <div class="info-item">
                        <div class="label">👩 Mãe</div>
                        <div class="value">{{ $membro->mae }}</div>
                    </div>
                    @endif

                    @if($membro->pai)
                    <div class="info-item">
                        <div class="label">👨 Pai</div>
                        <div class="value">{{ $membro->pai }}</div>
                    </div>
                    @endif

                    <!-- Endereço -->
                    <div class="info-item">
                        <div class="label">📍 Endereço</div>
                        <div class="value">{{ $membro->endereco ?? '—' }}, {{ $membro->numero ?? '—' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="label">🏘️ Bairro</div>
                        <div class="value">{{ $membro->bairro ?? '—' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="label">📮 CEP</div>
                        <div class="value">{{ $membro->cep ?? '—' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="label">🏙️ Cidade</div>
                        <div class="value">{{ $membro->cidade ?? '—' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="label">🗺️ UF</div>
                        <div class="value">{{ $membro->uf ?? '—' }}</div>
                    </div>

                    <!-- Dados da Igreja -->
                    <div class="info-item">
                        <div class="label">⛪ Congregação</div>
                        <div class="value">{{ $membro->congregacao ?? '—' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="label">📌 Função</div>
                        <div class="value">{{ $membro->funcao ?? '—' }}</div>
                    </div>

                    @if($membro->dataBatismo)
                    <div class="info-item">
                        <div class="label">💧 Data do Batismo</div>
                        <div class="value">{{ date('d/m/Y', strtotime($membro->dataBatismo)) }}</div>
                    </div>
                    @endif

                    @if($membro->data_Consagracao)
                    <div class="info-item">
                        <div class="label">🕊️ Data da Consagração</div>
                        <div class="value">{{ date('d/m/Y', strtotime($membro->data_Consagracao)) }}</div>
                    </div>
                    @endif

                    <div class="info-item">
                        <div class="label">📅 Data de Cadastro</div>
                        <div class="value">{{ $membro->created_at ? $membro->created_at->format('d/m/Y H:i') : '—' }}</div>
                    </div>

                    <div class="info-item">
                        <div class="label">🔔 Status</div>
                        <div class="value">
                            <span class="status-badge status-{{ strtolower($membro->status ?? 'inativo') }}">
                                {{ $membro->status ?? 'INATIVO' }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Biografia -->
                @if($membro->bio)
                <div class="bio-section">
                    <div class="label">📝 Biografia / Notas</div>
                    <div class="value">{{ $membro->bio }}</div>
                </div>
                @endif

                <!-- Actions -->
                <div class="member-actions">
                    {{-- ⭐ VER POSTS - APENAS QUEM PODE VER DASHBOARD --}}
                    @if(auth()->user()?->pode('dashboard'))
                        <a href="{{ route('admin.membros.posts', $membro->matricula) }}" class="btn-admin btn-admin-success">
                            <span>📝</span> Ver Posts
                        </a>
                    @endif

                    {{-- ⭐ EDITAR - APENAS QUEM PODE EDITAR ESTE MEMBRO --}}
                    @if(auth()->user()?->pode('editar_membro', $membro))
                        <a href="{{ route('admin.membros.edit', $membro->matricula) }}" class="btn-admin btn-admin-primary">
                            <span>✏️</span> Editar Membro
                        </a>
                    @endif

                    <a href="{{ route('admin.membros.index') }}" class="btn-admin btn-admin-secondary">
                        <span>🔙</span> Voltar
                    </a>

                    {{-- ⭐ REMOVER - APENAS ADMIN --}}
                    @if(auth()->user()?->pode('excluir_membro'))
                        <form action="{{ route('admin.membros.destroy', $membro->matricula) }}" method="POST" style="display: inline; margin-left: auto;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-admin btn-admin-danger" onclick="return confirm('Tem certeza que deseja remover o membro {{ addslashes($membro->nome) }}?')">
                                <span>🗑️</span> Remover Membro
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    console.log('👤 Visualizando membro: {{ $membro->nome }} (Matrícula #{{ $membro->matricula }})');
    @if(isset($membro->nivel))
        console.log('🏷️ Nível: {{ $membro->nivel }}');
    @endif
</script>
@endpush
@endsection