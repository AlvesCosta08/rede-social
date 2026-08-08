@extends('layouts.app')

@section('title', 'Editar Membro - Admin')
@section('page-title', 'Editar Membro')
@section('page-subtitle', 'Atualize as informações do membro')

@section('styles')
<style>
    .member-form-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .form-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        padding: 30px;
    }

    .form-section {
        margin-bottom: 30px;
        padding-bottom: 30px;
        border-bottom: 2px solid #f0ece6;
    }

    .form-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .form-section h3 {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-section h3 .section-icon {
        font-size: 1.3rem;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        font-size: 0.85rem;
        color: #1a1a2e;
        margin-bottom: 5px;
    }

    .form-group label .required {
        color: #f44336;
        margin-left: 4px;
    }

    .form-group label .optional {
        color: #999;
        font-weight: 400;
        font-size: 0.75rem;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 10px 14px;
        border: 2px solid #e8e4db;
        border-radius: 10px;
        font-size: 0.9rem;
        background: #faf8f5;
        transition: all 0.3s ease;
        color: #1a1a2e;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: #c9a84c;
        background: white;
        box-shadow: 0 0 0 3px rgba(201, 168, 76, 0.1);
    }

    .form-group input::placeholder,
    .form-group textarea::placeholder {
        color: #bbb;
    }

    .form-group .error-text {
        color: #f44336;
        font-size: 0.8rem;
        margin-top: 5px;
    }

    .form-group .help-text {
        color: #999;
        font-size: 0.75rem;
        margin-top: 3px;
    }

    .matricula-display {
        background: #faf8f5;
        padding: 10px 14px;
        border-radius: 10px;
        border: 2px solid #e8e4db;
        font-weight: 600;
        color: #1a1a2e;
        font-size: 1.1rem;
    }

    .form-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        padding-top: 20px;
        border-top: 2px solid #e8e4db;
        margin-top: 20px;
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

    .alert-danger {
        background: #ffebee;
        color: #c62828;
        padding: 15px;
        border-radius: 12px;
        margin-bottom: 20px;
        border-left: 4px solid #c62828;
    }

    .alert-danger ul {
        margin: 10px 0 0;
        padding-left: 20px;
    }

    .alert-danger ul li {
        margin: 5px 0;
    }

    .alert-success {
        background: #e8f5e9;
        color: #2e7d32;
        padding: 15px;
        border-radius: 12px;
        margin-bottom: 20px;
        border-left: 4px solid #4caf50;
    }

    /* ⭐ BADGE DE NÍVEL */
    .nivel-badge {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 0.6rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        margin-left: 8px;
    }

    .nivel-admin {
        background: #f44336;
        color: white;
    }

    .nivel-secretario {
        background: #ff9800;
        color: white;
    }

    .nivel-usuario {
        background: #9e9e9e;
        color: white;
    }

    .campo-bloqueado {
        opacity: 0.6;
        cursor: not-allowed;
        pointer-events: none;
    }

    @media (max-width: 768px) {
        .form-card {
            padding: 20px;
        }

        .form-row {
            grid-template-columns: 1fr;
            gap: 0;
        }

        .form-actions {
            flex-direction: column;
        }

        .form-actions .btn-admin {
            width: 100%;
            justify-content: center;
        }

        .form-actions form {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
<div class="member-form-container">
    <div class="form-card">
        {{-- ⭐ VERIFICA PERMISSÃO --}}
        @if(!auth()->user()?->pode('editar_membro', $membro))
            <div class="alert-danger">
                <strong>⛔ Acesso Negado</strong>
                <p>Você não tem permissão para editar este membro.</p>
                <p style="margin-top: 10px;">
                    <a href="{{ route('admin.membros.show', $membro->matricula) }}" class="btn-admin btn-admin-secondary" style="display: inline-block;">
                        <span>🔙</span> Voltar
                    </a>
                </p>
            </div>
        @else
            @if($errors->any())
                <div class="alert-danger">
                    <strong>⚠️ Por favor, corrija os seguintes erros:</strong>
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="alert-success">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('admin.membros.update', $membro->matricula) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Matrícula -->
                <div class="form-section">
                    <h3>
                        <span class="section-icon">🔢</span>
                        Identificação
                        {{-- ⭐ MOSTRA O NÍVEL ATUAL --}}
                        @if(isset($membro->nivel))
                            <span class="nivel-badge nivel-{{ $membro->nivel }}">
                                {{ $membro->nivel === 'admin' ? '👑 Admin' : ($membro->nivel === 'secretario' ? '📋 Secretário' : '👤 Membro') }}
                            </span>
                        @endif
                    </h3>
                    <div class="form-group">
                        <label>Matrícula</label>
                        <div class="matricula-display">#{{ $membro->matricula }}</div>
                    </div>
                </div>

                <!-- Dados Pessoais -->
                <div class="form-section">
                    <h3>
                        <span class="section-icon">👤</span>
                        Dados Pessoais
                    </h3>

                    <div class="form-group">
                        <label for="nome">Nome Completo <span class="required">*</span></label>
                        <input type="text" id="nome" name="nome" value="{{ old('nome', $membro->nome) }}" required>
                        @error('nome')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="nome_carteira">Nome para Carteira <span class="optional">(opcional)</span></label>
                        <input type="text" id="nome_carteira" name="nome_carteira" value="{{ old('nome_carteira', $membro->nome_carteira) }}">
                        @error('nome_carteira')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="email">E-mail <span class="optional">(opcional)</span></label>
                            <input type="email" id="email" name="email" value="{{ old('email', $membro->email) }}" placeholder="email@exemplo.com">
                            <div class="help-text">Deixe em branco para manter o e-mail atual</div>
                            @error('email')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="telefone">Telefone <span class="required">*</span></label>
                            <input type="text" id="telefone" name="telefone" value="{{ old('telefone', $membro->telefone) }}" required>
                            @error('telefone')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="documento">Documento (CPF) <span class="optional">(opcional)</span></label>
                            <input type="text" id="documento" name="documento" value="{{ old('documento', $membro->documento) }}" placeholder="000.000.000-00">
                            <div class="help-text">Deixe em branco para manter o documento atual</div>
                            @error('documento')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="dataNascimento">Data de Nascimento <span class="required">*</span></label>
                            <input type="date" id="dataNascimento" name="dataNascimento" value="{{ old('dataNascimento', $membro->dataNascimento) }}" required>
                            @error('dataNascimento')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="estadoCivil">Estado Civil</label>
                            <select id="estadoCivil" name="estadoCivil">
                                <option value="">Selecione</option>
                                @foreach($estadoCivilList as $estado)
                                    <option value="{{ $estado }}" {{ old('estadoCivil', $membro->estadoCivil) == $estado ? 'selected' : '' }}>{{ $estado }}</option>
                                @endforeach
                            </select>
                            @error('estadoCivil')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="dataBatismo">Data do Batismo <span class="optional">(opcional)</span></label>
                            <input type="date" id="dataBatismo" name="dataBatismo" value="{{ old('dataBatismo', $membro->dataBatismo) }}">
                            @error('dataBatismo')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="mae">Nome da Mãe <span class="optional">(opcional)</span></label>
                            <input type="text" id="mae" name="mae" value="{{ old('mae', $membro->mae) }}" placeholder="Nome completo da mãe">
                            @error('mae')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="pai">Nome do Pai <span class="optional">(opcional)</span></label>
                            <input type="text" id="pai" name="pai" value="{{ old('pai', $membro->pai) }}" placeholder="Nome completo do pai">
                            @error('pai')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Endereço -->
                <div class="form-section">
                    <h3>
                        <span class="section-icon">📍</span>
                        Endereço
                    </h3>

                    <div class="form-group">
                        <label for="endereco">Endereço <span class="required">*</span></label>
                        <input type="text" id="endereco" name="endereco" value="{{ old('endereco', $membro->endereco) }}" required>
                        @error('endereco')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="numero">Número <span class="required">*</span></label>
                            <input type="number" id="numero" name="numero" value="{{ old('numero', $membro->numero) }}" required>
                            @error('numero')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="bairro">Bairro <span class="required">*</span></label>
                            <input type="text" id="bairro" name="bairro" value="{{ old('bairro', $membro->bairro) }}" required>
                            @error('bairro')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="cep">CEP <span class="required">*</span></label>
                            <input type="text" id="cep" name="cep" value="{{ old('cep', $membro->cep) }}" required>
                            @error('cep')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="cidade">Cidade <span class="required">*</span></label>
                            <input type="text" id="cidade" name="cidade" value="{{ old('cidade', $membro->cidade) }}" required>
                            @error('cidade')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="uf">UF <span class="required">*</span></label>
                        <select id="uf" name="uf" required>
                            <option value="">Selecione</option>
                            @foreach($ufList as $uf)
                                <option value="{{ $uf }}" {{ old('uf', $membro->uf) == $uf ? 'selected' : '' }}>{{ $uf }}</option>
                            @endforeach
                        </select>
                        @error('uf')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Dados da Igreja -->
                <div class="form-section">
                    <h3>
                        <span class="section-icon">⛪</span>
                        Dados da Igreja
                    </h3>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="congregacao">Congregação <span class="required">*</span></label>
                            {{-- ⭐ SECRETÁRIO NÃO PODE ALTERAR CONGREGAÇÃO --}}
                            @if(auth()->user()?->isAdmin())
                                <select id="congregacao" name="congregacao" required>
                                    <option value="">Selecione</option>
                                    @foreach($congregacoes as $congregacao)
                                        <option value="{{ $congregacao }}" {{ old('congregacao', $membro->congregacao) == $congregacao ? 'selected' : '' }}>{{ $congregacao }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="text" class="form-control campo-bloqueado" value="{{ $membro->congregacao }}" disabled>
                                <input type="hidden" name="congregacao" value="{{ $membro->congregacao }}">
                                <div class="help-text">A congregação não pode ser alterada por secretários</div>
                            @endif
                            @error('congregacao')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="funcao">Função <span class="required">*</span></label>
                            <select id="funcao" name="funcao" required>
                                <option value="">Selecione</option>
                                @foreach($funcoes as $funcao)
                                    <option value="{{ $funcao }}" {{ old('funcao', $membro->funcao) == $funcao ? 'selected' : '' }}>{{ $funcao }}</option>
                                @endforeach
                            </select>
                            @error('funcao')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="data_Consagracao">Data da Consagração <span class="optional">(opcional)</span></label>
                            <input type="date" id="data_Consagracao" name="data_Consagracao" value="{{ old('data_Consagracao', $membro->data_Consagracao) }}">
                            <div class="help-text">Obrigatória para cargos ministeriais</div>
                            @error('data_Consagracao')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="status">Status <span class="required">*</span></label>
                            <select id="status" name="status" required>
                                <option value="">Selecione</option>
                                @foreach($statusList as $status)
                                    <option value="{{ $status }}" {{ old('status', $membro->status) == $status ? 'selected' : '' }}>{{ $status }}</option>
                                @endforeach
                            </select>
                            @error('status')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- ⭐ CAMPO NÍVEL - APENAS ADMIN PODE ALTERAR --}}
                    @if(auth()->user()?->isAdmin())
                        <div class="form-row">
                            <div class="form-group">
                                <label for="nivel">Nível de Permissão</label>
                                <select id="nivel" name="nivel">
                                    <option value="usuario" {{ old('nivel', $membro->nivel ?? 'usuario') == 'usuario' ? 'selected' : '' }}>👤 Usuário</option>
                                    <option value="secretario" {{ old('nivel', $membro->nivel ?? '') == 'secretario' ? 'selected' : '' }}>📋 Secretário</option>
                                    <option value="admin" {{ old('nivel', $membro->nivel ?? '') == 'admin' ? 'selected' : '' }}>👑 Administrador</option>
                                </select>
                                <div class="help-text">
                                    <strong>Usuário:</strong> Apenas perfil próprio<br>
                                    <strong>Secretário:</strong> Gerencia membros da congregação<br>
                                    <strong>Administrador:</strong> Acesso total ao sistema
                                </div>
                                @error('nivel')
                                    <div class="error-text">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group"></div>
                        </div>
                    @endif
                </div>

                <!-- Biografia e Senha -->
                <div class="form-section">
                    <h3>
                        <span class="section-icon">🔐</span>
                        Biografia e Segurança
                    </h3>

                    <div class="form-group">
                        <label for="bio">Biografia/Notas <span class="optional">(opcional)</span></label>
                        <textarea id="bio" name="bio" rows="3" placeholder="Informações adicionais sobre o membro...">{{ old('bio', $membro->bio) }}</textarea>
                        <div class="help-text">Máximo de 500 caracteres</div>
                        @error('bio')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="nova_senha">Nova Senha <span class="optional">(opcional)</span></label>
                            <input type="password" id="nova_senha" name="nova_senha" placeholder="Deixe em branco para manter a atual">
                            <div class="help-text">Mínimo 6 caracteres</div>
                            @error('nova_senha')
                                <div class="error-text">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="nova_senha_confirmation">Confirmar Nova Senha</label>
                            <input type="password" id="nova_senha_confirmation" name="nova_senha_confirmation" placeholder="Digite a nova senha novamente">
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="form-actions">
                    <button type="submit" class="btn-admin btn-admin-primary">
                        <span>💾</span> Atualizar Membro
                    </button>
                    <a href="{{ route('admin.membros.show', $membro->matricula) }}" class="btn-admin btn-admin-secondary">
                        <span>🔙</span> Voltar
                    </a>

                    {{-- ⭐ BOTÃO REMOVER - APENAS ADMIN --}}
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
            </form>
        @endif
    </div>
</div>

@push('scripts')
<script>
    console.log('✏️ Editando membro: {{ $membro->nome }} (Matrícula #{{ $membro->matricula }})');
    console.log('🏷️ Nível atual: {{ $membro->nivel ?? "usuario" }}');

    // Máscara para telefone
    document.getElementById('telefone')?.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 10) {
            value = value.replace(/^(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
        } else if (value.length > 6) {
            value = value.replace(/^(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
        } else if (value.length > 2) {
            value = value.replace(/^(\d{2})(\d{0,5})/, '($1) $2');
        }
        e.target.value = value;
    });

    // Máscara para CPF
    document.getElementById('documento')?.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 9) {
            value = value.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
        } else if (value.length > 6) {
            value = value.replace(/^(\d{3})(\d{3})(\d{0,3})/, '$1.$2.$3');
        } else if (value.length > 3) {
            value = value.replace(/^(\d{3})(\d{0,3})/, '$1.$2');
        }
        e.target.value = value;
    });

    // Máscara para CEP
    document.getElementById('cep')?.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 5) {
            value = value.replace(/^(\d{5})(\d{3})/, '$1-$2');
        }
        e.target.value = value;
    });

    // ⭐ VALIDAR CONSAGRAÇÃO (se função exigir)
    document.querySelector('select[name="funcao"]')?.addEventListener('change', function() {
        const funcoesConsagracao = ['Pastor-Presidente', 'Pastor-Vice-Presidente', 'Pastor', 'Evangelista', 'Presbitero', 'Diacono', 'Auxiliar'];
        const dataConsagracao = document.querySelector('input[name="data_Consagracao"]');
        
        if (funcoesConsagracao.includes(this.value)) {
            dataConsagracao.required = true;
            dataConsagracao.closest('.form-group').querySelector('.help-text').textContent = '⚠️ Obrigatória para o cargo selecionado';
            dataConsagracao.closest('.form-group').querySelector('.help-text').style.color = '#f44336';
        } else {
            dataConsagracao.required = false;
            dataConsagracao.closest('.form-group').querySelector('.help-text').textContent = 'Obrigatória para cargos ministeriais';
            dataConsagracao.closest('.form-group').querySelector('.help-text').style.color = '#999';
        }
    });

    @if(auth()->user()?->isAdmin())
        console.log('👑 Usuário ADMIN - campo Nível disponível');
    @elseif(auth()->user()?->isSecretario())
        console.log('📋 Usuário SECRETÁRIO - campo Congregação bloqueado');
    @endif
</script>
@endpush
@endsection