@extends('layouts.app')

@section('title', 'Detalhes do Membro - ' . $membro->nome)
@section('page-title', 'Detalhes do Membro')
@section('page-subtitle', 'Visualize as informações completas do membro')

@section('styles')
<style>
    /* ============================================================
       PERFIL MODERNO - ESTILO REDE SOCIAL
       ============================================================ */
    .perfil-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .perfil-card {
        background: var(--bg-card);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow);
        overflow: hidden;
        border: 1px solid var(--border);
        transition: var(--transition);
    }

    .perfil-card:hover {
        box-shadow: var(--shadow-hover);
    }

    .perfil-header {
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        padding: 30px 30px 0 30px;
        position: relative;
        min-height: 120px;
    }

    .perfil-header .perfil-actions-top {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        position: absolute;
        top: 12px;
        right: 16px;
    }

    .perfil-header .perfil-actions-top .icon-btn {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        border: none;
        background: rgba(255,255,255,0.15);
        color: white;
        cursor: pointer;
        transition: var(--transition);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        backdrop-filter: blur(4px);
        text-decoration: none;
    }

    .perfil-header .perfil-actions-top .icon-btn:hover {
        background: rgba(255,255,255,0.3);
        transform: scale(1.1);
    }

    .perfil-header .perfil-actions-top .icon-btn.danger:hover {
        background: rgba(231, 76, 60, 0.6);
    }

    .perfil-header .perfil-actions-top .icon-btn.warning:hover {
        background: rgba(241, 196, 15, 0.6);
    }

    .perfil-avatar-wrapper {
        display: flex;
        align-items: flex-end;
        gap: 20px;
        margin-top: -50px;
        position: relative;
        z-index: 2;
    }

    .perfil-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 4px solid var(--bg-card);
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        font-weight: 700;
        color: white;
        flex-shrink: 0;
        overflow: hidden;
        box-shadow: var(--shadow);
        position: relative;
    }

    .perfil-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .perfil-avatar .status-dot {
        position: absolute;
        bottom: 4px;
        right: 4px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        border: 3px solid var(--bg-card);
        background: #4CAF50;
    }

    .perfil-info {
        padding-bottom: 12px;
        flex: 1;
        min-width: 0;
    }

    .perfil-info .perfil-nome {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text);
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .perfil-info .perfil-nome .badge-funcao {
        font-size: 0.7rem;
        font-weight: 600;
        padding: 2px 14px;
        border-radius: 20px;
        background: var(--primary);
        color: white;
        letter-spacing: 0.5px;
    }

    .perfil-info .perfil-matricula {
        font-size: 0.85rem;
        color: var(--text-secondary);
        margin-top: 2px;
    }

    .perfil-info .perfil-local {
        font-size: 0.8rem;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        gap: 4px;
        margin-top: 2px;
    }

    .perfil-info .perfil-local i {
        font-size: 0.7rem;
        color: var(--primary);
    }

    .status-badge-modern {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 14px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-badge-modern .dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
    }

    .status-ativo .dot { background: #4CAF50; }
    .status-ativo { background: #e8f5e9; color: #2e7d32; }

    .status-inativo .dot { background: #f44336; }
    .status-inativo { background: #ffebee; color: #c62828; }

    .status-pendente .dot { background: #FF9800; }
    .status-pendente { background: #fff3e0; color: #e65100; }

    .status-transferido .dot { background: #2196F3; }
    .status-transferido { background: #e3f2fd; color: #0d47a1; }

    .perfil-body {
        padding: 20px 30px 30px 30px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px 30px;
        margin: 16px 0 24px 0;
    }

    .info-item {
        display: flex;
        flex-direction: column;
        gap: 2px;
        padding: 8px 0;
        border-bottom: 1px solid var(--border);
    }

    .info-item .label {
        font-size: 0.65rem;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: var(--text-secondary);
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .info-item .label i {
        font-size: 0.7rem;
        color: var(--primary);
        width: 16px;
    }

    .info-item .value {
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--text);
    }

    .perfil-actions {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
        padding-top: 20px;
        border-top: 1px solid var(--border);
        justify-content: center;
    }

    .perfil-actions .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 8px 16px;
        border: none;
        border-radius: 30px;
        font-size: 0.75rem;
        font-weight: 600;
        cursor: pointer;
        transition: var(--transition);
        text-decoration: none;
        color: var(--text-secondary);
        background: var(--bg);
        border: 1px solid transparent;
    }

    .perfil-actions .action-btn i {
        font-size: 1rem;
        transition: var(--transition);
    }

    .perfil-actions .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow);
        background: var(--bg-card);
        border-color: var(--border);
        color: var(--text);
    }

    .perfil-actions .action-btn:hover i {
        transform: scale(1.15);
    }

    .perfil-actions .action-btn.primary {
        background: var(--primary);
        color: white;
    }

    .perfil-actions .action-btn.primary:hover {
        background: var(--primary-dark);
        color: white;
        border-color: var(--primary);
    }

    .perfil-actions .action-btn.danger {
        color: #dc3545;
    }

    .perfil-actions .action-btn.danger:hover {
        background: #dc3545;
        color: white;
        border-color: #dc3545;
    }

    .perfil-actions .action-btn.info {
        color: #17a2b8;
    }

    .perfil-actions .action-btn.info:hover {
        background: #17a2b8;
        color: white;
        border-color: #17a2b8;
    }

    .perfil-actions .action-btn.whatsapp {
        color: #25d366;
    }

    .perfil-actions .action-btn.whatsapp:hover {
        background: #25d366;
        color: white;
        border-color: #25d366;
    }

    .perfil-actions .action-btn.curtido {
        background: #4CAF50;
        color: white;
        border-color: #4CAF50;
    }

    .perfil-actions .action-btn.seguindo {
        background: #6c757d;
        color: white;
        border-color: #6c757d;
    }

    /* ===== TOAST ===== */
    .toast-custom {
        position: fixed;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.85);
        color: white;
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.9rem;
        z-index: 9999;
        backdrop-filter: blur(10px);
        animation: fadeInUp 0.4s ease;
        display: none;
        border: 1px solid rgba(212, 175, 55, 0.2);
        max-width: 90%;
        text-align: center;
    }

    .toast-custom i { margin-right: 8px; }
    .toast-custom.success { border-color: #4CAF50; }
    .toast-custom.success i { color: #4CAF50; }
    .toast-custom.error { border-color: #f44336; }
    .toast-custom.error i { color: #f44336; }
    .toast-custom.info { border-color: #2196F3; }
    .toast-custom.info i { color: #2196F3; }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateX(-50%) translateY(20px); }
        to { opacity: 1; transform: translateX(-50%) translateY(0); }
    }

    /* ===== RESPONSIVO ===== */
    @media (max-width: 768px) {
        .perfil-header { padding: 20px 20px 0 20px; min-height: 80px; }
        .perfil-avatar-wrapper { margin-top: -40px; gap: 14px; flex-wrap: wrap; }
        .perfil-avatar { width: 90px; height: 90px; font-size: 36px; }
        .perfil-info .perfil-nome { font-size: 1.2rem; }
        .perfil-body { padding: 16px 20px 20px 20px; }
        .info-grid { grid-template-columns: 1fr 1fr; gap: 12px 20px; }
        .info-item { padding: 6px 0; }
        .info-item .value { font-size: 0.8rem; }
        .perfil-actions { gap: 4px; }
        .perfil-actions .action-btn { padding: 6px 12px; font-size: 0.65rem; }
        .perfil-actions .action-btn i { font-size: 0.85rem; }
        .perfil-actions .action-btn .btn-text { display: none; }
        .perfil-header .perfil-actions-top .icon-btn { width: 28px; height: 28px; font-size: 0.7rem; }
    }

    @media (max-width: 480px) {
        .info-grid { grid-template-columns: 1fr; }
        .perfil-avatar-wrapper { flex-direction: column; align-items: center; text-align: center; }
        .perfil-info .perfil-nome { justify-content: center; }
        .perfil-info .perfil-local { justify-content: center; }
        .perfil-actions { justify-content: center; }
        .perfil-actions .action-btn { padding: 8px 14px; }
        .perfil-actions .action-btn .btn-text { display: inline; }
    }

    body.dark .perfil-actions .action-btn {
        background: var(--bg-card);
        color: var(--text-secondary);
        border-color: var(--border);
    }

    body.dark .perfil-actions .action-btn:hover {
        background: var(--bg);
        color: var(--text);
    }

    body.dark .perfil-actions .action-btn.primary {
        background: var(--primary);
        color: white;
    }

    body.dark .status-ativo { background: #1b3a1b; color: #66bb6a; }
    body.dark .status-inativo { background: #3a1b1b; color: #ef5350; }
    body.dark .status-pendente { background: #3a2a1b; color: #ffa726; }
    body.dark .status-transferido { background: #1b2a3a; color: #42a5f5; }
</style>
@endsection

@section('content')
<div class="perfil-container">
    <div class="perfil-card">
        
        <!-- HEADER -->
        <div class="perfil-header">
            <div class="perfil-actions-top">
                @if(Auth::check() && Auth::user()->pode('editar_membro', $membro))
                    <button class="icon-btn" onclick="window.print()" title="Imprimir">
                        <i class="fas fa-print"></i>
                    </button>
                @endif
                
                @if(Auth::check() && Auth::user()->pode('editar_membro', $membro))
                    <a href="{{ route('admin.membros.edit', $membro->matricula) }}" 
                       class="icon-btn warning" title="Editar">
                        <i class="fas fa-edit"></i>
                    </a>
                @endif
                
                @if(Auth::check() && Auth::user()->pode('excluir_membro'))
                    <button class="icon-btn danger" 
                            onclick="excluirMembro('{{ $membro->matricula }}', '{{ addslashes($membro->nome) }}')" 
                            title="Excluir">
                        <i class="fas fa-trash"></i>
                    </button>
                @endif
            </div>
        </div>

        <!-- AVATAR E INFO -->
        <div class="perfil-body">
            <div class="perfil-avatar-wrapper">
                <div class="perfil-avatar">
                    @php
                        $fotoNome = $membro->foto ?? null;
                        $fotoUrl = $fotoNome ? route('imagem.foto', ['filename' => $fotoNome]) : null;
                    @endphp
                    
                    @if($fotoUrl)
                        <img src="{{ $fotoUrl }}" 
                             alt="Foto de {{ $membro->nome }}"
                             id="fotoPerfil"
                             onerror="this.style.display='none'; this.parentElement.textContent='{{ substr($membro->nome, 0, 1) }}';">
                    @else
                        {{ substr($membro->nome, 0, 1) }}
                    @endif
                    <span class="status-dot"></span>
                </div>

                <div class="perfil-info">
                    <div class="perfil-nome">
                        {{ $membro->nome ?? 'N/A' }}
                        <span class="badge-funcao">{{ $membro->funcao ?? 'Membro' }}</span>
                    </div>
                    <div class="perfil-matricula">
                        <i class="fas fa-id-card" style="font-size: 0.7rem; color: var(--text-secondary);"></i>
                        Matrícula #{{ $membro->matricula }}
                        <span class="status-badge-modern status-{{ strtolower($membro->status ?? 'ativo') }}" style="margin-left: 10px;">
                            <span class="dot"></span> {{ $membro->status ?? 'ATIVO' }}
                        </span>
                    </div>
                    <div class="perfil-local">
                        <i class="fas fa-map-marker-alt"></i>
                        {{ $membro->cidade ?? 'N/A' }}/{{ $membro->uf ?? 'N/A' }}
                        @if($membro->congregacao)
                            <span style="margin-left: 8px; color: var(--text-secondary);">
                                <i class="fas fa-church" style="font-size: 0.6rem;"></i> {{ $membro->congregacao }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- INFO GRID -->
            <div class="info-grid">
                <div>
                    <div class="info-item">
                        <span class="label"><i class="fas fa-envelope"></i> Email</span>
                        <span class="value">{{ $membro->email ?? 'Não informado' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label"><i class="fas fa-phone"></i> Telefone</span>
                        <span class="value">{{ $membro->telefone ?? 'Não informado' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label"><i class="fas fa-id-card"></i> Documento</span>
                        <span class="value">{{ $membro->documento ?? 'Não informado' }}</span>
                    </div>
                    <div class="info-item">
                        <span class="label"><i class="fas fa-calendar-alt"></i> Nascimento</span>
                        <span class="value">{{ isset($membro->dataNascimento) ? date('d/m/Y', strtotime($membro->dataNascimento)) : 'Não informado' }}</span>
                    </div>
                </div>

                <div>
                    <div class="info-item">
                        <span class="label"><i class="fas fa-water"></i> Batismo</span>
                        <span class="value">{{ isset($membro->dataBatismo) ? date('d/m/Y', strtotime($membro->dataBatismo)) : 'Não informado' }}</span>
                    </div>
                    @if($membro->data_Consagracao)
                    <div class="info-item">
                        <span class="label"><i class="fas fa-hands-praying"></i> Consagração</span>
                        <span class="value">{{ date('d/m/Y', strtotime($membro->data_Consagracao)) }}</span>
                    </div>
                    @endif
                    @if($membro->mae)
                    <div class="info-item">
                        <span class="label"><i class="fas fa-female"></i> Mãe</span>
                        <span class="value">{{ $membro->mae }}</span>
                    </div>
                    @endif
                    @if($membro->pai)
                    <div class="info-item">
                        <span class="label"><i class="fas fa-male"></i> Pai</span>
                        <span class="value">{{ $membro->pai }}</span>
                    </div>
                    @endif
                    <div class="info-item">
                        <span class="label"><i class="fas fa-calendar-plus"></i> Cadastro</span>
                        <span class="value">{{ isset($membro->datCadastro) ? date('d/m/Y', strtotime($membro->datCadastro)) : 'Não informado' }}</span>
                    </div>
                </div>
            </div>

            <!-- AÇÕES -->
            <div class="perfil-actions">
                <a href="{{ route('membros.index') }}" class="action-btn" title="Voltar">
                    <i class="fas fa-arrow-left"></i>
                    <span class="btn-text">Voltar</span>
                </a>

                <a href="{{ route('cartao.show', $membro->matricula) }}" class="action-btn info" target="_blank" title="Ver Cartão">
                    <i class="fas fa-id-card"></i>
                    <span class="btn-text">Cartão</span>
                </a>

                @if(Auth::check() && Auth::user()->matricula != $membro->matricula)
                    <button class="action-btn" onclick="curtir(this)" title="Curtir">
                        <i class="fas fa-heart" id="curtirIcon"></i>
                        <span class="btn-text" id="curtirText">Curtir</span>
                    </button>
                @endif

                @if(Auth::check() && Auth::user()->matricula != $membro->matricula)
                    <button class="action-btn success" onclick="seguir(this)" title="Seguir">
                        <i class="fas fa-user-plus" id="seguirIcon"></i>
                        <span class="btn-text" id="seguirText">Seguir</span>
                    </button>
                @endif

                <button class="action-btn primary" onclick="compartilhar()" title="Compartilhar">
                    <i class="fas fa-share-alt"></i>
                    <span class="btn-text">Compartilhar</span>
                </button>

                <button class="action-btn whatsapp" onclick="whatsapp()" title="WhatsApp">
                    <i class="fab fa-whatsapp"></i>
                    <span class="btn-text">WhatsApp</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- TOAST -->
<div class="toast-custom" id="toastCustom">
    <i class="fas fa-check-circle"></i>
    <span id="toastMessage">Mensagem</span>
</div>

<!-- MODAL DE EXCLUSÃO -->
<div class="modal fade" id="modalExcluir" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Tem certeza que deseja excluir este membro?</strong></p>
                <p>Esta ação não poderá ser desfeita.</p>
                <div id="dadosMembroExcluir"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="formExcluir" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Confirmar Exclusão
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// ============================================================
// FUNÇÃO DE EXCLUSÃO
// ============================================================
function excluirMembro(matricula, nome) {
    const urlExcluir = '{{ route("admin.membros.destroy", ":matricula") }}'.replace(':matricula', matricula);
    
    const modal = new bootstrap.Modal(document.getElementById('modalExcluir'));
    
    document.getElementById('dadosMembroExcluir').innerHTML = `
        <div class="alert alert-warning">
            <strong>Membro:</strong> ${nome}<br>
            <strong>Matrícula:</strong> #${matricula}
        </div>
    `;
    
    document.getElementById('formExcluir').action = urlExcluir;
    modal.show();
}

document.getElementById('formExcluir')?.addEventListener('submit', function(e) {
    if (!confirm('Tem certeza absoluta que deseja excluir este membro?')) {
        e.preventDefault();
        return false;
    }
});

// ============================================================
// FUNÇÃO DE TOAST
// ============================================================
function showToast(message, type = 'info') {
    const toast = document.getElementById('toastCustom');
    const messageEl = document.getElementById('toastMessage');
    const icon = toast.querySelector('i');
    
    messageEl.textContent = message;
    toast.className = 'toast-custom';
    toast.classList.add(type);
    
    if (type === 'success') {
        icon.className = 'fas fa-check-circle';
    } else if (type === 'error') {
        icon.className = 'fas fa-times-circle';
    } else {
        icon.className = 'fas fa-info-circle';
    }
    
    toast.style.display = 'block';
    clearTimeout(toast._timeout);
    toast._timeout = setTimeout(() => {
        toast.style.display = 'none';
    }, 3000);
}

// ============================================================
// FUNÇÃO CURTIR
// ============================================================
function curtir(elemento) {
    const isCurtido = elemento.classList.contains('curtido');
    const icon = elemento.querySelector('i');
    const text = elemento.querySelector('.btn-text');
    
    if (isCurtido) {
        elemento.classList.remove('curtido');
        icon.className = 'fas fa-heart';
        text.textContent = 'Curtir';
        elemento.style.background = '';
        elemento.style.color = '';
        showToast('💔 Você descurtiu este membro!', 'info');
    } else {
        elemento.classList.add('curtido');
        icon.className = 'fas fa-heart';
        text.textContent = 'Curtido';
        elemento.style.background = '#4CAF50';
        elemento.style.color = 'white';
        showToast('❤️ Você curtiu este membro!', 'success');
    }
}

// ============================================================
// FUNÇÃO SEGUIR
// ============================================================
function seguir(elemento) {
    const isSeguindo = elemento.classList.contains('seguindo');
    const icon = elemento.querySelector('i');
    const text = elemento.querySelector('.btn-text');
    
    if (isSeguindo) {
        elemento.classList.remove('seguindo');
        icon.className = 'fas fa-user-plus';
        text.textContent = 'Seguir';
        elemento.style.background = '';
        elemento.style.color = '';
        showToast('👋 Você deixou de seguir este membro!', 'info');
    } else {
        elemento.classList.add('seguindo');
        icon.className = 'fas fa-user-check';
        text.textContent = 'Seguindo';
        elemento.style.background = '#6c757d';
        elemento.style.color = 'white';
        showToast('👥 Você está seguindo este membro!', 'success');
    }
}

// ============================================================
// FUNÇÃO COMPARTILHAR
// ============================================================
function compartilhar() {
    const url = window.location.href;
    const nome = '{{ $membro->nome }}';
    
    if (navigator.share) {
        navigator.share({
            title: 'Perfil de ' + nome,
            text: 'Conheça o perfil de ' + nome + ' na ADTC2!',
            url: url
        }).catch(() => {});
    } else {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(url).then(() => {
                showToast('📋 Link copiado!', 'success');
            }).catch(() => {
                fallbackCopy(url);
            });
        } else {
            fallbackCopy(url);
        }
    }
}

function fallbackCopy(text) {
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();
    document.execCommand('copy');
    textarea.remove();
    showToast('📋 Link copiado!', 'success');
}

// ============================================================
// FUNÇÃO WHATSAPP
// ============================================================
function whatsapp() {
    const nome = '{{ $membro->nome }}';
    const matricula = '{{ $membro->matricula }}';
    const telefone = '{{ $membro->telefone ?? "" }}';
    const mensagem = `Olá! 👋\n\nVi seu perfil na Rede Cristã ADTC2.\n\n👤 Nome: ${nome}\n🆔 Matrícula: #${matricula}\n\nVamos nos conectar! 🙏`;
    
    const url = `https://wa.me/55${telefone.replace(/\D/g, '')}?text=${encodeURIComponent(mensagem)}`;
    window.open(url, '_blank');
}

// ============================================================
// CONSOLE LOG
// ============================================================
</script>
@endpush
@endsection