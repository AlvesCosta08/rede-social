@extends('layouts.app')

@section('title', 'Perfil - ' . $perfil->nome)

@section('styles')
<style>
    .profile-container {
        max-width: 700px;
        margin: 0 auto;
        padding: 10px;
    }

    .profile-card {
        background: white;
        border-radius: 20px;
        padding: 30px 25px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .profile-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #c9a84c, #f2d680, #c9a84c);
    }

    .profile-avatar-wrapper {
        position: relative;
        width: 120px;
        height: 120px;
        margin: 0 auto 15px;
        cursor: default;
    }

    .profile-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: linear-gradient(135deg, #d4af37, #c9a84c);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        font-weight: 700;
        color: #1a1a2e;
        border: 4px solid #c9a84c;
        overflow: hidden;
        position: relative;
        transition: all 0.3s ease;
    }

    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .profile-avatar .placeholder {
        font-size: 3rem;
        font-weight: 700;
        color: #1a1a2e;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
    }

    .avatar-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
        cursor: pointer;
        color: white;
        gap: 4px;
    }

    .avatar-overlay i { font-size: 1.5rem; }
    .avatar-overlay span { font-size: 0.7rem; font-weight: 500; }

    .profile-avatar-wrapper:hover .avatar-overlay {
        opacity: 1;
    }

    .btn-upload-avatar {
        position: absolute;
        bottom: 0;
        right: 0;
        background: rgba(108, 60, 225, 0.9);
        color: white;
        border: 3px solid white;
        border-radius: 50%;
        width: 38px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        z-index: 5;
    }

    .btn-upload-avatar:hover {
        transform: scale(1.1);
        background: #6c3ce1;
    }

    .btn-upload-avatar input[type="file"] {
        position: absolute;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        top: 0;
        left: 0;
    }

    .btn-upload-avatar i { font-size: 0.9rem; }

    .profile-name {
        font-size: 1.6rem;
        font-weight: 700;
        margin: 0 0 4px 0;
        color: #1a1a2e;
    }

    .profile-function {
        color: #c9a84c;
        font-weight: 600;
        font-size: 1rem;
        margin: 0 0 8px 0;
    }

    .profile-status {
        display: inline-block;
        padding: 4px 18px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: white;
    }

    .profile-status.ativo { background: #4caf50; }
    .profile-status.inativo { background: #f44336; }
    .profile-status.pendente { background: #ff9800; }

    .profile-bio {
        margin-top: 15px;
        padding: 15px 20px;
        background: #f8f6f2;
        border-radius: 12px;
        font-style: italic;
        color: #4a4a5a;
        font-size: 0.95rem;
        line-height: 1.6;
        position: relative;
    }

    .profile-bio::before {
        content: '"';
        font-size: 2rem;
        color: #c9a84c;
        opacity: 0.3;
        position: absolute;
        top: 5px;
        left: 10px;
    }

    .profile-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #e8e4db;
    }

    .profile-info-grid .info-item { text-align: center; }
    .profile-info-grid .info-item .label {
        font-size: 0.65rem;
        text-transform: uppercase;
        opacity: 0.5;
        letter-spacing: 0.5px;
        font-weight: 600;
    }
    .profile-info-grid .info-item .value {
        font-weight: 600;
        margin-top: 4px;
        font-size: 1rem;
        color: #1a1a2e;
    }

    .profile-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .profile-actions .btn {
        padding: 10px 20px;
        border: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        color: white;
    }

    .profile-actions .btn-primary {
        background: linear-gradient(145deg, #c9a84c, #b8960f);
    }

    .profile-actions .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(201, 168, 76, 0.4);
        filter: brightness(1.05);
    }

    .profile-actions .btn-edit {
        background: linear-gradient(145deg, #2c3e50, #1a2a3a);
    }

    .profile-actions .btn-edit:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(44, 62, 80, 0.3);
        filter: brightness(1.1);
    }

    .profile-actions .btn-follow {
        background: linear-gradient(145deg, #4caf50, #388e3c);
    }

    .profile-actions .btn-follow:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.4);
        filter: brightness(1.05);
    }

    .profile-actions .btn-unfollow {
        background: linear-gradient(145deg, #f44336, #c62828);
    }

    .profile-actions .btn-unfollow:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(244, 67, 54, 0.4);
        filter: brightness(1.05);
    }

    .publications-section { margin-top: 30px; }
    .publications-section .section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.2rem;
        font-weight: 600;
        color: #1a1a2e;
        margin-bottom: 15px;
    }
    .publications-section .section-title i { color: #c9a84c; }

    .publication-card {
        background: white;
        border-radius: 16px;
        padding: 18px 20px;
        margin-bottom: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        transition: all 0.3s ease;
    }

    .publication-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .publication-card .content {
        font-size: 0.95rem;
        line-height: 1.6;
        color: #2d2d3f;
    }

    .publication-card .meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 10px;
        padding-top: 10px;
        border-top: 1px solid #f0ece6;
        font-size: 0.75rem;
        color: #999;
    }

    .publication-card .meta .stats {
        display: flex;
        gap: 15px;
    }

    .publication-card .meta .stats span {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        color: #888;
    }

    .publication-card .meta .stats .heart { color: #e74c3c; }

    .empty-publications {
        text-align: center;
        padding: 40px 20px;
        opacity: 0.5;
        background: #faf8f5;
        border-radius: 16px;
    }

    .empty-publications i {
        font-size: 2rem;
        display: block;
        margin-bottom: 10px;
        color: #c9a84c;
        opacity: 0.3;
    }

    .toast-profile {
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

    .toast-profile.error { border-color: #f44336; }
    .toast-profile i { margin-right: 8px; }
    .toast-profile .icon-success { color: #4caf50; }
    .toast-profile .icon-error { color: #f44336; }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateX(-50%) translateY(20px); }
        to { opacity: 1; transform: translateX(-50%) translateY(0); }
    }

    @media (max-width: 480px) {
        .profile-card { padding: 20px 16px; }
        .profile-avatar { width: 90px; height: 90px; font-size: 2.2rem; }
        .profile-avatar-wrapper { width: 90px; height: 90px; }
        .profile-name { font-size: 1.3rem; }
        .profile-info-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
        .profile-info-grid .info-item .label { font-size: 0.55rem; }
        .profile-actions .btn { font-size: 0.75rem; padding: 8px 14px; }
        .btn-upload-avatar { width: 30px; height: 30px; }
        .btn-upload-avatar i { font-size: 0.7rem; }
        .avatar-overlay i { font-size: 1.2rem; }
        .avatar-overlay span { font-size: 0.6rem; }
        .publication-card { padding: 14px 16px; }
        .publication-card .content { font-size: 0.85rem; }
    }
</style>
@endsection

@section('content')
<div class="profile-container">
    <!-- ===== CARD DO PERFIL ===== -->
    <div class="profile-card">
        <!-- ============================================================
        ⭐ AVATAR - USA ROTA DO LARAVEL COM FALLBACK ⭐
        ============================================================ -->
        <div class="profile-avatar-wrapper">
            <div class="profile-avatar" id="avatarContainer">
                @php
                    $fotoNome = $perfil->foto ?? null;
                    $fotoUrl = $fotoNome ? route('imagem.foto', ['filename' => $fotoNome]) : null;
                    $inicial = substr($perfil->nome, 0, 1);
                @endphp
                
                @if($fotoUrl)
                    <img src="{{ $fotoUrl }}" 
                         alt="Foto de {{ $perfil->nome }}" 
                         id="fotoPerfil"
                         onerror="this.style.display='none'; document.getElementById('fotoPlaceholder').style.display='flex';">
                    <span class="placeholder" id="fotoPlaceholder" style="display: none;">{{ $inicial }}</span>
                @else
                    <span class="placeholder" id="fotoPlaceholder">{{ $inicial }}</span>
                @endif
            </div>

            <div class="avatar-overlay" id="avatarOverlay">
                <i class="fas fa-camera"></i>
                <span>Trocar foto</span>
            </div>

            {{-- ⭐ BOTÃO UPLOAD - APENAS O DONO DO PERFIL --}}
            @if($membroLogado && $membroLogado->matricula == $perfil->matricula)
                <div class="btn-upload-avatar" id="btnUploadAvatar" title="Trocar foto">
                    <i class="fas fa-camera"></i>
                    <input type="file" id="inputFoto" accept="image/*">
                </div>
            @endif
        </div>

        <h1 class="profile-name">{{ $perfil->nome }}</h1>
        <p class="profile-function">{{ $perfil->funcao ? ucfirst(str_replace('_', ' ', $perfil->funcao)) : 'Membro' }}</p>
        <span class="profile-status {{ strtolower($perfil->status ?? 'inativo') }}">
            {{ strtoupper($perfil->status ?? 'INATIVO') }}
        </span>

        @if($perfil->bio)
            <div class="profile-bio">{{ $perfil->bio }}</div>
        @endif

        <div class="profile-info-grid">
            <div class="info-item">
                <div class="label"><i class="fas fa-id-card"></i> Matrícula</div>
                <div class="value">#{{ $perfil->matricula }}</div>
            </div>
            <div class="info-item">
                <div class="label"><i class="fas fa-church"></i> Congregação</div>
                <div class="value">{{ $perfil->congregacao ? ucfirst(str_replace('_', ' ', $perfil->congregacao)) : 'Não informada' }}</div>
            </div>
            <div class="info-item">
                <div class="label"><i class="fas fa-calendar-alt"></i> Ingresso</div>
                <div class="value">
                    @if($perfil->datCadastro)
                        {{ \Carbon\Carbon::parse($perfil->datCadastro)->format('d/m/Y') }}
                    @else
                        -
                    @endif
                </div>
            </div>
            <div class="info-item">
                <div class="label"><i class="fas fa-comment-dots"></i> Publicações</div>
                <div class="value">{{ $perfil->publicacoes->count() }}</div>
            </div>
            <div class="info-item">
                <div class="label"><i class="fas fa-phone"></i> Telefone</div>
                <div class="value">{{ $perfil->telefone ?: '-' }}</div>
            </div>
            <div class="info-item">
                <div class="label"><i class="fas fa-map-marker-alt"></i> Cidade/UF</div>
                <div class="value">{{ $perfil->cidade ? $perfil->cidade . '/' . $perfil->uf : '-' }}</div>
            </div>
            <div class="info-item">
                <div class="label"><i class="fas fa-water"></i> Batismo</div>
                <div class="value">
                    @if($perfil->dataBatismo)
                        {{ \Carbon\Carbon::parse($perfil->dataBatismo)->format('d/m/Y') }}
                    @else
                        -
                    @endif
                </div>
            </div>
            @php
                $funcoesMinisteriais = ['Auxiliar', 'Obreiro', 'Diacono', 'Diácono', 'Presbitero', 'Presbítero', 'Evangelista', 'Pastor', 'Pastora', 'Pastor-Presidente', 'Vice-Presidente', 'Missionário', 'Missionária'];
                $mostrarConsagracao = in_array($perfil->funcao, $funcoesMinisteriais);
            @endphp

            @if($mostrarConsagracao)
            <div class="info-item">
                <div class="label"><i class="fas fa-hands-praying"></i> Consagração</div>
                <div class="value">
                    @if($perfil->data_Consagracao)
                        {{ \Carbon\Carbon::parse($perfil->data_Consagracao)->format('d/m/Y') }}
                    @else
                        Não informada
                    @endif
                </div>
            </div>
            @endif
        </div>

        <div class="profile-actions">
            <a href="{{ route('membro.cartao', $perfil->matricula) }}" class="btn btn-primary">
                <i class="fas fa-id-card"></i> Ver Cartão
            </a>
            
            {{-- ⭐ BOTÃO EDITAR - APENAS O DONO DO PERFIL --}}
            @if($membroLogado && $membroLogado->matricula == $perfil->matricula)
                <a href="{{ route('perfil.edit') }}" class="btn btn-edit">
                    <i class="fas fa-edit"></i> Editar Perfil
                </a>
            @endif
        </div>
    </div>

    <!-- ===== PUBLICAÇÕES ===== -->
    <div class="publications-section">
        <div class="section-title">
            <i class="fas fa-comment-dots"></i>
            <span>Publicações de {{ $perfil->nome }}</span>
        </div>

        @if($perfil->publicacoes->count() > 0)
            @foreach($perfil->publicacoes as $pub)
                <div class="publication-card">
                    <div class="content">{{ $pub->conteudo }}</div>
                    <div class="meta">
                        <span class="date">
                            <i class="far fa-clock"></i> {{ $pub->created_at->format('d/m/Y H:i') }}
                        </span>
                        <div class="stats">
                            <span>
                                <i class="fas fa-heart heart"></i> {{ $pub->curtidas_count }}
                            </span>
                            <span>
                                <i class="fas fa-comment"></i> {{ $pub->comentarios->count() }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty-publications">
                <i class="fas fa-comment-slash"></i>
                <p>Este membro ainda não publicou nada.</p>
            </div>
        @endif
    </div>
</div>

<!-- Toast para feedback -->
<div class="toast-profile" id="toastProfile">
    <i class="fas fa-check-circle icon-success"></i>
    <span id="toastMessage">Foto atualizada com sucesso!</span>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($membroLogado && $membroLogado->matricula == $perfil->matricula)
    const inputFoto = document.getElementById('inputFoto');
    const toastProfile = document.getElementById('toastProfile');
    const toastMessage = document.getElementById('toastMessage');
    const avatarContainer = document.getElementById('avatarContainer');
    const btnUpload = document.getElementById('btnUploadAvatar');

    function mostrarToast(message, type = 'success') {
        const icon = toastProfile.querySelector('i');
        
        toastMessage.textContent = message;
        toastProfile.className = 'toast-profile';
        
        if (type === 'error') {
            toastProfile.classList.add('error');
            icon.className = 'fas fa-times-circle icon-error';
        } else {
            icon.className = 'fas fa-check-circle icon-success';
        }
        
        toastProfile.style.display = 'block';
        clearTimeout(toastProfile._timeout);
        toastProfile._timeout = setTimeout(() => {
            toastProfile.style.display = 'none';
        }, 4000);
    }

    function atualizarFoto(url) {
        const fotoPerfil = document.getElementById('fotoPerfil');
        const fotoPlaceholder = document.getElementById('fotoPlaceholder');
        
        const timestamp = new Date().getTime();
        const urlComTimestamp = url + '?t=' + timestamp;
        
        if (fotoPerfil) {
            fotoPerfil.src = urlComTimestamp;
            fotoPerfil.style.display = 'block';
            fotoPerfil.onerror = function() {
                this.style.display = 'none';
                if (fotoPlaceholder) {
                    fotoPlaceholder.style.display = 'flex';
                }
            };
        } else {
            const img = document.createElement('img');
            img.id = 'fotoPerfil';
            img.src = urlComTimestamp;
            img.alt = 'Foto de perfil';
            img.style.width = '100%';
            img.style.height = '100%';
            img.style.objectFit = 'cover';
            img.onerror = function() {
                this.style.display = 'none';
                if (fotoPlaceholder) {
                    fotoPlaceholder.style.display = 'flex';
                }
            };
            avatarContainer.prepend(img);
        }
        
        if (fotoPlaceholder) {
            fotoPlaceholder.style.display = 'none';
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

        if (btnUpload) {
            btnUpload.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            btnUpload.style.pointerEvents = 'none';
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('fotoPerfil');
            if (img) {
                img.src = e.target.result;
                img.style.display = 'block';
            }
            const placeholder = document.getElementById('fotoPlaceholder');
            if (placeholder) {
                placeholder.style.display = 'none';
            }
        };
        reader.readAsDataURL(file);

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
            console.log('Resposta do upload:', data);
            
            if (data.success) {
                mostrarToast('Foto atualizada com sucesso! ✅', 'success');
                
                if (data.foto_url) {
                    atualizarFoto(data.foto_url);
                    
                    if (window.FotoEvent) {
                        window.FotoEvent.atualizada(data.foto_url);
                        console.log('📸 Evento global de atualização de foto disparado');
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
        })
        .finally(() => {
            if (btnUpload) {
                btnUpload.innerHTML = '<i class="fas fa-camera"></i>';
                btnUpload.style.pointerEvents = 'auto';
            }
        });
    });

    avatarContainer.addEventListener('dblclick', function(e) {
        if (e.target.closest('.btn-upload-avatar') || e.target.closest('#inputFoto')) return;
        
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
                const img = document.getElementById('fotoPerfil');
                if (img) img.remove();
                
                let placeholder = document.getElementById('fotoPlaceholder');
                if (!placeholder) {
                    const span = document.createElement('span');
                    span.id = 'fotoPlaceholder';
                    span.className = 'placeholder';
                    span.textContent = '{{ substr($perfil->nome, 0, 1) }}';
                    avatarContainer.prepend(span);
                    placeholder = span;
                } else {
                    placeholder.style.display = 'flex';
                }
                
                if (window.FotoEvent) {
                    window.FotoEvent.removida();
                    console.log('🗑️ Evento global de remoção de foto disparado');
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

    console.log('🖼️ Sistema de upload de foto carregado');
    console.log('💡 Dê duplo clique na foto para removê-la');
    @endif

    @if(session('success'))
        setTimeout(function() {
            const toast = document.getElementById('toastProfile');
            const message = document.getElementById('toastMessage');
            const icon = toast.querySelector('i');
            message.textContent = '{{ session('success') }}';
            toast.className = 'toast-profile';
            icon.className = 'fas fa-check-circle icon-success';
            toast.style.display = 'block';
            setTimeout(() => { toast.style.display = 'none'; }, 4000);
        }, 500);
    @endif
});
</script>
@endpush
@endsection