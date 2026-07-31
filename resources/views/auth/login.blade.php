<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Conexão Igreja</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .login-container {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 40px;
            max-width: 420px;
            width: 100%;
            border: 1px solid rgba(212, 175, 55, 0.15);
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        }
        .login-header { text-align: center; margin-bottom: 30px; }
        .login-header .icon { font-size: 3rem; color: #d4af37; margin-bottom: 8px; }
        .login-header h1 { color: #d4af37; font-size: 1.8rem; font-weight: 700; }
        .login-header p { color: rgba(255,255,255,0.5); font-size: 0.9rem; margin-top: 4px; }
        .form-group { margin-bottom: 18px; }
        .form-group label { display: block; color: rgba(255,255,255,0.7); font-size: 0.85rem; margin-bottom: 6px; font-weight: 600; }
        .form-group input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            background: rgba(255,255,255,0.06);
            color: white;
            font-size: 1rem;
            transition: 0.3s;
        }
        .form-group input:focus { outline: none; border-color: #d4af37; background: rgba(255,255,255,0.1); }
        .form-group input::placeholder { color: rgba(255,255,255,0.3); }
        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #d4af37, #c9a84c);
            border: none;
            border-radius: 12px;
            color: #1a1a2e;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-login:hover { transform: scale(1.02); box-shadow: 0 8px 30px rgba(212, 175, 55, 0.3); }
        .register-link { text-align: center; color: rgba(255,255,255,0.4); font-size: 0.9rem; margin-top: 18px; }
        .register-link a { color: #d4af37; text-decoration: none; font-weight: 600; }
        .register-link a:hover { text-decoration: underline; }
        .error-message {
            background: rgba(244, 67, 54, 0.15);
            border: 1px solid #f44336;
            color: #f44336;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
        }
        .success-message {
            background: rgba(76, 175, 80, 0.15);
            border: 1px solid #4caf50;
            color: #4caf50;
            padding: 12px 16px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="icon"><i class="fas fa-cross"></i></div>
            <h1>Conexão Igreja</h1>
            <p>Rede social dos membros</p>
        </div>

        @if(session('error'))
            <div class="error-message"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
        @endif

        @if(session('success'))
            <div class="success-message"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label><i class="fas fa-id-card"></i> Matrícula</label>
                <input type="number" name="matricula" placeholder="Digite sua matrícula" required autofocus>
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock"></i> Senha</label>
                <input type="password" name="password" placeholder="Digite sua senha" required>
            </div>
            <button type="submit" class="btn-login"><i class="fas fa-sign-in-alt"></i> Entrar</button>
        </form>

        <div class="register-link">
            <a href="{{ route('register') }}"><i class="fas fa-user-plus"></i> Criar minha conta</a>
        </div>
    </div>
</body>
</html>