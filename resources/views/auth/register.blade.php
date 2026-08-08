<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - Conexão Igreja</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
            max-height: 98vh;
            overflow-y: auto;
        }
        
        .register-container::-webkit-scrollbar { width: 4px; }
        .register-container::-webkit-scrollbar-thumb { background: #d4af37; border-radius: 10px; }
        
        .register-header { text-align: center; margin-bottom: 25px; }
        .register-header .icon { font-size: 2.5rem; color: #d4af37; }
        .register-header h1 { color: #d4af37; font-size: 1.8rem; }
        .register-header p { color: rgba(255,255,255,0.5); font-size: 0.9rem; }
        
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .form-group { margin-bottom: 12px; }
        .form-group.full-width { grid-column: 1 / -1; }
        
        .form-group label {
            display: block;
            color: rgba(255,255,255,0.7);
            font-size: 0.8rem;
            margin-bottom: 4px;
            font-weight: 600;
        }
        
        .form-group label .required { color: #f44336; }
        .form-group label .optional { color: rgba(255,255,255,0.3); font-weight: 400; }
        
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px 14px;
            border: 2px solid rgba(255,255,255,0.08);
            border-radius: 10px;
            background: rgba(255,255,255,0.06);
            color: white;
            font-size: 0.95rem;
            transition: 0.3s;
            appearance: auto;
            -webkit-appearance: auto;
        }
        
        .form-group input:focus, .form-group select:focus {
            outline: none;
            border-color: #d4af37;
            background: rgba(255,255,255,0.1);
        }
        
        .form-group input::placeholder { color: rgba(255,255,255,0.3); }
        
        .form-group select {
            background-color: #1a1a2e;
            color: #ffffff !important;
            cursor: pointer;
        }
        
        .form-group select option {
            background-color: #1a1a2e !important;
            color: #ffffff !important;
            padding: 8px 12px;
            font-size: 0.95rem;
        }
        
        .form-group select option:checked {
            background-color: #d4af37 !important;
            color: #1a1a2e !important;
        }
        
        .form-group .help-text { 
            color: rgba(255,255,255,0.4); 
            font-size: 0.7rem; 
            margin-top: 4px; 
            display: block; 
        }
        
        #status-busca {
            padding: 10px 14px;
            border-radius: 10px;
            margin-bottom: 15px;
            display: none;
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        #status-busca.loading {
            display: block;
            background: rgba(255, 193, 7, 0.15);
            border: 1px solid #ffc107;
            color: #ffc107;
        }
        
        #status-busca.success {
            display: block;
            background: rgba(76, 175, 80, 0.15);
            border: 1px solid #4caf50;
            color: #4caf50;
        }
        
        #status-busca.error {
            display: block;
            background: rgba(244, 67, 54, 0.15);
            border: 1px solid #f44336;
            color: #f44336;
        }
        
        .form-group input.preencher-automatico {
            border-color: rgba(76, 175, 80, 0.3);
            background: rgba(76, 175, 80, 0.05);
        }
        
        .terms-group {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin: 10px 0;
        }
        
        .terms-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin-top: 2px;
            accent-color: #d4af37;
            cursor: pointer;
            flex-shrink: 0;
        }
        
        .terms-group label {
            color: rgba(255,255,255,0.6);
            font-size: 0.85rem;
            cursor: pointer;
        }
        
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
        
        .btn-register:hover {
            transform: scale(1.02);
            box-shadow: 0 8px 30px rgba(212, 175, 55, 0.3);
        }
        
        .btn-register:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .login-link {
            text-align: center;
            color: rgba(255,255,255,0.4);
            font-size: 0.9rem;
            margin-top: 18px;
        }
        
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
        }
        
        .error-message i { flex-shrink: 0; font-size: 1.2rem; }
        
        @media (max-width: 480px) {
            .register-container { padding: 25px; }
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <div class="icon"><i class="fas fa-user-plus"></i></div>
            <h1>Criar Conta</h1>
            <p>Preencha os dados para ativar sua conta</p>
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

        <div id="status-busca"></div>

        <form method="POST" action="{{ route('register') }}" id="form-register">
            @csrf
            
            <!-- MATRÍCULA -->
            <div class="form-group full-width">
                <label>Matrícula <span class="required">*</span></label>
                <input type="number" 
                       name="matricula" 
                       id="matricula" 
                       value="{{ old('matricula') }}" 
                       placeholder="Digite sua matrícula" 
                       required>
                <span class="help-text">Digite sua matrícula para verificar e preencher automaticamente</span>
            </div>
            
            <!-- NOME COMPLETO -->
            <div class="form-group full-width">
                <label>Nome Completo <span class="required">*</span></label>
                <input type="text" 
                       name="nome" 
                       id="nome" 
                       value="{{ old('nome') }}" 
                       placeholder="Seu nome completo" 
                       required>
            </div>
            
            <!-- EMAIL -->
            <div class="form-group">
                <label>Email <span class="required">*</span></label>
                <input type="email" 
                       name="email" 
                       id="email" 
                       value="{{ old('email') }}" 
                       placeholder="seu@email.com" 
                       required>
            </div>
            
            <!-- TELEFONE -->
            <div class="form-group">
                <label>Telefone <span class="required">*</span></label>
                <input type="text" 
                       name="telefone" 
                       id="telefone" 
                       value="{{ old('telefone') }}" 
                       placeholder="(99) 99999-9999" 
                       required>
            </div>
            
            <!-- DOCUMENTO -->
            <div class="form-group">
                <label>Documento <span class="required">*</span></label>
                <input type="text" 
                       name="documento" 
                       id="documento" 
                       value="{{ old('documento') }}" 
                       placeholder="000.000.000-00" 
                       required>
            </div>
            
            <!-- DATA NASCIMENTO -->
            <div class="form-group">
                <label>Data de Nascimento <span class="required">*</span></label>
                <input type="date" 
                       name="dataNascimento" 
                       id="dataNascimento" 
                       value="{{ old('dataNascimento') }}" 
                       required>
            </div>

            <!-- DATA BATISMO (OPCIONAL) -->
            <div class="form-group">
                <label>Data de Batismo <span class="optional">(opcional)</span></label>
                <input type="date" 
                       name="dataBatismo" 
                       id="dataBatismo" 
                       value="{{ old('dataBatismo') }}">
            </div>
            
            <!-- ENDEREÇO -->
            <div class="form-group full-width">
                <label>Endereço <span class="required">*</span></label>
                <input type="text" 
                       name="endereco" 
                       id="endereco" 
                       value="{{ old('endereco') }}" 
                       placeholder="Rua, Avenida..." 
                       required>
            </div>
            
            <!-- CIDADE -->
            <div class="form-group">
                <label>Cidade <span class="required">*</span></label>
                <input type="text" 
                       name="cidade" 
                       id="cidade" 
                       value="{{ old('cidade') }}" 
                       placeholder="Sua cidade" 
                       required>
            </div>
            
            <!-- UF -->
            <div class="form-group">
                <label>UF <span class="required">*</span></label>
                <select name="uf" id="uf" required>
                    <option value="">UF</option>
                    @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                        <option value="{{ $uf }}" {{ old('uf') == $uf ? 'selected' : '' }}>{{ $uf }}</option>
                    @endforeach
                </select>
            </div>

            <!-- CONGREGAÇÃO -->
            <div class="form-group full-width">
                <label>Congregação <span class="required">*</span></label>
                <select name="congregacao" id="congregacao" required>
                    <option value="">Selecione sua congregação</option>
                    @php
                        $congregacoes = [
                            'SEDE', 'ALEGRIA', 'JUBAIA', 'LAGES', 'NOVO MARANGUAPE 1',
                            'NOVO MARANGUAPE 2', 'NOVO MARANGUAPE 3', 'NOVO MARANGUAPE 4',
                            'OUTRA BANDA', 'PARQUE SÃO JOÃO', 'NOVO PARQUE IRACEMA',
                            'NOVO PARQUE IRACEMA 2', 'SITIO SÃO LUIZ', 'TABATINGA',
                            'UMARIZEIRAS', 'VITÓRIA', 'VIÇOSA', 'PAPARA', 'PLANALTO',
                            'SERRA JUBAIA', 'IRACEMA', 'PARAISO', 'CASTELO', 'LAMEIRÃO'
                        ];
                    @endphp
                    @foreach($congregacoes as $congregacao)
                        <option value="{{ $congregacao }}" {{ old('congregacao') == $congregacao ? 'selected' : '' }}>
                            {{ $congregacao }}
                        </option>
                    @endforeach
                </select>
                <span class="help-text">Selecione a congregação onde você congrega</span>
            </div>

            <!-- FUNÇÃO -->
            <div class="form-group full-width">
                <label>Função/Cargo <span class="required">*</span></label>
                <select name="funcao" id="funcao" required>
                    <option value="">Selecione sua função</option>
                    <option value="novo_convertido" {{ old('funcao') == 'novo_convertido' ? 'selected' : '' }}>Novo Convertido</option>
                    <option value="membro" {{ old('funcao') == 'membro' ? 'selected' : '' }}>Membro</option>
                    <option value="congregado" {{ old('funcao') == 'congregado' ? 'selected' : '' }}>Congregado</option>
                    <option value="auxiliar" {{ old('funcao') == 'auxiliar' ? 'selected' : '' }}>Auxiliar</option>
                    <option value="diácono" {{ old('funcao') == 'diácono' ? 'selected' : '' }}>Diácono</option>
                    <option value="presbítero" {{ old('funcao') == 'presbítero' ? 'selected' : '' }}>Presbítero</option>
                    <option value="evangelista" {{ old('funcao') == 'evangelista' ? 'selected' : '' }}>Evangelista</option>
                    <option value="missionário" {{ old('funcao') == 'missionário' ? 'selected' : '' }}>Missionário</option>
                    <option value="pastor_presidente" {{ old('funcao') == 'pastor_presidente' ? 'selected' : '' }}>Pastor-Presidente</option>
                    <option value="co_pastor" {{ old('funcao') == 'co_pastor' ? 'selected' : '' }}>Co-Pastor</option>
                    <option value="pastor" {{ old('funcao') == 'pastor' ? 'selected' : '' }}>Pastor</option>
                </select>
                <span class="help-text">Selecione seu cargo ou função na igreja</span>
            </div>

            <!-- DATA DE CONSAGRAÇÃO -->
            <div class="form-group full-width" id="campo-consagracao" style="display: {{ in_array(old('funcao'), ['pastor_presidente', 'co_pastor', 'pastor', 'evangelista', 'presbítero', 'diácono', 'auxiliar', 'missionário']) ? 'block' : 'none' }};">
                <label>Data de Consagração <span class="required">*</span></label>
                <input type="date" 
                       name="data_Consagracao" 
                       id="data_Consagracao" 
                       value="{{ old('data_Consagracao') }}" 
                       {{ in_array(old('funcao'), ['pastor_presidente', 'co_pastor', 'pastor', 'evangelista', 'presbítero', 'diácono', 'auxiliar', 'missionário']) ? 'required' : '' }}>
                <span class="help-text">Preencha a data da sua consagração/ordenação</span>
            </div>
            
            <!-- SENHA -->
            <div class="form-group full-width">
                <label>Senha <span class="required">*</span></label>
                <input type="password" 
                       name="password" 
                       id="password" 
                       placeholder="Mínimo 6 caracteres" 
                       required>
            </div>
            
            <!-- CONFIRMAR SENHA -->
            <div class="form-group full-width">
                <label>Confirmar Senha <span class="required">*</span></label>
                <input type="password" 
                       name="password_confirmation" 
                       id="password_confirmation" 
                       placeholder="Digite a senha novamente" 
                       required>
            </div>
            
            <!-- TERMOS -->
            <div class="form-group full-width">
                <div class="terms-group">
                    <input type="checkbox" name="terms" id="terms" required>
                    <label for="terms">Li e aceito os <a href="#">Termos de Uso</a> e a <a href="#">Política de Privacidade</a></label>
                </div>
            </div>
            
            <button type="submit" class="btn-register" id="btn-register">
                <i class="fas fa-user-check"></i> Ativar minha conta
            </button>
        </form>

        <div class="login-link">
            Já tem conta? <a href="{{ route('login') }}">Faça login</a>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // ============================================================
        // ELEMENTOS DO DOM
        // ============================================================
        const matriculaInput = document.getElementById('matricula');
        const statusBusca = document.getElementById('status-busca');
        const btnRegister = document.getElementById('btn-register');
        
        const camposPreencher = {
            nome: document.getElementById('nome'),
            funcao: document.getElementById('funcao'),
            cidade: document.getElementById('cidade'),
            uf: document.getElementById('uf'),
            congregacao: document.getElementById('congregacao'),
        };
        
        // ============================================================
        // FUNÇÃO PARA MOSTRAR STATUS
        // ============================================================
        function mostrarStatus(mensagem, tipo) {
            statusBusca.textContent = mensagem;
            statusBusca.className = tipo;
            statusBusca.style.display = 'block';
        }
        
        function esconderStatus() {
            statusBusca.style.display = 'none';
            statusBusca.className = '';
        }
        
        // ============================================================
        // FUNÇÃO PARA PREENCHER CAMPOS
        // ============================================================
        function preencherCampos(dados) {
            if (dados.nome && camposPreencher.nome) {
                camposPreencher.nome.value = dados.nome;
                camposPreencher.nome.classList.add('preencher-automatico');
            }
            
            if (dados.funcao && camposPreencher.funcao) {
                const mapeamentoFuncao = {
                    'Membro': 'membro',
                    'Pastor-Presidente': 'pastor_presidente',
                    'Pastor-Vice-Presidente': 'co_pastor',
                    'Pastor': 'pastor',
                    'Evangelista': 'evangelista',
                    'Presbitero': 'presbítero',
                    'Diacono': 'diácono',
                    'Auxiliar': 'auxiliar',
                    'Missionário': 'missionário'
                };
                
                const funcaoMapeada = mapeamentoFuncao[dados.funcao] || dados.funcao;
                
                const options = camposPreencher.funcao.options;
                for (let i = 0; i < options.length; i++) {
                    if (options[i].value === funcaoMapeada) {
                        options[i].selected = true;
                        break;
                    }
                }
                camposPreencher.funcao.classList.add('preencher-automatico');
                
                const event = new Event('change');
                camposPreencher.funcao.dispatchEvent(event);
            }
            
            if (dados.cidade && camposPreencher.cidade) {
                camposPreencher.cidade.value = dados.cidade;
                camposPreencher.cidade.classList.add('preencher-automatico');
            }
            
            if (dados.uf && camposPreencher.uf) {
                const options = camposPreencher.uf.options;
                for (let i = 0; i < options.length; i++) {
                    if (options[i].value === dados.uf) {
                        options[i].selected = true;
                        break;
                    }
                }
                camposPreencher.uf.classList.add('preencher-automatico');
            }
            
            if (dados.congregacao && camposPreencher.congregacao) {
                const options = camposPreencher.congregacao.options;
                for (let i = 0; i < options.length; i++) {
                    if (options[i].value === dados.congregacao) {
                        options[i].selected = true;
                        break;
                    }
                }
                camposPreencher.congregacao.classList.add('preencher-automatico');
            }
        }
        
        // ============================================================
        // FUNÇÃO PARA BUSCAR MEMBRO NO BANCO ANTIGO
        // ============================================================
        function buscarMembro(matricula) {
            if (!matricula || matricula.length < 2) {
                esconderStatus();
                return;
            }
            
            mostrarStatus('⏳ Buscando dados da matrícula ' + matricula + '...', 'loading');
            btnRegister.disabled = true;
            
            const url = '/sistemas/conexao-igreja/buscar-membro-antigo/' + encodeURIComponent(matricula);
            
            console.log('🔍 Buscando:', url);
            
            fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => {
                console.log('📥 Status:', response.status);
                if (!response.ok) {
                    throw new Error('Erro na requisição: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('📦 Dados recebidos:', data);
                
                if (data.success && data.exists) {
                    mostrarStatus('✅ Dados encontrados! Campos preenchidos automaticamente.', 'success');
                    preencherCampos(data.dados);
                    btnRegister.disabled = false;
                } else if (data.success && !data.exists) {
                    mostrarStatus('⚠️ Matrícula ' + matricula + ' não encontrada. Favor procurar a Secretaria Geral da Sede.', 'error');
                    btnRegister.disabled = true;
                } else {
                    mostrarStatus('❌ Erro ao buscar dados: ' + (data.message || 'Tente novamente.'), 'error');
                    btnRegister.disabled = false;
                }
            })
            .catch(error => {
                console.error('❌ Erro:', error);
                mostrarStatus('❌ Erro na conexão: ' + error.message, 'error');
                btnRegister.disabled = false;
            });
        }
        
        // ============================================================
        // EVENTOS DA MATRÍCULA
        // ============================================================
        matriculaInput.addEventListener('blur', function() {
            const matricula = this.value.trim();
            if (matricula) {
                buscarMembro(matricula);
            } else {
                esconderStatus();
                btnRegister.disabled = false;
            }
        });
        
        matriculaInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const matricula = this.value.trim();
                if (matricula) {
                    buscarMembro(matricula);
                }
            }
        });
        
        matriculaInput.addEventListener('input', function() {
            if (this.value.trim() === '') {
                esconderStatus();
                btnRegister.disabled = false;
            }
        });
        
        // ============================================================
        // TOGGLE DATA DE CONSAGRAÇÃO
        // ============================================================
        const funcaoSelect = document.getElementById('funcao');
        const campoConsagracao = document.getElementById('campo-consagracao');
        const inputConsagracao = document.getElementById('data_Consagracao');

        const cargosConsagracao = [
            'pastor_presidente',
            'co_pastor',
            'pastor',
            'evangelista',
            'presbítero',
            'diácono',
            'auxiliar',
            'missionário'
        ];

        function toggleCampoConsagracao() {
            const funcao = funcaoSelect.value;
            const show = cargosConsagracao.includes(funcao);

            if (show) {
                campoConsagracao.style.display = 'block';
                inputConsagracao.setAttribute('required', 'required');
            } else {
                campoConsagracao.style.display = 'none';
                inputConsagracao.removeAttribute('required');
                inputConsagracao.value = '';
            }
        }

        toggleCampoConsagracao();
        funcaoSelect.addEventListener('change', toggleCampoConsagracao);
        
        // ============================================================
        // MASCARA PARA TELEFONE
        // ============================================================
        const telefoneInput = document.getElementById('telefone');
        if (telefoneInput) {
            telefoneInput.addEventListener('input', function(e) {
                let value = this.value.replace(/\D/g, '');
                if (value.length <= 11) {
                    if (value.length > 2) {
                        value = '(' + value.substring(0, 2) + ') ' + value.substring(2);
                    }
                    if (value.length > 10) {
                        value = value.substring(0, 10) + '-' + value.substring(10);
                    }
                    if (value.length > 7 && value.length <= 10) {
                        value = value.substring(0, 7) + '-' + value.substring(7);
                    }
                    this.value = value;
                }
            });
        }
        
        // ============================================================
        // MASCARA PARA DOCUMENTO (CPF)
        // ============================================================
        const documentoInput = document.getElementById('documento');
        if (documentoInput) {
            documentoInput.addEventListener('input', function(e) {
                let value = this.value.replace(/\D/g, '');
                if (value.length <= 14) {
                    if (value.length > 3) {
                        value = value.substring(0, 3) + '.' + value.substring(3);
                    }
                    if (value.length > 7) {
                        value = value.substring(0, 7) + '.' + value.substring(7);
                    }
                    if (value.length > 11) {
                        value = value.substring(0, 11) + '-' + value.substring(11);
                    }
                    this.value = value;
                }
            });
        }

        // ============================================================
        // VALIDAR SENHA EM TEMPO REAL
        // ============================================================
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('password_confirmation');
        
        function validarSenha() {
            const senha = passwordInput.value;
            const confirmacao = confirmPasswordInput.value;
            
            if (senha.length > 0 && senha.length < 6) {
                passwordInput.style.borderColor = '#f44336';
            } else if (senha.length >= 6) {
                passwordInput.style.borderColor = '#4caf50';
            }
            
            if (confirmacao.length > 0 && senha !== confirmacao) {
                confirmPasswordInput.style.borderColor = '#f44336';
            } else if (confirmacao.length > 0 && senha === confirmacao) {
                confirmPasswordInput.style.borderColor = '#4caf50';
            }
        }
        
        if (passwordInput) {
            passwordInput.addEventListener('input', validarSenha);
        }
        
        if (confirmPasswordInput) {
            confirmPasswordInput.addEventListener('input', validarSenha);
        }
    });
    </script>
</body>
</html>