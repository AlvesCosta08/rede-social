@extends('layouts.app')

@section('title', 'Sair da Igreja - Conexão Igreja')
@section('page-title', '😢 Sair da Igreja')
@section('page-subtitle', 'Sentimos muito que você esteja saindo')

@section('styles')
<style>
    .sair-container {
        max-width: 520px;
        margin: 20px auto;
        padding: 0 15px;
    }

    .sair-card {
        background: var(--bg-card);
        border-radius: var(--radius);
        padding: 40px 35px;
        box-shadow: var(--shadow);
        border: 1px solid var(--border);
        text-align: center;
        animation: fadeIn 0.5s ease;
    }

    .sair-card .icon-sair {
        font-size: 4rem;
        margin-bottom: 15px;
        display: block;
    }

    .sair-card h2 {
        font-size: 1.5rem;
        font-weight: 800;
        color: #f44336;
        margin-bottom: 12px;
    }

    .sair-card .texto-principal {
        color: var(--text);
        font-size: 1rem;
        margin-bottom: 8px;
        font-weight: 500;
    }

    .sair-card .texto-aviso {
        color: var(--text-secondary);
        font-size: 0.85rem;
        margin-bottom: 25px;
        padding: 12px 16px;
        background: rgba(244, 67, 54, 0.08);
        border-radius: var(--radius-sm);
        border-left: 3px solid #f44336;
        text-align: left;
    }

    .sair-card .texto-aviso i {
        color: #f44336;
        margin-right: 8px;
    }

    .sair-card .campo-confirmacao {
        text-align: left;
        margin-bottom: 25px;
    }

    .sair-card .campo-confirmacao label {
        display: block;
        font-weight: 600;
        font-size: 0.85rem;
        color: var(--text);
        margin-bottom: 6px;
    }

    .sair-card .campo-confirmacao label .required {
        color: #f44336;
    }

    .sair-card .campo-confirmacao input {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid var(--border);
        border-radius: var(--radius-sm);
        background: var(--bg);
        color: var(--text);
        font-size: 0.95rem;
        transition: var(--transition);
    }

    .sair-card .campo-confirmacao input:focus {
        outline: none;
        border-color: #f44336;
        box-shadow: 0 0 0 4px rgba(244, 67, 54, 0.1);
    }

    .sair-card .campo-confirmacao input.error {
        border-color: #f44336;
    }

    .sair-card .campo-confirmacao .help-text {
        font-size: 0.75rem;
        color: var(--text-secondary);
        margin-top: 4px;
        display: block;
        opacity: 0.7;
    }

    .sair-card .campo-confirmacao .error-text {
        color: #f44336;
        font-size: 0.75rem;
        margin-top: 4px;
        display: block;
    }

    .sair-actions {
        display: flex;
        gap: 15px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .sair-actions .btn-cancelar {
        padding: 12px 30px;
        border: 2px solid var(--border);
        border-radius: var(--radius-sm);
        background: transparent;
        color: var(--text-secondary);
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        transition: var(--transition);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .sair-actions .btn-cancelar:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: var(--bg);
        transform: translateY(-2px);
    }

    .sair-actions .btn-confirmar {
        padding: 12px 30px;
        border: none;
        border-radius: var(--radius-sm);
        background: linear-gradient(135deg, #f44336, #c62828);
        color: white;
        font-weight: 700;
        font-size: 0.9rem;
        cursor: pointer;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 16px rgba(244, 67, 54, 0.3);
    }

    .sair-actions .btn-confirmar:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(244, 67, 54, 0.4);
    }

    .sair-actions .btn-confirmar:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none !important;
    }

    /* Toast */
    .toast-sair {
        position: fixed;
        bottom: 30px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0, 0, 0, 0.85);
        color: white;
        padding: 12px 25px;
        border-radius: 12px;
        font-weight: 500;
        font-size: 0.9rem;
        z-index: 9999;
        backdrop-filter: blur(10px);
        animation: fadeUp 0.4s ease;
        display: none;
        border: 1px solid rgba(244, 67, 54, 0.3);
    }

    .toast-sair i { margin-right: 8px; }
    .toast-sair .icon-error { color: #f44336; }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateX(-50%) translateY(20px); }
        to { opacity: 1; transform: translateX(-50%) translateY(0); }
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 480px) {
        .sair-card {
            padding: 25px 20px;
        }

        .sair-card .icon-sair {
            font-size: 3rem;
        }

        .sair-card h2 {
            font-size: 1.2rem;
        }

        .sair-actions {
            flex-direction: column;
        }

        .sair-actions .btn-cancelar,
        .sair-actions .btn-confirmar {
            width: 100%;
            justify-content: center;
        }

        .toast-sair {
            left: 20px;
            right: 20px;
            transform: translateX(0);
            text-align: center;
        }
    }
</style>
@endsection

@section('content')
<div class="sair-container">
    <div class="sair-card">
        <span class="icon-sair">😢</span>
        <h2>Sair da Igreja</h2>
        <p class="texto-principal">Sentimos muito que você esteja saindo.</p>

        <div class="texto-aviso">
            <i class="fas fa-exclamation-triangle"></i>
            <strong>Esta ação é irreversível!</strong>
            <br>
            Todos os seus dados serão anonimizados e suas publicações removidas.
        </div>

        <form method="POST" action="{{ route('sair.igreja.confirmar') }}" id="formSair">
            @csrf

            <div class="campo-confirmacao">
                <label>
                    Digite seu nome completo para confirmar:
                    <span class="required">*</span>
                </label>
                <input 
                    type="text" 
                    name="confirmacao" 
                    id="confirmacao"
                    placeholder="{{ $user->nome }}" 
                    required
                    autofocus
                    class="{{ $errors->has('confirmacao') ? 'error' : '' }}"
                >
                <span class="help-text">Digite exatamente: <strong>{{ $user->nome }}</strong></span>
                @error('confirmacao')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="sair-actions">
                <a href="{{ route('feed.index') }}" class="btn-cancelar">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
                <button type="submit" class="btn-confirmar" id="btnConfirmar">
                    <i class="fas fa-sign-out-alt"></i> Confirmar Saída
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Toast -->
<div class="toast-sair" id="toastSair">
    <i class="fas fa-exclamation-circle icon-error"></i>
    <span id="toastMessage">Confirme digitando seu nome corretamente.</span>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formSair');
    const input = document.getElementById('confirmacao');
    const btnConfirmar = document.getElementById('btnConfirmar');
    const nomeCompleto = '{{ $user->nome }}';

    // ===== VALIDAR NOME EM TEMPO REAL =====
    input.addEventListener('input', function() {
        const valor = this.value.trim();
        if (valor.length > 0 && valor !== nomeCompleto) {
            this.classList.add('error');
        } else {
            this.classList.remove('error');
        }
    });

    // ===== VALIDAR ANTES DE ENVIAR =====
    form.addEventListener('submit', function(e) {
        const valor = input.value.trim();
        
        if (valor !== nomeCompleto) {
            e.preventDefault();
            input.classList.add('error');
            
            const toast = document.getElementById('toastSair');
            const message = document.getElementById('toastMessage');
            message.textContent = 'Digite exatamente "' + nomeCompleto + '" para confirmar.';
            toast.style.display = 'block';
            
            clearTimeout(toast._timeout);
            toast._timeout = setTimeout(() => {
                toast.style.display = 'none';
            }, 4000);
            
            return false;
        }
        
        btnConfirmar.disabled = true;
        btnConfirmar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processando...';
    });

    console.log('😢 Página de saída da igreja carregada');
    console.log('👤 Usuário: {{ $user->nome }}');
    console.log('🏷️ Nível: {{ $user->nivel ?? "usuario" }}');
});
</script>
@endpush
@endsection