<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Conexão Igreja')</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        /* ============================================================
           CSS VARIABLES - TEMA
           ============================================================ */
        :root {
            --primary: #6C3CE1;
            --primary-dark: #5A2FC4;
            --primary-light: #8B6BE6;
            --secondary: #FF6B6B;
            --accent: #F7C948;
            --success: #2ECC71;
            --danger: #E74C3C;
            --warning: #F39C12;
            --info: #3498DB;
            
            --bg: #F0EEF5;
            --bg-card: #FFFFFF;
            --text: #1A1A2E;
            --text-secondary: #6B6B7B;
            --border: #E8E5F0;
            --shadow: 0 8px 32px rgba(108, 60, 225, 0.12);
            --shadow-hover: 0 12px 48px rgba(108, 60, 225, 0.18);
            
            --radius: 16px;
            --radius-sm: 10px;
            --radius-lg: 24px;
            
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-slow: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            
            --navbar-height: 70px;
            --sidebar-width: 260px;
        }

        /* TEMA ESCURO */
        body.dark {
            --bg: #12121A;
            --bg-card: #1E1E2E;
            --text: #EDEDF5;
            --text-secondary: #9A9AAF;
            --border: #2A2A3E;
            --shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
            --shadow-hover: 0 12px 48px rgba(0, 0, 0, 0.5);
        }

        /* ============================================================
           RESET
           ============================================================ */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg);
            color: var(--text);
            transition: var(--transition);
            min-height: 100vh;
            display: flex;
        }

        /* ============================================================
           SCROLLBAR PERSONALIZADA
           ============================================================ */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg); }
        ::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--primary-dark); }

        /* ============================================================
           SIDEBAR
           ============================================================ */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--bg-card);
            border-right: 1px solid var(--border);
            padding: 20px 0;
            overflow-y: auto;
            transition: var(--transition);
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 24px 24px 24px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 20px;
        }

        .sidebar-brand .logo {
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            color: white;
            flex-shrink: 0;
        }

        .sidebar-brand h1 {
            font-size: 1.1rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .sidebar-brand span {
            font-size: 0.7rem;
            color: var(--text-secondary);
            display: block;
            -webkit-text-fill-color: var(--text-secondary);
            font-weight: 400;
        }

        .sidebar-nav {
            flex: 1;
            padding: 0 12px;
        }

        .sidebar-nav .nav-label {
            font-size: 0.65rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: var(--text-secondary);
            padding: 12px 12px 8px 12px;
            font-weight: 600;
        }

        .sidebar-nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            color: var(--text-secondary);
            text-decoration: none;
            transition: var(--transition);
            font-weight: 500;
            font-size: 0.9rem;
            position: relative;
            margin-bottom: 2px;
        }

        .sidebar-nav a i {
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
        }

        .sidebar-nav a:hover {
            background: var(--primary);
            color: white;
        }

        .sidebar-nav a.active {
            background: var(--primary);
            color: white;
            box-shadow: 0 4px 16px rgba(108, 60, 225, 0.3);
        }

        .sidebar-nav a .badge {
            margin-left: auto;
            background: var(--accent);
            color: #1A1A2E;
            font-size: 0.65rem;
            padding: 2px 10px;
            border-radius: 20px;
            font-weight: 700;
        }

        /* ⭐ BADGE DE NÍVEL DO USUÁRIO */
        .badge-nivel {
            display: inline-block;
            padding: 1px 8px;
            border-radius: 10px;
            font-size: 0.55rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-left: auto;
        }

        .badge-nivel-admin {
            background: #f44336;
            color: white;
        }

        .badge-nivel-secretario {
            background: #ff9800;
            color: white;
        }

        .badge-nivel-usuario {
            background: #9e9e9e;
            color: white;
        }

        .sidebar-footer {
            padding: 16px 24px;
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar-footer .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1rem;
            color: white;
            flex-shrink: 0;
            overflow: hidden;
        }

        .sidebar-footer .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .sidebar-footer .user-info {
            flex: 1;
            min-width: 0;
        }

        .sidebar-footer .user-info .name {
            font-weight: 600;
            font-size: 0.85rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .sidebar-footer .user-info .role {
            font-size: 0.7rem;
            color: var(--text-secondary);
        }

        /* ============================================================
           BOTÃO DE LOGOUT
           ============================================================ */
        .logout-form {
            display: inline;
        }

        .logout-btn {
            background: none;
            border: none;
            color: var(--text-secondary);
            font-size: 1.1rem;
            cursor: pointer;
            transition: var(--transition);
            padding: 4px 8px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .logout-btn:hover {
            color: var(--danger);
            background: rgba(231, 76, 60, 0.1);
        }

        .logout-btn i {
            font-size: 1.1rem;
        }

        /* ============================================================
           TOGGLE SIDEBAR (Mobile)
           ============================================================ */
        .sidebar-toggle {
            display: none;
            position: fixed;
            top: 16px;
            left: 16px;
            z-index: 999;
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 10px 14px;
            cursor: pointer;
            color: var(--text);
            font-size: 1.2rem;
            box-shadow: var(--shadow);
            transition: var(--transition);
        }

        .sidebar-toggle:hover {
            transform: scale(1.05);
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 998;
            backdrop-filter: blur(4px);
        }

        /* ============================================================
           MAIN CONTENT
           ============================================================ */
        .main-content {
            margin-left: var(--sidebar-width);
            flex: 1;
            min-height: 100vh;
            padding: 20px 30px;
            transition: var(--transition);
        }

        /* ============================================================
           TOP BAR
           ============================================================ */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border);
        }

        .top-bar .page-title h2 {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .top-bar .page-title p {
            font-size: 0.85rem;
            color: var(--text-secondary);
            margin-top: 2px;
        }

        .top-bar .actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .top-bar .actions .theme-toggle {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 10px 14px;
            cursor: pointer;
            color: var(--text);
            font-size: 1.1rem;
            transition: var(--transition);
        }

        .top-bar .actions .theme-toggle:hover {
            transform: scale(1.05);
            border-color: var(--primary);
        }

        /* ============================================================
           TOASTS
           ============================================================ */
        .toast {
            padding: 14px 20px;
            border-radius: var(--radius-sm);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideDown 0.4s ease;
            border-left: 4px solid;
        }

        .toast-success {
            background: rgba(46, 204, 113, 0.12);
            border-left-color: var(--success);
            color: var(--success);
        }

        .toast-error {
            background: rgba(231, 76, 60, 0.12);
            border-left-color: var(--danger);
            color: var(--danger);
        }

        .toast-info {
            background: rgba(52, 152, 219, 0.12);
            border-left-color: var(--info);
            color: var(--info);
        }

        @keyframes slideDown {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* ============================================================
           CARDS
           ============================================================ */
        .card-modern {
            background: var(--bg-card);
            border-radius: var(--radius);
            padding: 24px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            transition: var(--transition);
        }

        .card-modern:hover {
            box-shadow: var(--shadow-hover);
            transform: translateY(-2px);
        }

        /* ============================================================
           BOTÕES
           ============================================================ */
        .btn-modern {
            padding: 10px 24px;
            border: none;
            border-radius: var(--radius-sm);
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-modern-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
            box-shadow: 0 4px 16px rgba(108, 60, 225, 0.3);
        }

        .btn-modern-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(108, 60, 225, 0.4);
        }

        .btn-modern-danger {
            background: linear-gradient(135deg, var(--danger), #C0392B);
            color: white;
            box-shadow: 0 4px 16px rgba(231, 76, 60, 0.3);
        }

        .btn-modern-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(231, 76, 60, 0.4);
        }

        .btn-modern-outline {
            background: transparent;
            color: var(--text);
            border: 2px solid var(--border);
        }

        .btn-modern-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-2px);
        }

        .btn-modern-success {
            background: linear-gradient(135deg, var(--success), #27AE60);
            color: white;
            box-shadow: 0 4px 16px rgba(46, 204, 113, 0.3);
        }

        .btn-modern-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(46, 204, 113, 0.4);
        }

        /* ============================================================
           RESPONSIVO
           ============================================================ */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.open {
                transform: translateX(0);
            }

            .sidebar-overlay.open {
                display: block;
            }

            .sidebar-toggle {
                display: block;
            }

            .main-content {
                margin-left: 0;
                padding: 20px 16px;
                padding-top: 80px;
            }

            .top-bar {
                flex-direction: column;
                align-items: stretch;
                gap: 12px;
            }

            .top-bar .actions {
                justify-content: flex-end;
            }
        }

        @media (max-width: 480px) {
            .top-bar .page-title h2 { font-size: 1.2rem; }
            .top-bar .actions { flex-wrap: wrap; justify-content: flex-start; }
        }

        /* ============================================================
           UTILITÁRIOS
           ============================================================ */
        .text-center { text-align: center; }
        .text-muted { color: var(--text-secondary); }
        .mt-20 { margin-top: 20px; }
        .mb-20 { margin-bottom: 20px; }
        .gap-12 { gap: 12px; }
        .flex { display: flex; }
        .flex-center { display: flex; align-items: center; justify-content: center; }
        .flex-between { display: flex; justify-content: space-between; align-items: center; }
        .flex-wrap { flex-wrap: wrap; }
        .w-full { width: 100%; }
        .max-w-700 { max-width: 700px; margin: 0 auto; }
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        
        @media (max-width: 600px) { .grid-2 { grid-template-columns: 1fr; } }
    </style>
    
    @yield('styles')
</head>
<body>
    <!-- ============================================================
    SIDEBAR TOGGLE (Mobile)
    ============================================================ -->
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- ============================================================
    SIDEBAR - SÓ APARECE SE ESTIVER LOGADO ✅ USA AUTH
    ============================================================ -->
    @auth
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="logo"><i class="fas fa-cross"></i></div>
            <div>
                <h1>Conexão</h1>
                <span>Rede Cristã</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-label">Menu Principal</div>
            
            <a href="{{ route('feed.index') }}" class="{{ request()->routeIs('feed.index') ? 'active' : '' }}">
                <i class="fas fa-home"></i> Feed
            </a>
            
            <a href="{{ route('perfil.show', auth()->user()->matricula) }}" class="{{ request()->routeIs('perfil.show') ? 'active' : '' }}">
                <i class="fas fa-user"></i> Perfil
            </a>
            
            <a href="{{ route('membro.meu-cartao') }}" class="{{ request()->routeIs('membro.*') ? 'active' : '' }}">
                <i class="fas fa-id-card"></i> Cartão Digital
            </a>
            
            <div class="nav-label" style="margin-top: 20px;">Comunidade</div>
            
            <a href="{{ route('membros.index') }}" class="{{ request()->routeIs('membros.*') ? 'active' : '' }}">
                <i class="fas fa-search"></i> Buscar Membros
            </a>

            <!-- ============================================================
            ⭐ MENU ADMIN - VERIFICA PERMISSÃO COM PODE()
            ============================================================ -->
            @if(auth()->user()?->pode('dashboard'))
                <div class="nav-label" style="margin-top: 20px;">Administração</div>
                
                <!-- ⭐ GERENCIAR MEMBROS - ADMIN E SECRETÁRIO -->
                <a href="{{ route('admin.membros.index') }}" class="{{ request()->routeIs('admin.membros.*') ? 'active' : '' }}">
                    <i class="fas fa-users-cog"></i> Gerenciar Membros
                    @if(auth()->user()->isAdmin())
                        <span class="badge">👑</span>
                    @elseif(auth()->user()->isSecretario())
                        <span class="badge">📋</span>
                    @endif
                </a>

                <!-- ⭐ ESTATÍSTICAS - ADMIN E SECRETÁRIO -->
                <a href="{{ route('admin.estatisticas') }}" class="{{ request()->routeIs('admin.estatisticas') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i> Estatísticas
                </a>

                <!-- ⭐ GERENCIAR SECRETÁRIOS - APENAS ADMIN -->
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.secretarios.index') }}" class="{{ request()->routeIs('admin.secretarios.*') ? 'active' : '' }}">
                        <i class="fas fa-user-tie"></i> Secretários
                        <span class="badge">👑</span>
                    </a>
                @endif
            @endif
        </nav>

        <!-- ============================================================
        FOOTER DA SIDEBAR - ✅ COM FOTO DINÂMICA
        ============================================================ -->
        <div class="sidebar-footer">
            <div class="avatar" id="sidebarAvatarContainer">
                @php
                    $user = auth()->user();
                    $fotoNome = $user->foto ?? null;
                    $fotoUrl = $fotoNome ? route('imagem.foto', ['filename' => $fotoNome]) : null;
                    $inicial = substr($user->nome ?? '?', 0, 1);
                @endphp
                
                @if($fotoUrl)
                    <img src="{{ $fotoUrl }}" 
                         alt="Foto" 
                         id="sidebarAvatar"
                         onerror="this.style.display='none'; this.parentElement.textContent='{{ $inicial }}';">
                @else
                    {{ $inicial }}
                @endif
            </div>
            <div class="user-info">
                <div class="name">{{ auth()->user()->nome ?? 'Visitante' }}</div>
                <div class="role">
                    {{ auth()->user()->funcao ?? 'Membro' }}
                    @if(auth()->user()->isAdmin())
                        <span class="badge-nivel badge-nivel-admin">Admin</span>
                    @elseif(auth()->user()->isSecretario())
                        <span class="badge-nivel badge-nivel-secretario">Secretário</span>
                    @endif
                </div>
            </div>
            
            <!-- ✅ LOGOUT CORRIGIDO - USANDO FORMULÁRIO POST -->
            <form action="{{ route('logout') }}" method="POST" class="logout-form">
                @csrf
                <button type="submit" class="logout-btn" title="Sair">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </form>
        </div>
    </aside>
    @endauth

    <!-- ============================================================
    MAIN CONTENT
    ============================================================ -->
    <main class="main-content">
        <!-- TOP BAR - ✅ MELHORADO: Usa Auth -->
        @auth
        <div class="top-bar">
            <div class="page-title">
                <h2>@yield('page-title', 'Dashboard')</h2>
                <p>@yield('page-subtitle', 'Bem-vindo à sua comunidade cristã')</p>
            </div>
            <div class="actions">
                <button class="theme-toggle" onclick="toggleTheme()" id="themeToggle">
                    <i class="fas fa-moon" id="themeIcon"></i>
                </button>
                <span style="font-size: 0.85rem; color: var(--text-secondary);">
                    <i class="fas fa-user-circle"></i> {{ auth()->user()->nome }}
                    @if(auth()->user()->isAdmin())
                        <span style="color: #f44336; font-weight: 700;"> 👑</span>
                    @elseif(auth()->user()->isSecretario())
                        <span style="color: #ff9800; font-weight: 700;"> 📋</span>
                    @endif
                </span>
            </div>
        </div>
        @endauth

        <!-- TOASTS -->
        @if(session('success'))
            <div class="toast toast-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="toast toast-error">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        @if(session('info'))
            <div class="toast toast-info">
                <i class="fas fa-info-circle"></i> {{ session('info') }}
            </div>
        @endif

        <!-- CONTENT -->
        @yield('content')
    </main>

    <!-- ============================================================
    SCRIPTS - ✅ MELHORADO
    ============================================================ -->
    <script>
        // ===== TEMA CLARO/ESCURO =====
        function toggleTheme() {
            document.body.classList.toggle('dark');
            const icon = document.getElementById('themeIcon');
            icon.classList.toggle('fa-moon');
            icon.classList.toggle('fa-sun');
            localStorage.setItem('theme', document.body.classList.contains('dark') ? 'dark' : 'light');
        }

        // Carregar tema salvo
        if (localStorage.getItem('theme') === 'dark') {
            document.body.classList.add('dark');
            const icon = document.getElementById('themeIcon');
            if (icon) icon.classList.replace('fa-moon', 'fa-sun');
        }

        // ===== SIDEBAR MOBILE =====
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('sidebarToggle');
        const overlay = document.getElementById('sidebarOverlay');

        function toggleSidebar() {
            sidebar?.classList.toggle('open');
            overlay?.classList.toggle('open');
        }

        if (toggleBtn) {
            toggleBtn.addEventListener('click', toggleSidebar);
        }

        if (overlay) {
            overlay.addEventListener('click', toggleSidebar);
        }

        // Fechar sidebar ao redimensionar para desktop
        window.addEventListener('resize', () => {
            if (window.innerWidth > 992) {
                sidebar?.classList.remove('open');
                overlay?.classList.remove('open');
            }
        });

        // ===== AUTO-FECHAR TOASTS =====
        document.querySelectorAll('.toast').forEach(toast => {
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-20px)';
                setTimeout(() => toast.remove(), 400);
            }, 5000);
        });

        // ===== CONFIRMAR LOGOUT =====
        document.querySelectorAll('.logout-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (!confirm('Tem certeza que deseja sair?')) {
                    e.preventDefault();
                }
            });
        });

        // ============================================================
        // ⭐ SISTEMA GLOBAL DE ATUALIZAÇÃO DE FOTO
        // ============================================================
        
        const FotoEvent = {
            atualizada: function(fotoUrl) {
                document.dispatchEvent(new CustomEvent('fotoAtualizada', {
                    detail: { fotoUrl: fotoUrl }
                }));
            },
            removida: function() {
                document.dispatchEvent(new CustomEvent('fotoRemovida'));
            }
        };

        window.FotoEvent = FotoEvent;

        function atualizarTodasFotos(url) {
            const timestamp = new Date().getTime();
            const urlComTimestamp = url + '?t=' + timestamp;
            
            // Atualiza todas as imagens de perfil
            document.querySelectorAll('img[id="fotoPerfil"], img[id="fotoPreview"], #sidebarAvatar, .avatar img, .profile-avatar img, .member-avatar img, .foto-membro img').forEach(img => {
                img.src = urlComTimestamp;
                img.style.display = 'block';
                img.onerror = function() {
                    this.style.display = 'none';
                    const placeholder = this.parentElement?.querySelector('.placeholder, .avatar-placeholder');
                    if (placeholder) {
                        placeholder.style.display = 'flex';
                    }
                };
            });
            
            // Oculta placeholders
            document.querySelectorAll('.placeholder, .avatar-placeholder').forEach(el => {
                el.style.display = 'none';
            });
            
            console.log('📸 Fotos atualizadas globalmente:', url);
        }

        function removerTodasFotos() {
            document.querySelectorAll('img[id="fotoPerfil"], img[id="fotoPreview"], #sidebarAvatar, .avatar img, .profile-avatar img, .member-avatar img, .foto-membro img').forEach(img => {
                img.remove();
            });
            
            document.querySelectorAll('.placeholder, .avatar-placeholder').forEach(el => {
                el.style.display = 'flex';
            });
            
            console.log('🗑️ Fotos removidas globalmente');
        }

        window.atualizarTodasFotos = atualizarTodasFotos;
        window.removerTodasFotos = removerTodasFotos;

        document.addEventListener('fotoAtualizada', function(e) {
            atualizarTodasFotos(e.detail.fotoUrl);
        });

        document.addEventListener('fotoRemovida', function() {
            removerTodasFotos();
        });

        console.log('🔄 Sistema global de atualização de fotos carregado');
        console.log('🕊️ Conexão Igreja - Rede Social Cristã');
        
        @auth
            console.log('👤 Logado como: {{ auth()->user()->nome }}');
            console.log('📋 Matrícula: {{ auth()->user()->matricula }}');
            @if(auth()->user()->isAdmin())
                console.log('🔐 Acesso administrativo detectado');
                console.log('👑 Nível: ADMIN');
            @elseif(auth()->user()->isSecretario())
                console.log('📋 Nível: SECRETÁRIO');
            @else
                console.log('👤 Nível: USUÁRIO');
            @endif
        @endauth
    </script>

    @stack('scripts')
</body>
</html>
EOF