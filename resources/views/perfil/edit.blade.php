@extends('layouts.app')

@section('title', 'Editar Perfil - ' . $perfil->nome)
@section('page-title', '✏️ Editar Perfil')
@section('page-subtitle', 'Atualize todos os seus dados cadastrais')

@section('styles')
<style>
    .edit-profile-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 10px 15px;
    }

    .edit-profile-card {
        background: var(--bg-card);
        border-radius: 20px;
        padding: 30px 35px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        border: 1px solid var(--border);
    }

    .edit-profile-card .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid var(--border);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .edit-profile-card .section-title i {
        color: var(--primary);
    }

    .form-group {
        margin-bottom: 18px;
    }

    .form-group label {
        display: block;
        font-weight: 600;
        font-size: 0.85rem;
        color: var(--text);
        margin-bottom: 5px;
    }

    .form-group label .required {
        color: #e74c3c;
        margin-left: 2px;
    }

    .form-group .form-control {
        width: 100%;
        padding: 10px 14px;
        border: 2px solid var(--border);
        border-radius: 10px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background: var(--bg);
        color: var(--text);
    }

    .form-group .form-control:focus {
        outline: none;
        border-color: var(--primary);
        background: var(--bg-card);
        box-shadow: 0 0 0 3px rgba(108, 60, 225, 0.1);
    }

    .form-group .form-control.error {
        border-color: #e74c3c;
    }

    .form-group .error-text {
        color: #e74c3c;
        font-size: 0.75rem;
        margin-top: 4px;
        display: block;
    }

    .form-group .help-text {
        color: var(--text-secondary);
        font-size: 0.75rem;
        margin-top: 4px;
        display: block;
        opacity: 0.7;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
    }

    .form-row-3 {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 15px;
    }

    /* Avatar Upload */
    .avatar-upload-wrapper {
        display: flex;
        align-items: center;
        gap: 25px;
        padding: 15px 0;
        margin-bottom: 10px;
        flex-wrap: wrap;
    }

    .avatar-upload-wrapper .avatar-preview {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary), var(--primary-light));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        font-weight: 700;
        color: white;
        border: 4px solid var(--primary);
        overflow: hidden;
        flex-shrink: 0;
        position: relative;
        box-shadow: 0 4px 20px rgba(108, 60, 225, 0.3);
    }

    .avatar-upload-wrapper .avatar-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .avatar-upload-wrapper .avatar-preview .placeholder {
        font-size: 3rem;
        font-weight: 700;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
    }

    .avatar-upload-wrapper .avatar-actions {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .avatar-upload-wrapper .avatar-actions .btn-upload {
        padding: 8px 20px;
        border: none;
        border-radius: 10px;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        position: relative;
        overflow: hidden;
    }

    .avatar-upload-wrapper .avatar-actions .btn-upload:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(108, 60, 225, 0.4);
    }

    .avatar-upload-wrapper .avatar-actions .btn-upload input[type="file"] {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .avatar-upload-wrapper .avatar-actions .btn-remove {
        padding: 8px 20px;
        border: 2px solid #e74c3c;
        border-radius: 10px;
        background: transparent;
        color: #e74c3c;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .avatar-upload-wrapper .avatar-actions .btn-remove:hover {
        background: #e74c3c;
        color: white;
    }

    .avatar-upload-wrapper .avatar-info {
        font-size: 0.75rem;
        color: var(--text-secondary);
    }

    /* Botões */
    .form-actions {
        display: flex;
        gap: 12px;
        margin-top: 25px;
        padding-top: 20px;
        border-top: 2px solid var(--border);
        flex-wrap: wrap;
    }

    .form-actions .btn-submit {
        padding: 12px 35px;
        border: none;
        border-radius: 12px;
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 4px 16px rgba(108, 60, 225, 0.3);
    }

    .form-actions .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(108, 60, 225, 0.4);
    }

    .form-actions .btn-submit:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none !important;
        box-shadow: none !important;
    }

    .form-actions .btn-cancel {
        padding: 12px 30px;
        border: 2px solid var(--border);
        border-radius: 12px;
        background: transparent;
        color: var(--text-secondary);
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .form-actions .btn-cancel:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: var(--bg);
    }

    /* Toast */
    .toast-profile-edit {
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
        border: 1px solid rgba(76, 175, 80, 0.3);
    }

    .toast-profile-edit.error { border-color: #f44336; }
    .toast-profile-edit i { margin-right: 8px; }
    .toast-profile-edit .icon-success { color: #4caf50; }
    .toast-profile-edit .icon-error { color: #f44336; }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateX(-50%) translateY(20px); }
        to { opacity: 1; transform: translateX(-50%) translateY(0); }
    }

    /* Responsive */
    @media (max-width: 768px) {
        .edit-profile-card { padding: 20px; }
        .form-row { grid-template-columns: 1fr; gap: 0; }
        .form-row-3 { grid-template-columns: 1fr 1fr; gap: 10px; }
        .avatar-upload-wrapper { flex-direction: column; align-items: center; text-align: center; }
        .avatar-upload-wrapper .avatar-actions { align-items: center; }
        .form-actions { flex-direction: column; }
        .form-actions .btn-submit,
        .form-actions .btn-cancel { width: 100%; justify-content: center; }
    }

    @media (max-width: 480px) {
        .edit-profile-card { padding: 15px; }
        .edit-profile-card .section-title { font-size: 0.95rem; }
        .form-group .form-control { font-size: 0.85rem; padding: 8px 12px; }
        .avatar-upload-wrapper .avatar-preview { width: 90px; height: 90px; font-size: 2rem; }
        .form-row-3 { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')
<div class="edit-profile-container">
    <div class="edit-profile-card">
        <form method="POST" action="{{ route('perfil.update') }}" id="editForm">
            @csrf
            @method('PUT')

            <!-- ===== FOTO DE PERFIL ===== -->
            <div class="section-title">
                <i class="fas fa-user-circle"></i>
                <span>Foto de Perfil</span>
            </div>

            <div class="avatar-upload-wrapper">
                <div class="avatar-preview" id="avatarPreview">
                    @php
                        $fotoNome = $perfil->foto ?? null;
                        $fotoUrl = $fotoNome ? route('imagem.foto', ['filename' => $fotoNome]) : null;
                    @endphp
                    
                    @if($fotoUrl)
                        <img src="{{ $fotoUrl }}" 
                             alt="Foto de {{ $perfil->nome }}" 
                             id="fotoPreview"
                             onerror="this.style.display='none'; document.getElementById('placeholderPreview').style.display='flex';">
                        <span class="placeholder" id="placeholderPreview" style="display: none;">{{ substr($perfil->nome, 0, 1) }}</span>
                    @else
                        <span class="placeholder" id="placeholderPreview">{{ substr($perfil->nome, 0, 1) }}</span>
                    @endif
                </div>

                <div class="avatar-actions">
                    <button type="button" class="btn-upload" id="btnUpload">
                        <i class="fas fa-camera"></i> Trocar Foto
                        <input type="file" id="inputFoto" accept="image/*">
                    </button>
                    <button type="button" class="btn-remove" id="btnRemoverFoto">
                        <i class="fas fa-trash-alt"></i> Remover Foto
                    </button>
                    <div class="avatar-info">
                        <i class="fas fa-info-circle"></i> Formatos: JPG, PNG, GIF, WEBP • Máx: 2MB
                    </div>
                </div>
            </div>

            <!-- ===== DADOS PESSOAIS ===== -->
            <div class="section-title">
                <i class="fas fa-user"></i>
                <span>Dados Pessoais</span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Nome Completo <span class="required">*</span></label>
                    <input type="text" name="nome" class="form-control @error('nome') error @enderror" 
                           value="{{ old('nome', $perfil->nome) }}" required>
                    @error('nome') <span class="error-text">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Nome para Carteira</label>
                    <input type="text" name="nome_carteira" class="form-control @error('nome_carteira') error @enderror" 
                           value="{{ old('nome_carteira', $perfil->nome_carteira) }}">
                    <span class="help-text">Como aparecerá na carteira digital</span>
                    @error('nome_carteira') <span class="error-text">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Email <span class="required">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') error @enderror" 
                           value="{{ old('email', $perfil->email) }}" required>
                    @error('email') <span class="error-text">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Telefone <span class="required">*</span></label>
                    <input type="text" name="telefone" class="form-control @error('telefone') error @enderror" 
                           value="{{ old('telefone', $perfil->telefone) }}" required>
                    @error('telefone') <span class="error-text">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Telefone 2 (WhatsApp)</label>
                    <input type="text" name="telefone2" class="form-control @error('telefone2') error @enderror" 
                           value="{{ old('telefone2', $perfil->telefone2 ?? '') }}">
                    <span class="help-text">Número para contato secundário</span>
                    @error('telefone2') <span class="error-text">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Documento (CPF) <span class="required">*</span></label>
                    <input type="text" name="documento" class="form-control @error('documento') error @enderror" 
                           value="{{ old('documento', $perfil->documento) }}" required>
                    @error('documento') <span class="error-text">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Data de Nascimento <span class="required">*</span></label>
                    <input type="date" name="dataNascimento" class="form-control @error('dataNascimento') error @enderror" 
                           value="{{ old('dataNascimento', $perfil->dataNascimento ? date('Y-m-d', strtotime($perfil->dataNascimento)) : '') }}" required>
                    @error('dataNascimento') <span class="error-text">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Estado Civil</label>
                    <select name="estadoCivil" class="form-control @error('estadoCivil') error @enderror">
                        <option value="">Selecione...</option>
                        <option value="Solteiro(a)" {{ old('estadoCivil', $perfil->estadoCivil) == 'Solteiro(a)' ? 'selected' : '' }}>Solteiro(a)</option>
                        <option value="Casado(a)" {{ old('estadoCivil', $perfil->estadoCivil) == 'Casado(a)' ? 'selected' : '' }}>Casado(a)</option>
                        <option value="Divorciado(a)" {{ old('estadoCivil', $perfil->estadoCivil) == 'Divorciado(a)' ? 'selected' : '' }}>Divorciado(a)</option>
                        <option value="Viúvo(a)" {{ old('estadoCivil', $perfil->estadoCivil) == 'Viúvo(a)' ? 'selected' : '' }}>Viúvo(a)</option>
                        <option value="União Estável" {{ old('estadoCivil', $perfil->estadoCivil) == 'União Estável' ? 'selected' : '' }}>União Estável</option>
                    </select>
                    @error('estadoCivil') <span class="error-text">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Nome da Mãe</label>
                    <input type="text" name="mae" class="form-control @error('mae') error @enderror" 
                           value="{{ old('mae', $perfil->mae) }}">
                    @error('mae') <span class="error-text">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Nome do Pai</label>
                    <input type="text" name="pai" class="form-control @error('pai') error @enderror" 
                           value="{{ old('pai', $perfil->pai) }}">
                    @error('pai') <span class="error-text">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-group">
                <label>Biografia</label>
                <textarea name="bio" class="form-control @error('bio') error @enderror" rows="3" maxlength="500">{{ old('bio', $perfil->bio) }}</textarea>
                <span class="help-text">Máximo 500 caracteres - Conte um pouco sobre você</span>
                @error('bio') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <!-- ===== ENDEREÇO ===== -->
            <div class="section-title">
                <i class="fas fa-map-marker-alt"></i>
                <span>Endereço</span>
            </div>

            <div class="form-group">
                <label>Logradouro</label>
                <input type="text" name="logradouro" class="form-control @error('logradouro') error @enderror" 
                       value="{{ old('logradouro', $perfil->logradouro) }}">
                <span class="help-text">Rua, Avenida, etc.</span>
                @error('logradouro') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label>Endereço <span class="required">*</span></label>
                <input type="text" name="endereco" class="form-control @error('endereco') error @enderror" 
                       value="{{ old('endereco', $perfil->endereco) }}" required>
                @error('endereco') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="form-row-3">
                <div class="form-group">
                    <label>Número <span class="required">*</span></label>
                    <input type="number" name="numero" class="form-control @error('numero') error @enderror" 
                           value="{{ old('numero', $perfil->numero) }}" required>
                    @error('numero') <span class="error-text">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Bairro <span class="required">*</span></label>
                    <input type="text" name="bairro" class="form-control @error('bairro') error @enderror" 
                           value="{{ old('bairro', $perfil->bairro) }}" required>
                    @error('bairro') <span class="error-text">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>CEP <span class="required">*</span></label>
                    <input type="text" name="cep" class="form-control @error('cep') error @enderror" 
                           value="{{ old('cep', $perfil->cep) }}" required>
                    @error('cep') <span class="error-text">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Cidade <span class="required">*</span></label>
                    <input type="text" name="cidade" class="form-control @error('cidade') error @enderror" 
                           value="{{ old('cidade', $perfil->cidade) }}" required>
                    @error('cidade') <span class="error-text">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>UF <span class="required">*</span></label>
                    <select name="uf" class="form-control @error('uf') error @enderror" required>
                        <option value="">Selecione</option>
                        @php
                            $ufs = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'];
                        @endphp
                        @foreach($ufs as $uf)
                            <option value="{{ $uf }}" {{ old('uf', $perfil->uf) == $uf ? 'selected' : '' }}>{{ $uf }}</option>
                        @endforeach
                    </select>
                    @error('uf') <span class="error-text">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- ===== DADOS DA IGREJA ===== -->
            <div class="section-title">
                <i class="fas fa-church"></i>
                <span>Dados Ministeriais</span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Congregação <span class="required">*</span></label>
                    <input type="text" name="congregacao" class="form-control @error('congregacao') error @enderror" 
                           value="{{ old('congregacao', $perfil->congregacao) }}" required>
                    @error('congregacao') <span class="error-text">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Função <span class="required">*</span></label>
                    <select name="funcao" class="form-control @error('funcao') error @enderror" required>
                        <option value="Membro" {{ old('funcao', $perfil->funcao) == 'Membro' ? 'selected' : '' }}>Membro</option>
                        <option value="Auxiliar" {{ old('funcao', $perfil->funcao) == 'Auxiliar' ? 'selected' : '' }}>Auxiliar</option>
                        <option value="Diacono" {{ old('funcao', $perfil->funcao) == 'Diacono' ? 'selected' : '' }}>Diácono</option>
                        <option value="Presbitero" {{ old('funcao', $perfil->funcao) == 'Presbitero' ? 'selected' : '' }}>Presbítero</option>
                        <option value="Evangelista" {{ old('funcao', $perfil->funcao) == 'Evangelista' ? 'selected' : '' }}>Evangelista</option>
                        <option value="Pastor" {{ old('funcao', $perfil->funcao) == 'Pastor' ? 'selected' : '' }}>Pastor</option>
                        <option value="Pastor-Presidente" {{ old('funcao', $perfil->funcao) == 'Pastor-Presidente' ? 'selected' : '' }}>Pastor Presidente</option>
                        <option value="Vice-Presidente" {{ old('funcao', $perfil->funcao) == 'Vice-Presidente' ? 'selected' : '' }}>Vice Presidente</option>
                    </select>
                    @error('funcao') <span class="error-text">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Data de Batismo</label>
                    <input type="date" name="dataBatismo" class="form-control @error('dataBatismo') error @enderror" 
                           value="{{ old('dataBatismo', $perfil->dataBatismo ? date('Y-m-d', strtotime($perfil->dataBatismo)) : '') }}">
                    @error('dataBatismo') <span class="error-text">{{ $message }}</span> @enderror
                </div>
                <div class="form-group" id="consagracaoGroup" style="{{ in_array(old('funcao', $perfil->funcao), ['Auxiliar','Diacono','Presbitero','Evangelista','Pastor','Pastor-Presidente','Vice-Presidente']) ? '' : 'display:none;' }}">
                    <label>Data de Consagração</label>
                    <input type="date" name="data_Consagracao" class="form-control @error('data_Consagracao') error @enderror" 
                           value="{{ old('data_Consagracao', $perfil->data_Consagracao ? date('Y-m-d', strtotime($perfil->data_Consagracao)) : '') }}">
                    <span class="help-text">Obrigatória para cargos ministeriais</span>
                    @error('data_Consagracao') <span class="error-text">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- ===== SENHA ===== -->
            <div class="section-title">
                <i class="fas fa-lock"></i>
                <span>Alterar Senha</span>
            </div>

            <div class="form-group">
                <span class="help-text">Preencha apenas se quiser alterar sua senha</span>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Senha Atual</label>
                    <input type="password" name="current_password" class="form-control @error('current_password') error @enderror" 
                           placeholder="Digite sua senha atual">
                    @error('current_password') <span class="error-text">{{ $message }}</span> @enderror
                </div>
                <div class="form-group">
                    <label>Nova Senha</label>
                    <input type="password" name="new_password" class="form-control @error('new_password') error @enderror" 
                           placeholder="Digite a nova senha (mínimo 8 caracteres)">
                    @error('new_password') <span class="error-text">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="form-group">
                <label>Confirmar Nova Senha</label>
                <input type="password" name="new_password_confirmation" class="form-control" 
                       placeholder="Confirme a nova senha">
            </div>

            <!-- ===== PRIVACIDADE ===== -->
            <div class="section-title">
                <i class="fas fa-shield-alt"></i>
                <span>Privacidade</span>
            </div>

            <div class="form-group">
                <label style="display: flex; align-items: center; gap: 10px; cursor: pointer; font-weight: 400;">
                    <input type="checkbox" name="privacidade" value="1" 
                           {{ old('privacidade', $perfil->privacidade) ? 'checked' : '' }}>
                    <span>Perfil privado (apenas membros podem ver)</span>
                </label>
                <span class="help-text">Desmarque para tornar seu perfil visível para todos</span>
            </div>

            <!-- ===== BOTÕES ===== -->
            <div class="form-actions">
                <button type="submit" class="btn-submit" id="btnSubmit">
                    <i class="fas fa-save"></i> Salvar Alterações
                </button>
                <a href="{{ route('perfil.show', $perfil->matricula) }}" class="btn-cancel">
                    <i class="fas fa-times"></i> Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Toast -->
<div class="toast-profile-edit" id="toastEdit">
    <i class="fas fa-check-circle icon-success"></i>
    <span id="toastMessage">Foto atualizada com sucesso!</span>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ===== FUNÇÃO PARA MOSTRAR TOAST =====
    function mostrarToast(message, type = 'success') {
        const toast = document.getElementById('toastEdit');
        const messageEl = document.getElementById('toastMessage');
        const icon = toast.querySelector('i');
        
        messageEl.textContent = message;
        toast.className = 'toast-profile-edit';
        
        if (type === 'error') {
            toast.classList.add('error');
            icon.className = 'fas fa-times-circle icon-error';
        } else {
            icon.className = 'fas fa-check-circle icon-success';
        }
        
        toast.style.display = 'block';
        clearTimeout(toast._timeout);
        toast._timeout = setTimeout(() => {
            toast.style.display = 'none';
        }, 4000);
    }

    // ============================================================
    // ⭐ UPLOAD DE FOTO
    // ============================================================
    const inputFoto = document.getElementById('inputFoto');
    const avatarPreview = document.getElementById('avatarPreview');
    const fotoPreview = document.getElementById('fotoPreview');
    const placeholderPreview = document.getElementById('placeholderPreview');
    const btnUpload = document.getElementById('btnUpload');
    const btnRemover = document.getElementById('btnRemoverFoto');

    function atualizarPreview(url) {
        const timestamp = new Date().getTime();
        const urlComTimestamp = url + '?t=' + timestamp;
        
        if (fotoPreview) {
            fotoPreview.src = urlComTimestamp;
            fotoPreview.style.display = 'block';
            fotoPreview.onerror = function() {
                this.style.display = 'none';
                if (placeholderPreview) {
                    placeholderPreview.style.display = 'flex';
                }
            };
        } else {
            const img = document.createElement('img');
            img.id = 'fotoPreview';
            img.src = urlComTimestamp;
            img.alt = 'Foto de perfil';
            img.onerror = function() {
                this.style.display = 'none';
                if (placeholderPreview) {
                    placeholderPreview.style.display = 'flex';
                }
            };
            avatarPreview.prepend(img);
        }
        
        if (placeholderPreview) {
            placeholderPreview.style.display = 'none';
        }
    }

    inputFoto.addEventListener('change', function() {
        const file = this.files[0];
        if (!file) return;

        if (file.size > 2 * 1024 * 1024) {
            mostrarToast('A imagem deve ter no máximo 2MB.', 'error');
            this.value = '';
            return;
        }

        const tiposPermitidos = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
        if (!tiposPermitidos.includes(file.type)) {
            mostrarToast('Formato não permitido. Use JPEG, PNG, JPG, GIF ou WEBP.', 'error');
            this.value = '';
            return;
        }

        // Preview
        const reader = new FileReader();
        reader.onload = function(e) {
            atualizarPreview(e.target.result);
        };
        reader.readAsDataURL(file);

        // Upload
        const formData = new FormData();
        formData.append('foto', file);

        const url = '{{ route("perfil.foto.upload") }}';

        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarToast('Foto atualizada com sucesso! ✅', 'success');
                
                if (data.foto_url) {
                    atualizarPreview(data.foto_url);
                    
                    if (window.FotoEvent) {
                        window.FotoEvent.atualizada(data.foto_url);
                    } else if (window.atualizarTodasFotos) {
                        window.atualizarTodasFotos(data.foto_url);
                    }
                }
                inputFoto.value = '';
            } else {
                mostrarToast(data.message || 'Erro ao atualizar foto.', 'error');
                inputFoto.value = '';
            }
        })
        .catch(error => {
            console.error('Erro no upload:', error);
            mostrarToast('Erro ao fazer upload. Tente novamente.', 'error');
            inputFoto.value = '';
        });
    });

    // ===== REMOVER FOTO =====
    btnRemover.addEventListener('click', function() {
        if (!confirm('Deseja remover sua foto de perfil?')) return;
        
        fetch('{{ route("perfil.foto.remover") }}', {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const img = document.getElementById('fotoPreview');
                if (img) img.remove();
                
                let placeholder = document.getElementById('placeholderPreview');
                if (!placeholder) {
                    const span = document.createElement('span');
                    span.id = 'placeholderPreview';
                    span.className = 'placeholder';
                    span.textContent = '{{ substr($perfil->nome, 0, 1) }}';
                    avatarPreview.prepend(span);
                    placeholder = span;
                } else {
                    placeholder.style.display = 'flex';
                }
                
                if (window.FotoEvent) {
                    window.FotoEvent.removida();
                } else if (window.removerTodasFotos) {
                    window.removerTodasFotos();
                }
                
                mostrarToast('Foto removida com sucesso!', 'success');
            } else {
                mostrarToast(data.message || 'Erro ao remover foto.', 'error');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            mostrarToast('Erro ao remover foto.', 'error');
        });
    });

    // ===== MOSTRAR/OCULTAR DATA DE CONSAGRAÇÃO =====
    const funcaoSelect = document.querySelector('select[name="funcao"]');
    const consagracaoGroup = document.getElementById('consagracaoGroup');
    
    if (funcaoSelect && consagracaoGroup) {
        const funcoesMinisteriais = ['Auxiliar', 'Diacono', 'Presbitero', 'Evangelista', 'Pastor', 'Pastor-Presidente', 'Vice-Presidente'];
        
        funcaoSelect.addEventListener('change', function() {
            if (funcoesMinisteriais.includes(this.value)) {
                consagracaoGroup.style.display = 'block';
            } else {
                consagracaoGroup.style.display = 'none';
            }
        });
    }

    // ===== MÁSCARAS =====
    // Telefone
    const telefoneInput = document.querySelector('input[name="telefone"]');
    if (telefoneInput) {
        telefoneInput.addEventListener('input', function(e) {
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
    }

    // CPF
    const documentoInput = document.querySelector('input[name="documento"]');
    if (documentoInput) {
        documentoInput.addEventListener('input', function(e) {
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
    }

    // CEP
    const cepInput = document.querySelector('input[name="cep"]');
    if (cepInput) {
        cepInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 5) {
                value = value.replace(/^(\d{5})(\d{3})/, '$1-$2');
            }
            e.target.value = value;
        });
    }

    // ===== SUBMIT DO FORMULÁRIO =====
    const form = document.getElementById('editForm');
    const btnSubmit = document.getElementById('btnSubmit');

    form.addEventListener('submit', function() {
        btnSubmit.disabled = true;
        btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
    });

    console.log('📝 Perfil edit carregado');
    console.log('👤 Usuário: {{ $perfil->nome }}');
    console.log('📋 Matrícula: {{ $perfil->matricula }}');
});
</script>
@endpush
@endsection