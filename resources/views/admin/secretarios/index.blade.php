@extends('layouts.admin')

@section('title', 'Gerenciar Secretários - Conexão Igreja')
@section('page-title', '👔 Gerenciar Secretários')
@section('page-subtitle', 'Promova ou remova secretários da sua congregação')

@section('content')
<div class="grid-2" style="grid-template-columns: 1fr;">
    <!-- Card: Secretários Atuais -->
    <div class="card-modern">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h5 style="font-weight: 600; color: var(--text); margin: 0;">
                <i class="fas fa-user-tie" style="color: var(--primary);"></i> 
                Secretários Atuais
                <span style="font-size: 0.75rem; font-weight: 400; color: var(--text-secondary); margin-left: 8px;">
                    ({{ $secretarios->total() }})
                </span>
            </h5>
        </div>
        
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border);">
                        <th style="padding: 12px 8px; text-align: left; font-weight: 600; color: var(--text-secondary); font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Matrícula</th>
                        <th style="padding: 12px 8px; text-align: left; font-weight: 600; color: var(--text-secondary); font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Nome</th>
                        <th style="padding: 12px 8px; text-align: left; font-weight: 600; color: var(--text-secondary); font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Congregação</th>
                        <th style="padding: 12px 8px; text-align: left; font-weight: 600; color: var(--text-secondary); font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Status</th>
                        <th style="padding: 12px 8px; text-align: center; font-weight: 600; color: var(--text-secondary); font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($secretarios as $secretario)
                        <tr style="border-bottom: 1px solid var(--border); transition: var(--transition);">
                            <td style="padding: 12px 8px; font-weight: 600;">{{ $secretario->matricula }}</td>
                            <td style="padding: 12px 8px;">
                                <div style="display: flex; align-items: center; gap: 10px;">
                                    <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--primary-light)); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 0.8rem; flex-shrink: 0;">
                                        {{ substr($secretario->nome, 0, 1) }}
                                    </div>
                                    {{ $secretario->nome }}
                                </div>
                            </td>
                            <td style="padding: 12px 8px;">
                                <span style="background: rgba(108, 60, 225, 0.08); padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; color: var(--primary);">
                                    {{ $secretario->congregacao ?? 'Geral' }}
                                </span>
                            </td>
                            <td style="padding: 12px 8px;">
                                <span style="background: {{ $secretario->status == 'ativo' ? 'rgba(46, 204, 113, 0.15)' : 'rgba(243, 156, 18, 0.15)' }}; color: {{ $secretario->status == 'ativo' ? 'var(--success)' : 'var(--warning)' }}; padding: 4px 12px; border-radius: 20px; font-size: 0.7rem; font-weight: 600;">
                                    <i class="fas fa-{{ $secretario->status == 'ativo' ? 'check-circle' : 'clock' }}"></i>
                                    {{ ucfirst($secretario->status) }}
                                </span>
                            </td>
                            <td style="padding: 12px 8px; text-align: center;">
                                <form action="{{ route('admin.secretarios.destroy', $secretario->matricula) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('⚠️ Deseja remover este secretário?')"
                                      style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-modern btn-modern-danger" style="padding: 6px 14px; font-size: 0.75rem;">
                                        <i class="fas fa-user-minus"></i> Remover
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 40px 20px; color: var(--text-secondary);">
                                <i class="fas fa-user-tie" style="font-size: 2.5rem; display: block; margin-bottom: 12px; opacity: 0.3;"></i>
                                <p style="font-size: 1rem;">Nenhum secretário cadastrado</p>
                                <p style="font-size: 0.85rem; opacity: 0.7;">Promova um membro abaixo</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div style="margin-top: 20px;">
            {{ $secretarios->links('pagination::bootstrap-5') }}
        </div>
    </div>

    <!-- Card: Promover Membro -->
    <div class="card-modern" style="margin-top: 24px;">
        <h5 style="font-weight: 600; color: var(--text); margin-bottom: 20px;">
            <i class="fas fa-user-plus" style="color: var(--primary);"></i> 
            Promover Membro a Secretário
        </h5>

        <form action="" method="POST" id="formPromoverSecretario">
            @csrf
            @method('PUT')
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                <!-- Selecionar Membro -->
                <div>
                    <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 6px; color: var(--text-secondary);">
                        <i class="fas fa-user"></i> Membro
                    </label>
                    <select name="matricula" id="membro" class="form-control" required style="width: 100%; padding: 10px 14px; border-radius: var(--radius-sm); border: 2px solid var(--border); background: var(--bg-card); color: var(--text); font-size: 0.9rem; transition: var(--transition);">
                        <option value="">Selecione um membro...</option>
                        @foreach($membros as $membro)
                            <option value="{{ $membro->matricula }}">
                                #{{ $membro->matricula }} - {{ $membro->nome }} 
                                ({{ $membro->congregacao ?? 'Sede' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Congregação -->
                <div>
                    <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 6px; color: var(--text-secondary);">
                        <i class="fas fa-building"></i> Congregação
                    </label>
                    <input type="text" name="congregacao" id="congregacao" 
                           style="width: 100%; padding: 10px 14px; border-radius: var(--radius-sm); border: 2px solid var(--border); background: var(--bg-card); color: var(--text); font-size: 0.9rem; transition: var(--transition);" 
                           placeholder="Ex: Sede (opcional)">
                </div>

                <!-- Ação -->
                <div>
                    <label style="display: block; font-weight: 600; font-size: 0.85rem; margin-bottom: 6px; color: var(--text-secondary);">
                        <i class="fas fa-tag"></i> Ação
                    </label>
                    <select name="nivel" id="nivel" class="form-control" required style="width: 100%; padding: 10px 14px; border-radius: var(--radius-sm); border: 2px solid var(--border); background: var(--bg-card); color: var(--text); font-size: 0.9rem; transition: var(--transition);">
                        <option value="secretario">✅ Promover a Secretário</option>
                        <option value="usuario">⬇️ Rebaixar para Usuário</option>
                    </select>
                </div>

                <!-- Botão -->
                <div style="display: flex; align-items: flex-end;">
                    <button type="submit" class="btn-modern btn-modern-primary" style="width: 100%;">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectMembro = document.getElementById('membro');
        const form = document.getElementById('formPromoverSecretario');
        
        {{-- ⭐ CORRIGIDO: parâmetro 'matricula' --}}
        const baseUrl = "{{ route('admin.secretarios.update', ['matricula' => '__MATRICULA__']) }}";
        
        if (selectMembro) {
            selectMembro.addEventListener('change', function() {
                const matricula = this.value;
                if (matricula) {
                    form.action = baseUrl.replace('__MATRICULA__', matricula);
                } else {
                    form.action = '';
                }
            });
        }

        // Auto-fechar toasts
        document.querySelectorAll('.toast').forEach(toast => {
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-20px)';
                setTimeout(() => toast.remove(), 400);
            }, 5000);
        });
    });
</script>
@endpush