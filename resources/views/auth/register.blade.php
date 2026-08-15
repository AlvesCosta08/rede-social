<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Cadastro - ADTC2 MARANGUAPE</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <!-- Vite - CSS e JS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
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
        .register-container {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-radius: clamp(16px, 2.5vh, 24px);
            padding: clamp(16px, 3vh, 45px) clamp(14px, 3vw, 40px);
            max-width: 520px;
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
        .register-container::-webkit-scrollbar { width: 3px; }
        .register-container::-webkit-scrollbar-thumb { background: rgba(212, 175, 55, 0.3); border-radius: 10px; }
        .register-container::-webkit-scrollbar-track { background: transparent; }
        
        /* ============================================================
           HEADER
           ============================================================ */
        .register-header {
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
        
        .logo-igreja .cross-icon .fallback-icon {
            font-size: 2.5rem;
            color: #d4af37;
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
        
        .register-header .slogan {
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
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: clamp(8px, 1.2vw, 16px);
        }
        
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
        
        .form-group label .required {
            color: #f44336;
            margin-left: 2px;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: clamp(9px, 1.6vh, 13px) clamp(10px, 1.8vw, 16px);
            border: 2px solid rgba(255,255,255,0.08);
            border-radius: clamp(10px, 1.8vh, 12px);
            background: rgba(255,255,255,0.06);
            color: white;
            font-size: clamp(0.8rem, 1.4vh, 1rem);
            transition: all 0.3s ease;
        }
        
        .form-group select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='rgba(255,255,255,0.4)' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 36px;
            cursor: pointer;
        }
        
        .form-group select option {
            background: #1a1a2e;
            color: white;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 60px;
            font-family: inherit;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #d4af37;
            background: rgba(255,255,255,0.1);
            box-shadow: 0 0 20px rgba(212, 175, 55, 0.05);
        }
        
        .form-group input::placeholder,
        .form-group textarea::placeholder { 
            color: rgba(255,255,255,0.25);
            font-weight: 300;
            font-size: clamp(0.7rem, 1.2vh, 0.85rem);
        }
        
        .form-group input.is-invalid,
        .form-group select.is-invalid,
        .form-group textarea.is-invalid {
            border-color: #f44336;
            background: rgba(244, 67, 54, 0.08);
        }
        
        .form-group .error-text {
            color: #f44336;
            font-size: clamp(0.6rem, 1vh, 0.75rem);
            margin-top: 3px;
            display: block;
        }
        
        .form-group .help-text {
            color: rgba(255,255,255,0.3);
            font-size: clamp(0.55rem, 0.9vh, 0.7rem);
            margin-top: 3px;
            display: block;
        }
        
        /* ============================================================
           TERMOS E CHECKBOX
           ============================================================ */
        .terms-group {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin-bottom: clamp(10px, 1.8vh, 20px);
        }
        
        .terms-group input[type="checkbox"] {
            width: clamp(16px, 2.2vh, 20px);
            height: clamp(16px, 2.2vh, 20px);
            accent-color: #d4af37;
            cursor: pointer;
            border-radius: 4px;
            flex-shrink: 0;
            margin-top: 1px;
        }
        
        .terms-group label {
            color: rgba(255,255,255,0.5);
            font-size: clamp(0.7rem, 1.2vh, 0.85rem);
            cursor: pointer;
            font-weight: 400;
        }
        
        .terms-group label a {
            color: #d4af37;
            text-decoration: none;
        }
        
        .terms-group label a:hover {
            text-decoration: underline;
        }
        
        .terms-group.is-invalid label {
            color: #f44336;
        }
        
        /* ============================================================
           BOTÃO
           ============================================================ */
        .btn-register {
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
        
        .btn-register::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .btn-register:hover::before {
            left: 100%;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(212, 175, 55, 0.3);
        }
        
        .btn-register:active {
            transform: scale(0.98);
        }
        
        .btn-register i {
            margin-right: 8px;
        }
        
        /* ============================================================
           LINK DE LOGIN
           ============================================================ */
        .login-link {
            text-align: center;
            color: rgba(255,255,255,0.35);
            font-size: clamp(0.7rem, 1.2vh, 0.9rem);
            margin-top: clamp(12px, 2vh, 20px);
            padding-top: clamp(12px, 2vh, 20px);
            border-top: 1px solid rgba(255,255,255,0.05);
        }
        
        .login-link a {
            color: #d4af37;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }
        
        .login-link a:hover { 
            color: #f2d680;
            text-decoration: underline; 
        }
        
        .login-link i {
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
        
        .alert-warning {
            background: rgba(255, 152, 0, 0.12);
            border: 1px solid rgba(255, 152, 0, 0.3);
            color: #ff9800;
        }
        
        /* ============================================================
           FEEDBACK DE VALIDAÇÃO EM TEMPO REAL
           ============================================================ */
        .feedback-success {
            color: #4caf50;
            font-size: clamp(0.6rem, 1vh, 0.75rem);
            margin-top: 3px;
            display: block;
        }
        
        .feedback-error {
            color: #f44336;
            font-size: clamp(0.6rem, 1vh, 0.75rem);
            margin-top: 3px;
            display: block;
        }
        
        .feedback-warning {
            color: #ff9800;
            font-size: clamp(0.6rem, 1vh, 0.75rem);
            margin-top: 3px;
            display: block;
        }
        
        .hidden {
            display: none !important;
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
            
            .register-container {
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
            
            .register-header .slogan {
                display: none;
            }
            
            .divider {
                display: none;
            }
            
            .form-row {
                grid-template-columns: 1fr 1fr;
                gap: clamp(4px, 0.6vw, 8px);
            }
            
            .form-group {
                margin-bottom: clamp(4px, 0.8vh, 8px);
            }
            
            .form-group input,
            .form-group select,
            .form-group textarea {
                padding: clamp(5px, 0.8vh, 8px) clamp(8px, 1.2vw, 12px);
                font-size: clamp(0.7rem, 1vh, 0.8rem);
                border-radius: 8px;
            }
            
            .form-group label {
                font-size: clamp(0.55rem, 0.8vh, 0.65rem);
                margin-bottom: 2px;
            }
            
            .btn-register {
                padding: clamp(6px, 1vh, 10px);
                font-size: clamp(0.75rem, 1.2vh, 0.85rem);
                border-radius: 8px;
            }
            
            .terms-group {
                margin-bottom: clamp(4px, 0.8vh, 8px);
                gap: 6px;
            }
            
            .terms-group input[type="checkbox"] {
                width: clamp(12px, 1.8vh, 16px);
                height: clamp(12px, 1.8vh, 16px);
            }
            
            .terms-group label {
                font-size: clamp(0.6rem, 0.9vh, 0.75rem);
            }
            
            .login-link {
                margin-top: clamp(6px, 1vh, 10px);
                padding-top: clamp(6px, 1vh, 10px);
                font-size: clamp(0.55rem, 0.8vh, 0.7rem);
            }
            
            .register-header {
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
            
            .register-container {
                padding: clamp(10px, 2vh, 16px) clamp(8px, 1.5vw, 12px);
                border-radius: 14px;
                max-height: 99vh;
                max-height: 99dvh;
            }
            
            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
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
            
            .register-header .slogan {
                font-size: clamp(0.5rem, 0.9vh, 0.6rem);
            }
            
            .form-group input,
            .form-group select,
            .form-group textarea {
                padding: clamp(6px, 1vh, 8px) clamp(6px, 1vw, 10px);
                font-size: clamp(0.7rem, 1.1vh, 0.8rem);
            }
            
            .btn-register {
                font-size: clamp(0.75rem, 1.2vh, 0.85rem);
                padding: clamp(8px, 1.2vh, 10px);
            }
        }

        /* ============================================================
           TELAS MÉDIAS (TABLETS)
           ============================================================ */
        @media (min-width: 768px) and (max-width: 1024px) {
            .register-container {
                max-width: 560px;
                padding: clamp(24px, 4vh, 40px) clamp(24px, 4vw, 48px);
            }
            
            .form-row {
                grid-template-columns: 1fr 1fr;
                gap: 16px;
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
    <div class="register-container">
        <div class="register-header">
            <div class="logo-igreja">
                <div class="cross-icon" id="logoContainer">
                    <!-- ⭐ IMAGEM CORRIGIDA COM FALLBACK -->
                    @php
                        $logoPath = public_path('imagens/logo-branco.png');
                        $logoExists = file_exists($logoPath);
                    @endphp
                    
                    @if($logoExists)
                        <img id="logoImg"
                             src="{{ asset('imagens/logo-branco.png') }}"
                             alt="Logo ADTC2 MARANGUAPE"
                             loading="lazy"
                             onerror="this.onerror=null; this.src='{{ asset('imagens/logo-branco.svg') }}';"
                             onerror="this.style.display='none'; document.getElementById('logoContainer').innerHTML='<span class=\'fallback-icon\'>✝</span>';">
                    @else
                        <span class="fallback-icon">✝</span>
                    @endif
                </div>
                <div class="logo-texto">
                    <div class="logo-nome">ADTC2 MARANGUAPE</div>
                    <div class="logo-sub">Ministério Templo Central</div>
                </div>
            </div>
            <div class="divider"></div>
            <p class="slogan">Crie sua conta e conecte-se à comunidade</p>
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

        <form method="POST" action="{{ route('register.post') }}" id="registerForm">
            @csrf

            <!-- ⭐ NOME -->
            <div class="form-group">
                <label><i class="fas fa-user"></i> Nome Completo <span class="required">*</span></label>
                <input 
                    type="text" 
                    name="nome" 
                    placeholder="Digite seu nome completo" 
                    value="{{ old('nome') }}" 
                    required 
                    autofocus
                    class="{{ $errors->has('nome') ? 'is-invalid' : '' }}"
                >
                @error('nome')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <!-- ⭐ EMAIL -->
            <div class="form-group">
                <label><i class="fas fa-envelope"></i> E-mail <span class="required">*</span></label>
                <input 
                    type="email" 
                    name="email" 
                    id="email"
                    placeholder="Digite seu e-mail" 
                    value="{{ old('email') }}" 
                    required
                    class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                >
                <div id="emailFeedback"></div>
                @error('email')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <!-- ⭐ TELEFONE -->
            <div class="form-group">
                <label><i class="fas fa-phone"></i> Telefone <span class="required">*</span></label>
                <input 
                    type="text" 
                    name="telefone" 
                    id="telefone"
                    placeholder="(00) 00000-0000" 
                    value="{{ old('telefone') }}" 
                    required
                    class="{{ $errors->has('telefone') ? 'is-invalid' : '' }}"
                >
                @error('telefone')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <!-- ⭐ TELEFONE 2 -->
            <div class="form-group">
                <label><i class="fas fa-phone-alt"></i> Telefone 2</label>
                <input 
                    type="text" 
                    name="telefone2" 
                    id="telefone2"
                    placeholder="(00) 00000-0000" 
                    value="{{ old('telefone2') }}"
                    class="{{ $errors->has('telefone2') ? 'is-invalid' : '' }}"
                >
                @error('telefone2')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <!-- ⭐ DOCUMENTO -->
            <div class="form-group">
                <label><i class="fas fa-id-card"></i> CPF/RG <span class="required">*</span></label>
                <input 
                    type="text" 
                    name="documento" 
                    id="documento"
                    placeholder="000.000.000-00" 
                    value="{{ old('documento') }}" 
                    required
                    class="{{ $errors->has('documento') ? 'is-invalid' : '' }}"
                >
                @error('documento')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <!-- ⭐ DATA NASCIMENTO -->
            <div class="form-group">
                <label><i class="fas fa-calendar-alt"></i> Data de Nascimento <span class="required">*</span></label>
                <input 
                    type="date" 
                    name="dataNascimento" 
                    value="{{ old('dataNascimento') }}" 
                    required
                    class="{{ $errors->has('dataNascimento') ? 'is-invalid' : '' }}"
                >
                @error('dataNascimento')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <!-- ⭐ DATA BATISMO -->
            <div class="form-group">
                <label><i class="fas fa-water"></i> Data de Batismo</label>
                <input 
                    type="date" 
                    name="dataBatismo" 
                    value="{{ old('dataBatismo') }}"
                    class="{{ $errors->has('dataBatismo') ? 'is-invalid' : '' }}"
                >
                @error('dataBatismo')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <!-- ⭐ ESTADO CIVIL -->
            <div class="form-group">
                <label><i class="fas fa-ring"></i> Estado Civil</label>
                <select 
                    name="estadoCivil" 
                    id="estadoCivil"
                    class="form-select {{ $errors->has('estadoCivil') ? 'is-invalid' : '' }}"
                >
                    <option value="">Selecione</option>
                    <option value="casado" {{ old('estadoCivil') == 'casado' ? 'selected' : '' }}>Casado(a)</option>
                    <option value="solteiro" {{ old('estadoCivil') == 'solteiro' ? 'selected' : '' }}>Solteiro(a)</option>
                    <option value="divorciado" {{ old('estadoCivil') == 'divorciado' ? 'selected' : '' }}>Divorciado(a)</option>
                    <option value="viuvo" {{ old('estadoCivil') == 'viuvo' ? 'selected' : '' }}>Viúvo(a)</option>
                    <option value="separado" {{ old('estadoCivil') == 'separado' ? 'selected' : '' }}>Separado(a)</option>
                </select>
                @error('estadoCivil')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <!-- ⭐ ENDEREÇO -->
            <div class="form-group">
                <label><i class="fas fa-home"></i> Endereço <span class="required">*</span></label>
                <input 
                    type="text" 
                    name="endereco" 
                    placeholder="Rua, Avenida..." 
                    value="{{ old('endereco') }}" 
                    required
                    class="{{ $errors->has('endereco') ? 'is-invalid' : '' }}"
                >
                @error('endereco')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <!-- ⭐ NÚMERO -->
            <div class="form-group">
                <label><i class="fas fa-hashtag"></i> Número</label>
                <input 
                    type="text" 
                    name="numero" 
                    placeholder="Número da casa" 
                    value="{{ old('numero') }}"
                    class="{{ $errors->has('numero') ? 'is-invalid' : '' }}"
                >
                @error('numero')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <!-- ⭐ BAIRRO -->
            <div class="form-group">
                <label><i class="fas fa-map-pin"></i> Bairro</label>
                <input 
                    type="text" 
                    name="bairro" 
                    placeholder="Seu bairro" 
                    value="{{ old('bairro') }}"
                    class="{{ $errors->has('bairro') ? 'is-invalid' : '' }}"
                >
                @error('bairro')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <!-- ⭐ CEP -->
            <div class="form-group">
                <label><i class="fas fa-mailbox"></i> CEP</label>
                <input 
                    type="text" 
                    name="cep" 
                    id="cep"
                    placeholder="00000-000" 
                    value="{{ old('cep') }}"
                    class="{{ $errors->has('cep') ? 'is-invalid' : '' }}"
                >
                @error('cep')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <!-- ⭐ CIDADE E UF (ROW) -->
            <div class="form-row">
                <div class="form-group">
                    <label><i class="fas fa-city"></i> Cidade <span class="required">*</span></label>
                    <input 
                        type="text" 
                        name="cidade" 
                        placeholder="Sua cidade" 
                        value="{{ old('cidade') }}" 
                        required
                        class="{{ $errors->has('cidade') ? 'is-invalid' : '' }}"
                    >
                    @error('cidade')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label><i class="fas fa-map-marker-alt"></i> UF <span class="required">*</span></label>
                    <select 
                        name="uf" 
                        required
                        class="{{ $errors->has('uf') ? 'is-invalid' : '' }}"
                    >
                        <option value="">Selecione</option>
                        <option value="AC" {{ old('uf') == 'AC' ? 'selected' : '' }}>AC</option>
                        <option value="AL" {{ old('uf') == 'AL' ? 'selected' : '' }}>AL</option>
                        <option value="AP" {{ old('uf') == 'AP' ? 'selected' : '' }}>AP</option>
                        <option value="AM" {{ old('uf') == 'AM' ? 'selected' : '' }}>AM</option>
                        <option value="BA" {{ old('uf') == 'BA' ? 'selected' : '' }}>BA</option>
                        <option value="CE" {{ old('uf') == 'CE' ? 'selected' : '' }}>CE</option>
                        <option value="DF" {{ old('uf') == 'DF' ? 'selected' : '' }}>DF</option>
                        <option value="ES" {{ old('uf') == 'ES' ? 'selected' : '' }}>ES</option>
                        <option value="GO" {{ old('uf') == 'GO' ? 'selected' : '' }}>GO</option>
                        <option value="MA" {{ old('uf') == 'MA' ? 'selected' : '' }}>MA</option>
                        <option value="MT" {{ old('uf') == 'MT' ? 'selected' : '' }}>MT</option>
                        <option value="MS" {{ old('uf') == 'MS' ? 'selected' : '' }}>MS</option>
                        <option value="MG" {{ old('uf') == 'MG' ? 'selected' : '' }}>MG</option>
                        <option value="PA" {{ old('uf') == 'PA' ? 'selected' : '' }}>PA</option>
                        <option value="PB" {{ old('uf') == 'PB' ? 'selected' : '' }}>PB</option>
                        <option value="PR" {{ old('uf') == 'PR' ? 'selected' : '' }}>PR</option>
                        <option value="PE" {{ old('uf') == 'PE' ? 'selected' : '' }}>PE</option>
                        <option value="PI" {{ old('uf') == 'PI' ? 'selected' : '' }}>PI</option>
                        <option value="RJ" {{ old('uf') == 'RJ' ? 'selected' : '' }}>RJ</option>
                        <option value="RN" {{ old('uf') == 'RN' ? 'selected' : '' }}>RN</option>
                        <option value="RS" {{ old('uf') == 'RS' ? 'selected' : '' }}>RS</option>
                        <option value="RO" {{ old('uf') == 'RO' ? 'selected' : '' }}>RO</option>
                        <option value="RR" {{ old('uf') == 'RR' ? 'selected' : '' }}>RR</option>
                        <option value="SC" {{ old('uf') == 'SC' ? 'selected' : '' }}>SC</option>
                        <option value="SP" {{ old('uf') == 'SP' ? 'selected' : '' }}>SP</option>
                        <option value="SE" {{ old('uf') == 'SE' ? 'selected' : '' }}>SE</option>
                        <option value="TO" {{ old('uf') == 'TO' ? 'selected' : '' }}>TO</option>
                    </select>
                    @error('uf')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- ⭐ CONGREGAÇÃO -->
            <div class="form-group">
                <label><i class="fas fa-church"></i> Congregação <span class="required">*</span></label>
                <select 
                    name="congregacao" 
                    id="congregacao"
                    required
                    class="{{ $errors->has('congregacao') ? 'is-invalid' : '' }}"
                >
                    <option value="">Selecione sua congregação</option>
                    <?php
                        $congregacoes = [
                            'SEDE', 'ALEGRIA', 'JUBAIA', 'LAGES', 'NOVO MARANGUAPE 1',
                            'NOVO MARANGUAPE 2', 'NOVO MARANGUAPE 3', 'NOVO MARANGUAPE 4',
                            'OUTRA BANDA', 'PARQUE SÃO JOÃO', 'NOVO PARQUE IRACEMA',
                            'NOVO PARQUE IRACEMA 2', 'SITIO SÃO LUIZ', 'TABATINGA',
                            'UMARIZEIRAS', 'VITÓRIA', 'VIÇOSA', 'PAPARA', 'PLANALTO',
                            'SERRA JUBAIA', 'IRACEMA', 'PARAISO', 'CASTELO', 'LAMEIRÃO'
                        ];
                        foreach ($congregacoes as $congregacao) {
                            $selected = old('congregacao') == $congregacao ? 'selected' : '';
                            echo "<option value=\"$congregacao\" $selected>$congregacao</option>";
                        }
                    ?>
                </select>
                @error('congregacao')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

        <!-- ⭐ FUNÇÃO -->
        <div class="form-group">
            <label><i class="fas fa-user-tag"></i> Função/Cargo <span class="required">*</span></label>
            <select 
                name="funcao" 
                id="funcao"
                required
                class="{{ $errors->has('funcao') ? 'is-invalid' : '' }}"
            >
                <option value="">Selecione uma função</option>
                <option value="Membro" {{ old('funcao') == 'Membro' ? 'selected' : '' }}>Membro</option>
                <option value="Auxiliar" {{ old('funcao') == 'Auxiliar' ? 'selected' : '' }}>Auxiliar</option>
                <option value="Diacono" {{ old('funcao') == 'Diacono' ? 'selected' : '' }}>Diácono</option>
                <option value="Presbitero" {{ old('funcao') == 'Presbitero' ? 'selected' : '' }}>Presbítero</option>
                <option value="Evangelista" {{ old('funcao') == 'Evangelista' ? 'selected' : '' }}>Evangelista</option>
                <option value="Pastor" {{ old('funcao') == 'Pastor' ? 'selected' : '' }}>Pastor</option>
                <option value="Pastor-Presidente" {{ old('funcao') == 'Pastor-Presidente' ? 'selected' : '' }}>Pastor-Presidente</option>
                <option value="Pastor-Vice-Presidente" {{ old('funcao') == 'Pastor-Vice-Presidente' ? 'selected' : '' }}>Pastor-Vice-Presidente</option>
                <option value="Secretario" {{ old('funcao') == 'Secretario' ? 'selected' : '' }}>Secretário</option>
            </select>
            @error('funcao')
                <span class="error-text">{{ $message }}</span>
            @enderror
        </div>

            <!-- ⭐ DATA CONSAGRAÇÃO (oculto por padrão) -->
            <div class="form-group" id="dataConsagracaoGroup" style="display: none;">
                <label><i class="fas fa-hands-praying"></i> Data de Consagração</label>
                <input 
                    type="date" 
                    name="data_Consagracao" 
                    id="data_Consagracao"
                    value="{{ old('data_Consagracao') }}"
                    class="{{ $errors->has('data_Consagracao') ? 'is-invalid' : '' }}"
                >
                <span class="help-text">Obrigatório para cargos pastorais e de liderança.</span>
                @error('data_Consagracao')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <!-- ⭐ NOME CARTEIRA -->
            <div class="form-group">
                <label><i class="fas fa-id-badge"></i> Nome na Carteira</label>
                <input 
                    type="text" 
                    name="nome_carteira" 
                    placeholder="Como aparece na carteira de membro" 
                    value="{{ old('nome_carteira') }}"
                    class="{{ $errors->has('nome_carteira') ? 'is-invalid' : '' }}"
                >
                @error('nome_carteira')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <!-- ⭐ MÃE -->
            <div class="form-group">
                <label><i class="fas fa-female"></i> Nome da Mãe</label>
                <input 
                    type="text" 
                    name="mae" 
                    placeholder="Nome da sua mãe" 
                    value="{{ old('mae') }}"
                    class="{{ $errors->has('mae') ? 'is-invalid' : '' }}"
                >
                @error('mae')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <!-- ⭐ PAI -->
            <div class="form-group">
                <label><i class="fas fa-male"></i> Nome do Pai</label>
                <input 
                    type="text" 
                    name="pai" 
                    placeholder="Nome do seu pai" 
                    value="{{ old('pai') }}"
                    class="{{ $errors->has('pai') ? 'is-invalid' : '' }}"
                >
                @error('pai')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <!-- ⭐ BIO -->
            <div class="form-group">
                <label><i class="fas fa-align-left"></i> Biografia</label>
                <textarea 
                    name="bio" 
                    rows="3" 
                    placeholder="Fale um pouco sobre você..."
                    class="{{ $errors->has('bio') ? 'is-invalid' : '' }}"
                >{{ old('bio') }}</textarea>
                @error('bio')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <!-- ⭐ SENHA -->
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Senha <span class="required">*</span></label>
                <input 
                    type="password" 
                    name="password" 
                    placeholder="Mínimo 8 caracteres" 
                    required
                    minlength="8"
                    class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                >
                <span class="help-text">Mínimo 8 caracteres.</span>
                @error('password')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <!-- ⭐ CONFIRMAR SENHA -->
            <div class="form-group">
                <label><i class="fas fa-check-circle"></i> Confirmar Senha <span class="required">*</span></label>
                <input 
                    type="password" 
                    name="password_confirmation" 
                    placeholder="Confirme sua senha" 
                    required
                >
            </div>

            <!-- ⭐ TERMOS -->
            <div class="terms-group {{ $errors->has('terms') ? 'is-invalid' : '' }}">
                <input type="checkbox" name="terms" id="terms" value="1" {{ old('terms') ? 'checked' : '' }}>
                <label for="terms">
                    Li e aceito os <a href="#" target="_blank">Termos de Uso</a> <span class="required">*</span>
                </label>
            </div>
            @error('terms')
                <span class="error-text">{{ $message }}</span>
            @enderror

            <!-- ⭐ PRIVACIDADE -->
            <div class="terms-group">
                <input type="checkbox" name="privacidade" id="privacidade" value="1" {{ old('privacidade') ? 'checked' : '' }}>
                <label for="privacidade">
                    Perfil privado (apenas membros podem ver)
                </label>
            </div>

            <button type="submit" class="btn-register">
                <i class="fas fa-user-plus"></i> Cadastrar
            </button>
        </form>

        <div class="login-link">
            <a href="{{ route('login') }}"><i class="fas fa-sign-in-alt"></i> Já tem conta? Faça login</a>
        </div>
    </div>

    <!-- ⭐ SCRIPTS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    
    <script>
        $(document).ready(function() {
            // ============================================================
            // MÁSCARAS
            // ============================================================
            $('#telefone').mask('(00) 00000-0000');
            $('#telefone2').mask('(00) 00000-0000');
            $('#documento').mask('000.000.000-00');
            $('#cep').mask('00000-000');

            // ============================================================
            // MOSTRAR/OCULTAR DATA DE CONSAGRAÇÃO
            // ============================================================
            var cargosComConsagracao = [
                'pastor_presidente', 'co_pastor', 'pastor',
                'evangelista', 'presbítero', 'diácono', 'auxiliar'
            ];

            function toggleDataConsagracao() {
                var funcao = $('#funcao').val();
                if (cargosComConsagracao.includes(funcao)) {
                    $('#dataConsagracaoGroup').slideDown(200);
                    $('#data_Consagracao').prop('required', true);
                } else {
                    $('#dataConsagracaoGroup').slideUp(200);
                    $('#data_Consagracao').prop('required', false);
                }
            }

            $('#funcao').on('change', toggleDataConsagracao);
            toggleDataConsagracao();

            // ============================================================
            // VERIFICAR EMAIL EM TEMPO REAL
            // ============================================================
            var emailTimeout;

            $('#email').on('input', function() {
                clearTimeout(emailTimeout);
                var email = $(this).val();
                var feedback = $('#emailFeedback');
                
                if (email && email.length > 5 && email.includes('@')) {
                    emailTimeout = setTimeout(function() {
                        $.ajax({
                            url: '{{ route("verificar.email") }}',
                            method: 'POST',
                            data: {
                                email: email,
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.available) {
                                    feedback.html('<span class="feedback-success">✅ ' + response.message + '</span>');
                                } else {
                                    feedback.html('<span class="feedback-error">❌ ' + response.message + '</span>');
                                }
                            },
                            error: function() {
                                feedback.html('<span class="feedback-warning">⚠️ Não foi possível verificar o e-mail.</span>');
                            }
                        });
                    }, 500);
                } else {
                    feedback.html('');
                }
            });
        });
    </script>
</body>
</html>