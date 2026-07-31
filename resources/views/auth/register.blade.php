<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Conexão Igreja</title>
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
        .register-container {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 40px;
            max-width: 560px;
            width: 100%;
            border: 1px solid rgba(212, 175, 55, 0.15);
            box-shadow: 0 20px 60px rgba(0,0,0,0.5);
        }
        .register-header { text-align: center; margin-bottom: 30px; }
        .register-header .icon { font-size: 2.5rem; color: #d4af37; margin-bottom: 8px; }
        .register-header h1 { color: #d4af37; font-size: 1.8rem; font-weight: 700; }
        .register-header p { color: rgba(255,255,255,0.5); font-size: 0.9rem; margin-top: 4px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .form-group { margin-bottom: 12px; }
        .form-group.full-width { grid-column: 1 / -1; }
        .form-group label { display: block; color: rgba(255,255,255,0.7); font-size: 0.8rem; margin-bottom: 4px; font-weight: 600; }
        .form-group label .required { color: #f44336; }
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid rgba(255,255,255,0.08);
            border-radius: 10px;
            background: rgba(255,255,255,0.06);
            color: white;
            font-size: 0.95rem;
            transition: 0.3s;
        }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: #d4af37; background: rgba(255,255,255,0.1); }
        .form-group input::placeholder { color: rgba(255,255,255,0.3); }
        .form-group select option { color: #1a1a2e; }
        .form-group .error-text { color: #f44336; font-size: 0.75rem; margin-top: 4px; display: block; }
        .terms-group { display: flex; align-items: flex-start; gap: 10px; margin: 10px 0; }
        .terms-group input[type="checkbox"] { width: 18px; height: 18px; margin-top: 2px; accent-color: #d4af37; cursor: pointer; }
        .terms-group label { color: rgba(255,255,255,0.6); font-size: 0.85rem; cursor: pointer; }
        .terms-group label a { color: #d4af37; text-decoration: none; }
        .terms-group label a:hover { text-decoration: underline; }
        .btn-register {
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
            margin-top: 10px;
        }
        .btn-register:hover { transform: scale(1.02); box-shadow: 0 8px 30px rgba(212, 175, 55, 0.3); }
        .login-link { text-align: center; color: rgba(255,255,255,0.4); font-size: 0.9rem; margin-top: 18px; }
        .login-link a { color: #d4af37; text-decoration: none; font-weight: 600; }
        .login-link a:hover { text-decoration: underline; }
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
        /* Estilo para o campo de consagração - inicialmente escondido */
        #campo-consagracao {
            display: none;
        }
        #campo-consagracao.visible {
            display: block;
        }
        @media (max-width: 480px) { .register-container { padding: 25px; } .form-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <div class="icon"><i class="fas fa-user-plus"></i></div>
            <h1>Seja Bem-vindo(a)</h1>
            <p>Faça parte da nossa família cristã</p>
        </div>

        @if($errors->any())
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="form-grid">
                <div class="form-group full-width">
                    <label>Nome Completo <span class="required">*</span></label>
                    <input type="text" name="nome" value="{{ old('nome') }}" placeholder="Seu nome completo" required>
                </div>
                <div class="form-group">
                    <label>Email <span class="required">*</span></label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="seu@email.com" required>
                </div>
                <div class="form-group">
                    <label>Telefone <span class="required">*</span></label>
                    <input type="text" name="telefone" value="{{ old('telefone') }}" placeholder="(99) 99999-9999" required>
                </div>
                <div class="form-group">
                    <label>Documento (CPF/RG) <span class="required">*</span></label>
                    <input type="text" name="documento" value="{{ old('documento') }}" placeholder="000.000.000-00" required>
                </div>
                <div class="form-group">
                    <label>Data de Nascimento <span class="required">*</span></label>
                    <input type="date" name="dataNascimento" value="{{ old('dataNascimento') }}" required>
                </div>
                <div class="form-group">
                    <label>Data de Batismo</label>
                    <input type="date" name="dataBatismo" value="{{ old('dataBatismo') }}">
                </div>
                <div class="form-group full-width">
                    <label>Endereço <span class="required">*</span></label>
                    <input type="text" name="endereco" value="{{ old('endereco') }}" placeholder="Rua, Avenida..." required>
                </div>
                <div class="form-group">
                    <label>Cidade <span class="required">*</span></label>
                    <input type="text" name="cidade" value="{{ old('cidade') }}" placeholder="Sua cidade" required>
                </div>
                <div class="form-group">
                    <label>UF <span class="required">*</span></label>
                    <select name="uf" required>
                        <option value="">UF</option>
                        @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                            <option value="{{ $uf }}" {{ old('uf') == $uf ? 'selected' : '' }}>{{ $uf }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group full-width">
                    <label>Congregação <span class="required">*</span></label>
                    <input type="text" name="congregacao" value="{{ old('congregacao') }}" placeholder="Nome da sua congregação" required>
                </div>
                
                <!-- Campo de Função -->
                <div class="form-group full-width">
                    <label>Função <span class="required">*</span></label>
                    <select name="funcao" id="funcao" required>
                        <option value="">Selecione sua função</option>
                        <option value="Membro" {{ old('funcao') == 'Membro' ? 'selected' : '' }}>Membro</option>
                        <option value="Auxiliar" {{ old('funcao') == 'Auxiliar' ? 'selected' : '' }}>Auxiliar</option>
                        <option value="Obreiro" {{ old('funcao') == 'Obreiro' ? 'selected' : '' }}>Obreiro</option>
                        <option value="Evangelista" {{ old('funcao') == 'Evangelista' ? 'selected' : '' }}>Evangelista</option>
                        <option value="Diacono" {{ old('funcao') == 'Diacono' ? 'selected' : '' }}>Diácono</option>
                        <option value="Presbitero" {{ old('funcao') == 'Presbitero' ? 'selected' : '' }}>Presbítero</option>
                        <option value="Pastor" {{ old('funcao') == 'Pastor' ? 'selected' : '' }}>Pastor</option>
                    </select>
                </div>

                <!-- Campo de Data de Consagração (escondido por padrão) -->
                <div class="form-group full-width" id="campo-consagracao">
                    <label>Data de Consagração <span class="required">*</span></label>
                    <input type="date" name="data_Consagracao" value="{{ old('data_Consagracao') }}">
                    <small style="color: rgba(255,255,255,0.4); font-size: 0.75rem;">Obrigatório para funções ministeriais</small>
                </div>

                <div class="form-group full-width">
                    <label>Senha <span class="required">*</span></label>
                    <input type="password" name="password" placeholder="Mínimo 6 caracteres" required>
                </div>
                <div class="form-group full-width">
                    <label>Confirmar Senha <span class="required">*</span></label>
                    <input type="password" name="password_confirmation" placeholder="Digite a senha novamente" required>
                </div>
                <div class="form-group full-width">
                    <div class="terms-group">
                        <input type="checkbox" name="terms" id="terms" required>
                        <label for="terms">Li e aceito os <a href="#">Termos de Uso</a> e <a href="#">Política de Privacidade</a></label>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn-register"><i class="fas fa-user-check"></i> Criar minha conta</button>
        </form>

        <div class="login-link">
            Já tem conta? <a href="{{ route('login') }}">Faça login</a>
        </div>
    </div>

    <script>
        // JavaScript para mostrar/esconder o campo de consagração
        document.addEventListener('DOMContentLoaded', function() {
            const funcaoSelect = document.getElementById('funcao');
            const campoConsagracao = document.getElementById('campo-consagracao');
            const inputConsagracao = document.querySelector('input[name="data_Consagracao"]');

            // Funções que precisam de consagração
            const funcoesMinisteriais = ['Diacono', 'Presbitero', 'Pastor', 'Evangelista', 'Auxiliar', 'Obreiro'];

            function toggleCampoConsagracao() {
                const funcao = funcaoSelect.value;
                if (funcoesMinisteriais.includes(funcao)) {
                    campoConsagracao.classList.add('visible');
                    inputConsagracao.required = true;
                } else {
                    campoConsagracao.classList.remove('visible');
                    inputConsagracao.required = false;
                    inputConsagracao.value = ''; // Limpa o campo quando escondido
                }
            }

            // Verifica se já tem um valor selecionado (para manter consistência após erro de validação)
            toggleCampoConsagracao();

            // Adiciona o evento de mudança
            funcaoSelect.addEventListener('change', toggleCampoConsagracao);
        });
    </script>
</body>
</html>