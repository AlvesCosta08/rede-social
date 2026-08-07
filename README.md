Sistema de Comunidade Cristã
📋 Visão Geral
Plataforma de comunidade cristã desenvolvida com Laravel, oferecendo funcionalidades completas de rede social, incluindo feed de publicações, perfis de membros, sistema de seguidores, e painel administrativo.

🚀 Funcionalidades Principais
🔐 Autenticação
Login e registro de usuários

Verificação de matrícula

Sistema de logout

Proteção contra ataques de força bruta

📱 Feed Social
Publicação de posts

Sistema de curtidas

Comentários em posts

Feed global e personalizado

Deleção e edição de posts/comentários

👤 Perfis de Membros
Visualização de perfis

Edição de perfil

Upload e remoção de foto de perfil

Cartão digital do membro

Visualização de posts por membro

🔍 Busca e Navegação
Busca por membros (nome, matrícula)

Autocomplete para busca

Busca avançada com filtros

Busca por proximidade geográfica

Sugestões de membros

👥 Sistema de Seguidores
Seguir/deixar de seguir membros

Lista de seguidores/seguindo

Sugestões de membros para seguir

Verificação de relação entre membros

🛡️ Painel Administrativo
CRUD completo de membros

Ações em massa

Estatísticas do sistema

Exportação de dados

Gerenciamento de posts

📂 Estrutura do Projeto
text
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/
│   │   │   │   └── MembroController.php
│   │   │   ├── AuthController.php
│   │   │   ├── FeedController.php
│   │   │   ├── ImagemController.php
│   │   │   ├── MembroController.php
│   │   │   ├── PerfilController.php
│   │   │   └── SeguidorController.php
│   │   ├── Middleware/
│   │   │   └── AdminMiddleware.php
│   │   └── Requests/
│   ├── Models/
│   │   ├── Membro.php
│   │   ├── Post.php
│   │   ├── Comentario.php
│   │   ├── Curtida.php
│   │   ├── Seguidor.php
│   │   └── Imagem.php
│   └── Services/
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── views/
│   │   ├── auth/
│   │   ├── feed/
│   │   ├── perfil/
│   │   ├── membros/
│   │   ├── seguidores/
│   │   └── admin/
│   └── css/
├── routes/
│   └── web.php
└── public/
    └── uploads/
🛡️ Segurança
Práticas Implementadas
CSRF Protection em todas as requisições POST/PUT/DELETE

SQL Injection Protection via Eloquent ORM

XSS Protection com escopo de saída

Rate Limiting para prevenir DDoS

Validação de dados nas requisições

Verificação de permissões em ações administrativas

Autenticação obrigatória para rotas protegidas

Limites de Requisição
Feed: 300 req/min

Publicações: 20 req/min

Curtidas: 100 req/min

Comentários: 30 req/min

Buscas: 60 req/min

Login: 10 req/min

📊 Modelos de Dados
Principais Modelos
Membro - Usuários da plataforma

Matrícula, nome, email, foto, cargo, função, etc.

Post - Publicações no feed

Conteúdo, imagem, membro_id, data

Comentario - Comentários em posts

Conteúdo, post_id, membro_id

Curtida - Curtidas em posts

post_id, membro_id

Seguidor - Relações de seguir

seguidor_id, seguido_id, data

Imagem - Fotos de perfil e posts

Caminho, tipo, membro_id

Relacionamentos
Membro → Posts (1:N)

Post → Comentarios (1:N)

Post → Curtidas (1:N)

Membro → Seguidores (auto-relacionamento N:N)

🔧 Instalação e Configuração
Pré-requisitos
PHP 8.0+

Composer

MySQL/PostgreSQL

Laravel 10+

Passos para Instalação
bash
# Clone o repositório
git clone [url-do-repositorio]

# Instale as dependências
composer install

# Configure o arquivo .env
cp .env.example .env

# Gere a chave da aplicação
php artisan key:generate

# Execute as migrações
php artisan migrate

# Popule o banco de dados (opcional)
php artisan db:seed

# Inicie o servidor
php artisan serve
Variáveis de Ambiente
env
APP_NAME="Sistema Comunidade"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=comunidade
DB_USERNAME=root
DB_PASSWORD=

# Configurações de upload
UPLOAD_MAX_SIZE=2048
ALLOWED_EXTENSIONS=jpg,jpeg,png,gif
🧪 Testes
bash
# Executar todos os testes
php artisan test

# Executar testes específicos
php artisan test --filter=AuthController

# Executar com cobertura de código
php artisan test --coverage
📝 Logs e Monitoramento
Os logs são registrados em storage/logs/laravel.log com os seguintes níveis:

Erros 404: Registrados com detalhes da requisição

Ações administrativas: Registro de operações críticas

Falhas de autenticação: Tentativas de login inválidas

Operações de banco: Queries lentas e erros

📦 Dependências
Produção
Laravel Framework ^10.0

MySQL/MariaDB Driver

Laravel Sanctum (API)

Intervention Image (manipulação de imagens)

Laravel Debugbar (desenvolvimento)

Desenvolvimento
Laravel Tinker

PHPUnit

Laravel Sail (Docker)

Laravel IDE Helper

🤝 Contribuição
Fork o projeto

Crie sua branch (git checkout -b feature/nova-funcionalidade)

Commit suas mudanças (git commit -m 'Adiciona nova funcionalidade')

Push para a branch (git push origin feature/nova-funcionalidade)

Abra um Pull Request

Padrões de Código
PSR-12 para PHP

ESLint para JavaScript

Nomes de classes em PascalCase

Métodos em camelCase

Variáveis em snake_case

📄 Licença
Este projeto é proprietário e confidencial.

📞 Suporte
Para suporte, entre em contato com a equipe de desenvolvimento.

📚 Documentação Adicional
Documentação do Laravel

Guia de Estilo PHP

Padrões de Projeto

Versão: 1.0.0
Última Atualização: 2026

