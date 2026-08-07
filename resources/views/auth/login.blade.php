<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - ADTC2 MARANGUAPE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* ============================================================
           RESET E BASE
           ============================================================ */
        * { 
            margin: 0; 
            padding: 0; 
            box-sizing: border-box; 
        }
        
        html, body {
            width: 100%;
            min-height: 100vh;
            min-height: 100dvh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            display: flex;
            justify-content: center;
            align-items: center;
            padding: clamp(8px, 2vh, 20px);
            position: relative;
            overflow: hidden;
        }
        
        /* Efeito de fundo */
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle at 30% 50%, rgba(212, 175, 55, 0.05) 0%, transparent 50%),
                        radial-gradient(circle at 70% 50%, rgba(212, 175, 55, 0.03) 0%, transparent 50%);
            animation: rotate 20s linear infinite;
        }
        
        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* ============================================================
           CONTAINER PRINCIPAL - RESPONSIVO
           ============================================================ */
        .login-container {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: clamp(16px, 2.5vh, 24px);
            padding: clamp(16px, 3vh, 45px) clamp(14px, 3vw, 40px);
            max-width: 420px;
            width: 100%;
            max-height: 98vh;
            max-height: 98dvh;
            overflow-y: auto;
            border: 1px solid rgba(212, 175, 55, 0.15);
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
            position: relative;
            z-index: 1;
        }
        
        /* Scroll suave no container */
        .login-container::-webkit-scrollbar { width: 3px; }
        .login-container::-webkit-scrollbar-thumb { background: rgba(212, 175, 55, 0.3); border-radius: 10px; }
        .login-container::-webkit-scrollbar-track { background: transparent; }
        
        /* ============================================================
           HEADER
           ============================================================ */
        .login-header {
            text-align: center;
            margin-bottom: clamp(12px, 2.5vh, 35px);
        }
        
        /* ===== LOGO ===== */
        .logo-igreja {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: clamp(4px, 1vh, 12px);
            margin-bottom: clamp(6px, 1.2vh, 15px);
        }
        
        .logo-igreja .cross-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: clamp(50px, 9vh, 80px);
            height: clamp(50px, 9vh, 80px);
            flex-shrink: 0;
            overflow: hidden;
            border-radius: 50%;
            background: rgba(212, 175, 55, 0.1);
            border: 2px solid rgba(212, 175, 55, 0.2);
            padding: clamp(6px, 1.2vh, 12px);
            transition: all 0.3s ease;
        }
        
        .logo-igreja .cross-icon:hover {
            transform: scale(1.05);
            border-color: rgba(212, 175, 55, 0.4);
            box-shadow: 0 0 30px rgba(212, 175, 55, 0.1);
        }
        
        .logo-igreja .cross-icon img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }
        
        .logo-igreja .cross-icon .logo-fallback {
            display: none;
            width: 100%;
            height: 100%;
        }
        
        .logo-igreja .cross-icon .logo-fallback svg {
            width: 100%;
            height: 100%;
        }
        
        .logo-igreja .logo-texto {
            text-align: center;
            line-height: 1.2;
        }
        
        .logo-igreja .logo-texto .logo-nome {
            font-size: clamp(0.9rem, 2.2vh, 1.4rem);
            font-weight: 800;
            letter-spacing: clamp(1px, 0.3vw, 3px);
            background: linear-gradient(180deg, #f2d680, #d4af37, #b8960f);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 0 30px rgba(212, 175, 55, 0.1);
        }
        
        .logo-igreja .logo-texto .logo-sub {
            font-size: clamp(0.4rem, 1vh, 0.65rem);
            font-weight: 600;
            letter-spacing: clamp(2px, 0.3vw, 4px);
            color: rgba(255, 255, 255, 0.4);
            text-transform: uppercase;
        }
        
        .login-header .slogan {
            color: rgba(255,255,255,0.35);
            font-size: clamp(0.6rem, 1.3vh, 0.85rem);
            font-weight: 300;
            letter-spacing: clamp(1px, 0.2vw, 2px);
            margin-top: clamp(2px, 0.5vh, 5px);
        }
        
        .divider {
            width: clamp(40px, 8vw, 60px);
            height: 2px;
            background: linear-gradient(90deg, transparent, rgba(212, 175, 55, 0.3), transparent);
            margin: clamp(6px, 1.2vh, 15px) auto;
        }
        
        /* ============================================================
           FORMULÁRIO
           ============================================================ */
        .form-group { 
            margin-bottom: clamp(10px, 1.8vh, 20px); 
        }
        
        .form-group label {
            display: block;
            color: rgba(255,255,255,0.6);
            font-size: clamp(0.65rem, 1.2vh, 0.8rem);
            margin-bottom: 4px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        
        .form-group label i {
            color: #d4af37;
            margin-right: 6px;
            width: 16px;
        }
        
        .form-group input {
            width: 100%;
            padding: clamp(9px, 1.6vh, 13px) clamp(10px, 1.8vw, 16px);
            border: 2px solid rgba(255,255,255,0.08);
            border-radius: clamp(10px, 1.8vh, 12px);
            background: rgba(255,255,255,0.06);
            color: white;
            font-size: clamp(0.8rem, 1.4vh, 1rem);
            transition: all 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #d4af37;
            background: rgba(255,255,255,0.1);
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.05);
        }
        
        .form-group input::placeholder { 
            color: rgba(255,255,255,0.25);
            font-weight: 300;
            font-size: clamp(0.7rem, 1.2vh, 0.85rem);
        }
        
        .form-group input.is-invalid {
            border-color: #f44336;
            background: rgba(244, 67, 54, 0.08);
        }
        
        .form-group .error-text {
            color: #f44336;
            font-size: clamp(0.6rem, 1vh, 0.75rem);
            margin-top: 3px;
            display: block;
        }
        
        /* ============================================================
           LEMBRAR-ME
           ============================================================ */
        .remember-group {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: clamp(10px, 1.8vh, 20px);
        }
        
        .remember-group input[type="checkbox"] {
            width: clamp(14px, 2.2vh, 18px);
            height: clamp(14px, 2.2vh, 18px);
            accent-color: #d4af37;
            cursor: pointer;
            border-radius: 4px;
            flex-shrink: 0;
        }
        
        .remember-group label {
            color: rgba(255,255,255,0.5);
            font-size: clamp(0.7rem, 1.2vh, 0.85rem);
            cursor: pointer;
            font-weight: 400;
        }
        
        /* ============================================================
           BOTÃO
           ============================================================ */
        .btn-login {
            width: 100%;
            padding: clamp(11px, 1.8vh, 15px);
            background: linear-gradient(135deg, #d4af37, #c9a84c);
            border: none;
            border-radius: clamp(10px, 1.8vh, 12px);
            color: #1a1a2e;
            font-size: clamp(0.9rem, 1.6vh, 1.1rem);
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            letter-spacing: 0.5px;
            position: relative;
            overflow: hidden;
        }
        
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .btn-login:hover::before {
            left: 100%;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(212, 175, 55, 0.3);
        }
        
        .btn-login:active {
            transform: scale(0.98);
        }
        
        .btn-login i {
            margin-right: 8px;
        }
        
        /* ============================================================
           LINK DE REGISTRO
           ============================================================ */
        .register-link {
            text-align: center;
            color: rgba(255,255,255,0.35);
            font-size: clamp(0.7rem, 1.2vh, 0.9rem);
            margin-top: clamp(12px, 2vh, 20px);
            padding-top: clamp(12px, 2vh, 20px);
            border-top: 1px solid rgba(255,255,255,0.05);
        }
        
        .register-link a {
            color: #d4af37;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .register-link a:hover { 
            color: #f2d680;
            text-decoration: underline; 
        }
        
        .register-link i {
            margin-right: 6px;
        }
        
        /* ============================================================
           MENSAGENS DE ALERTA
           ============================================================ */
        .alert {
            padding: clamp(8px, 1.2vh, 12px) clamp(10px, 1.5vw, 16px);
            border-radius: clamp(8px, 1.5vh, 10px);
            margin-bottom: clamp(10px, 1.8vh, 20px);
            display: flex;
            align-items: flex-start;
            gap: 8px;
            font-size: clamp(0.75rem, 1.2vh, 0.9rem);
        }
        
        .alert i {
            margin-top: 2px;
            flex-shrink: 0;
        }
        
        .alert-error {
            background: rgba(244, 67, 54, 0.12);
            border: 1px solid rgba(244, 67, 54, 0.3);
            color: #f44336;
        }
        
        .alert-success {
            background: rgba(76, 175, 80, 0.12);
            border: 1px solid rgba(76, 175, 80, 0.3);
            color: #4caf50;
        }
        
        .alert-info {
            background: rgba(33, 150, 243, 0.12);
            border: 1px solid rgba(33, 150, 243, 0.3);
            color: #2196f3;
        }

        /* ============================================================
           ORIENTAÇÃO PAISAGEM (Landscape)
           ============================================================ */
        @media (max-height: 500px) and (orientation: landscape) {
            body {
                padding: 6px 16px;
                align-items: flex-start;
                padding-top: 8px;
            }
            
            .login-container {
                max-height: 96vh;
                max-height: 96dvh;
                padding: clamp(8px, 1.5vh, 14px) clamp(16px, 4vw, 30px);
                border-radius: 14px;
                overflow-y: auto;
            }
            
            .logo-igreja {
                flex-direction: row;
                gap: 10px;
                margin-bottom: 4px;
            }
            
            .logo-igreja .cross-icon {
                width: clamp(32px, 5vh, 45px);
                height: clamp(32px, 5vh, 45px);
                padding: clamp(4px, 0.6vh, 6px);
            }
            
            .logo-igreja .logo-texto .logo-nome {
                font-size: clamp(0.7rem, 1.6vh, 0.95rem);
                letter-spacing: 1px;
            }
            
            .logo-igreja .logo-texto .logo-sub {
                font-size: clamp(0.3rem, 0.6vh, 0.45rem);
                letter-spacing: 1px;
            }
            
            .login-header .slogan {
                display: none;
            }
            
            .divider {
                display: none;
            }
            
            .form-group {
                margin-bottom: clamp(4px, 0.8vh, 8px);
            }
            
            .form-group input {
                padding: clamp(5px, 0.8vh, 8px) clamp(8px, 1.2vw, 12px);
                font-size: clamp(0.7rem, 1vh, 0.8rem);
                border-radius: 8px;
            }
            
            .form-group label {
                font-size: clamp(0.55rem, 0.8vh, 0.65rem);
                margin-bottom: 2px;
            }
            
            .btn-login {
                padding: clamp(6px, 1vh, 10px);
                font-size: clamp(0.75rem, 1.2vh, 0.85rem);
                border-radius: 8px;
            }
            
            .remember-group {
                margin-bottom: clamp(4px, 0.8vh, 8px);
                gap: 6px;
            }
            
            .remember-group input[type="checkbox"] {
                width: clamp(12px, 1.8vh, 16px);
                height: clamp(12px, 1.8vh, 16px);
            }
            
            .remember-group label {
                font-size: clamp(0.6rem, 0.9vh, 0.75rem);
            }
            
            .register-link {
                margin-top: clamp(6px, 1vh, 10px);
                padding-top: clamp(6px, 1vh, 10px);
                font-size: clamp(0.55rem, 0.8vh, 0.7rem);
            }
            
            .login-header {
                margin-bottom: clamp(6px, 1vh, 12px);
            }
            
            .alert {
                padding: clamp(4px, 0.6vh, 6px) clamp(6px, 0.8vw, 10px);
                font-size: clamp(0.55rem, 0.7vh, 0.7rem);
                margin-bottom: clamp(4px, 0.8vh, 8px);
                border-radius: 6px;
            }
        }

        /* ============================================================
           TELAS MUITO PEQUENAS (ATÉ 360px)
           ============================================================ */
        @media (max-width: 360px) {
            body {
                padding: 6px;
            }
            
            .login-container {
                padding: clamp(10px, 2vh, 16px) clamp(8px, 1.5vw, 12px);
                border-radius: 14px;
                max-height: 99vh;
                max-height: 99dvh;
            }
            
            .logo-igreja .cross-icon {
                width: clamp(38px, 7vh, 48px);
                height: clamp(38px, 7vh, 48px);
                padding: clamp(4px, 0.6vh, 6px);
            }
            
            .logo-igreja .logo-texto .logo-nome {
                font-size: clamp(0.7rem, 1.8vh, 0.9rem);
                letter-spacing: 0.5px;
            }
            
            .logo-igreja .logo-texto .logo-sub {
                font-size: clamp(0.3rem, 0.6vh, 0.4rem);
                letter-spacing: 1px;
            }
            
            .login-header .slogan {
                font-size: clamp(0.5rem, 0.9vh, 0.6rem);
            }
            
            .form-group input {
                padding: clamp(6px, 1vh, 8px) clamp(6px, 1vw, 10px);
                font-size: clamp(0.7rem, 1.1vh, 0.8rem);
            }
            
            .btn-login {
                font-size: clamp(0.75rem, 1.2vh, 0.85rem);
                padding: clamp(8px, 1.2vh, 10px);
            }
        }

        /* ============================================================
           PREFERE DARK MODE
           ============================================================ */
        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(135deg, #0a0a1a, #1a1a2e, #0f0c29);
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="logo-igreja">
                <div class="cross-icon">
                    <img src="/sistemas/conexao-igreja/public/imagens/logo-branco.png" 
                         alt="Logo ADTC2 MARANGUAPE"
                         loading="lazy"
                         onerror="this.style.display='none'; this.parentElement.querySelector('.logo-fallback').style.display='flex';">
                    <div class="logo-fallback">
                        <svg viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="17" y="2" width="16" height="46" rx="3" fill="url(#goldGrad)"/>
                            <rect x="2" y="17" width="46" height="16" rx="3" fill="url(#goldGrad)"/>
                            <defs>
                                <linearGradient id="goldGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#f2d680"/>
                                    <stop offset="50%" stop-color="#d4af37"/>
                                    <stop offset="100%" stop-color="#b8960f"/>
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                </div>
                <div class="logo-texto">
                    <div class="logo-nome">ADTC2 MARANGUAPE</div>
                    <div class="logo-sub">Ministério Templo Central</div>
                </div>
            </div>
            <div class="divider"></div>
            <p class="slogan">Conecte-se com sua comunidade</p>
        </div>

        <!-- ⭐ MENSAGENS DE SESSÃO -->
        @if(session('error'))
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
        @endif

        @if(session('info'))
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> {{ session('info') }}
            </div>
        @endif

        <!-- ⭐ ERROS DE VALIDAÇÃO DO LARAVEL -->
        @if($errors->any())
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i>
                <div>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="form-group">
                <label><i class="fas fa-id-card"></i> Matrícula</label>
                <input 
                    type="number" 
                    name="matricula" 
                    placeholder="Digite sua matrícula" 
                    value="{{ old('matricula') }}" 
                    required 
                    autofocus
                    class="{{ $errors->has('matricula') ? 'is-invalid' : '' }}"
                >
                @error('matricula')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Senha</label>
                <input 
                    type="password" 
                    name="password" 
                    placeholder="Digite sua senha" 
                    required
                    class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                >
                @error('password')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <!-- ⭐ CAMPO LEMBRAR-ME -->
            <div class="remember-group">
                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label for="remember">Lembrar-me</label>
            </div>

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt"></i> Entrar
            </button>
        </form>

        <div class="register-link">
            <a href="{{ route('register') }}"><i class="fas fa-user-plus"></i> Criar minha conta</a>
        </div>
    </div>
</body>
</html>