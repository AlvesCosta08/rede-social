@extends('layouts.app')

@section('title', 'Novo Membro - Admin')
@section('page-title', 'Cadastrar Novo Membro')
@section('page-subtitle', 'Preencha todos os dados para cadastrar um novo membro')

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

    /* ⭐ BADGE DE NÍVEL */
    .nivel-badge {
        display: inline-block;
        padding: 2px 10px;
        border-radius: 12px;
        font-size: 0.6rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
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
    }
</style>
@endsection

@section('content')
<div class="member-form-container">
    <div class="form-card">
        {{-- ⭐ VERIFICA PERMISSÃO --}}
        @if(!auth()->user()?->pode('criar_membro'))
            <div class="alert-danger">
                <strong>⛔ Acesso Negado</strong>
                <p>Você não tem permissão para criar novos membros.</p>
            </div>
        @endif

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

        <form action="{{ route('admin.membros.store') }}" method="POST">
            @csrf

            <!-- Dados Pessoais -->
            <div class="form-section">
                <h3>
                    <span class="section-icon">👤</span>
                    Dados Pessoais
                </h3>

                <div class="form-group">
                    <label for="nome">Nome Completo <span class="required">*</span></label>
                    <input type="text" id="nome" name="nome" value="{{ old('nome') }}" required placeholder="Digite o nome completo">
                    @error('nome')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="nome_carteira">Nome para Carteira <span class="optional">(opcional)</span></label>
                    <input type="text" id="nome_carteira" name="nome_carteira" value="{{ old('nome_carteira') }}" placeholder="Nome como deve aparecer na carteira">
                    @error('nome_carteira')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">E-mail <span class="required">*</span></label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="email@exemplo.com">
                        @error('email')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="telefone">Telefone <span class="required">*</span></label>
                        <input type="text" id="telefone" name="telefone" value="{{ old('telefone') }}" required placeholder="(00) 00000-0000">
                        @error('telefone')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="documento">Documento (CPF) <span class="required">*</span></label>
                        <input type="text" id="documento" name="documento" value="{{ old('documento') }}" required placeholder="000.000.000-00">
                        @error('documento')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="dataNascimento">Data de Nascimento <span class="required">*</span></label>
                        <input type="date" id="dataNascimento" name="dataNascimento" value="{{ old('dataNascimento') }}" required>
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
                                <option value="{{ $estado }}" {{ old('estadoCivil') == $estado ? 'selected' : '' }}>{{ $estado }}</option>
                            @endforeach
                        </select>
                        @error('estadoCivil')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="dataBatismo">Data do Batismo <span class="optional">(opcional)</span></label>
                        <input type="date" id="dataBatismo" name="dataBatismo" value="{{ old('dataBatismo') }}">
                        @error('dataBatismo')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="mae">Nome da Mãe <span class="optional">(opcional)</span></label>
                        <input type="text" id="mae" name="mae" value="{{ old('mae') }}" placeholder="Nome completo da mãe">
                        @error('mae')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="pai">Nome do Pai <span class="optional">(opcional)</span></label>
                        <input type="text" id="pai" name="pai" value="{{ old('pai') }}" placeholder="Nome completo do pai">
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
                    <input type="text" id="endereco" name="endereco" value="{{ old('endereco') }}" required placeholder="Rua, Avenida, etc">
                    @error('endereco')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="numero">Número <span class="required">*</span></label>
                        <input type="number" id="numero" name="numero" value="{{ old('numero') }}" required placeholder="Número">
                        @error('numero')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="bairro">Bairro <span class="required">*</span></label>
                        <input type="text" id="bairro" name="bairro" value="{{ old('bairro') }}" required placeholder="Bairro">
                        @error('bairro')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="cep">CEP <span class="required">*</span></label>
                        <input type="text" id="cep" name="cep" value="{{ old('cep') }}" required placeholder="00000-000">
                        @error('cep')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="cidade">Cidade <span class="required">*</span></label>
                        <input type="text" id="cidade" name="cidade" value="{{ old('cidade') }}" required placeholder="Cidade">
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
                            <option value="{{ $uf }}" {{ old('uf') == $uf ? 'selected' : '' }}>{{ $uf }}</option>
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
                        <select id="congregacao" name="congregacao" required>
                            <option value="">Selecione</option>
                            @foreach($congregacoes as $congregacao)
                                <option value="{{ $congregacao }}" {{ old('congregacao') == $congregacao ? 'selected' : '' }}>{{ $congregacao }}</option>
                            @endforeach
                        </select>
                        @error('congregacao')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="funcao">Função <span class="required">*</span></label>
                        <select id="funcao" name="funcao" required>
                            <option value="">Selecione</option>
                            @foreach($funcoes as $funcao)
                                <option value="{{ $funcao }}" {{ old('funcao') == $funcao ? 'selected' : '' }}>{{ $funcao }}</option>
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
                        <input type="date" id="data_Consagracao" name="data_Consagracao" value="{{ old('data_Consagracao') }}">
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
                                <option value="{{ $status }}" {{ old('status') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                            @endforeach
                        </select>
                        @error('status')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- ⭐ CAMPO NÍVEL - APENAS ADMIN PODE DEFINIR --}}
                @if(auth()->user()?->isAdmin())
                    <div class="form-row">
                        <div class="form-group">
                            <label for="nivel">Nível de Permissão</label>
                            <select id="nivel" name="nivel">
                                <option value="usuario" {{ old('nivel') == 'usuario' ? 'selected' : '' }}>👤 Usuário</option>
                                <option value="secretario" {{ old('nivel') == 'secretario' ? 'selected' : '' }}>📋 Secretário</option>
                                <option value="admin" {{ old('nivel') == 'admin' ? 'selected' : '' }}>👑 Administrador</option>
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
                    Segurança e Biografia
                </h3>

                <div class="form-group">
                    <label for="bio">Biografia/Notas <span class="optional">(opcional)</span></label>
                    <textarea id="bio" name="bio" rows="3" placeholder="Informações adicionais sobre o membro...">{{ old('bio') }}</textarea>
                    <div class="help-text">Máximo de 500 caracteres</div>
                    @error('bio')
                        <div class="error-text">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="senha">Senha <span class="required">*</span></label>
                        <input type="password" id="senha" name="senha" required placeholder="Mínimo 6 caracteres">
                        @error('senha')
                            <div class="error-text">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="senha_confirmation">Confirmar Senha <span class="required">*</span></label>
                        <input type="password" id="senha_confirmation" name="senha_confirmation" required placeholder="Digite a senha novamente">
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="form-actions">
                @if(auth()->user()?->pode('criar_membro'))
                    <button type="submit" class="btn-admin btn-admin-primary">
                        <span>💾</span> Cadastrar Membro
                    </button>
                @endif
                <a href="{{ route('admin.membros.index') }}" class="btn-admin btn-admin-secondary">
                    <span>🔙</span> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    console.log('📝 Formulário de cadastro de membros carregado');
    
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
    @endif
</script>
@endpush
@endsection