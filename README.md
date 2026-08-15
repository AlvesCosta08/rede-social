<div align="center">

# 🏛️ **SISTEMA COMUNIDADE CRISTÃ**

### *Plataforma completa para gestão de membros e interação social*

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://mysql.com)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)

</div>

---

## 📋 **ÍNDICE**

- [📖 Sobre o Projeto](#-sobre-o-projeto)
- [✨ Funcionalidades](#-funcionalidades)
- [🛠️ Tecnologias](#️-tecnologias)
- [🏗️ Arquitetura](#️-arquitetura)
- [👥 Níveis de Usuário](#-níveis-de-usuário)
- [🔐 Permissões](#-permissões)
- [📂 Estrutura do Projeto](#-estrutura-do-projeto)
- [🚀 Instalação](#-instalação)
- [⚙️ Configuração](#️-configuração)
- [📡 Rotas Principais](#-rotas-principais)
- [📊 API Endpoints](#-api-endpoints)
- [🛠️ Comandos Úteis](#️-comandos-úteis)
- [🧪 Testes](#-testes)
- [🤝 Contribuição](#-contribuição)
- [📄 Licença](#-licença)

---

## 📖 **SOBRE O PROJETO**

Sistema desenvolvido para **gestão completa de membros** de uma comunidade cristã, combinando funcionalidades administrativas com interação social.

### 🎯 **Objetivos**

- ✅ Gerenciar membros com diferentes níveis de acesso
- ✅ Facilitar a comunicação entre membros
- ✅ Oferecer carteira digital personalizada
- ✅ Gerar estatísticas e relatórios
- ✅ Garantir segurança e controle de permissões

---

## ✨ **FUNCIONALIDADES**

### 👤 **Membros**
- Cadastro completo com dados pessoais
- Sistema de níveis (Admin, Secretário, Membro)
- Carteira digital com foto e dados
- Geolocalização de membros
- Sistema de seguidores

### 📱 **Feed Social**
- Publicações com texto
- Sistema de curtidas
- Comentários em publicações
- Feed personalizado (seguindo)
- Feed global

### 🔐 **Administração**
- Dashboard com estatísticas
- CRUD completo de membros
- Gerenciamento de secretários
- Ações em massa (ativar, inativar, transferir, excluir)
- Exportação de dados (CSV)
- Sistema de permissões granulares

### 🎨 **Interface**
- Design responsivo
- Badges de status e níveis
- Upload de foto de perfil
- Busca e filtros avançados

---

## 🛠️ **TECNOLOGIAS**

### **Backend**
| Tecnologia | Versão | Descrição |
|------------|--------|-----------|
| **PHP** | ^8.4 | Linguagem de programação |
| **Laravel** | ^12.0 | Framework PHP |
| **MySQL** | 8.0+ | Banco de dados relacional |
| **Laravel Sanctum** | ^4.0 | Autenticação API |
| **Laravel Tinker** | ^2.9 | REPL para Laravel |

### **Frontend**
| Tecnologia | Descrição |
|------------|-----------|
| **Bootstrap** | Framework CSS |
| **Blade** | Template engine do Laravel |
| **jQuery** | Biblioteca JavaScript |
| **Chart.js** | Gráficos e estatísticas |

### **Ferramentas de Desenvolvimento**
| Ferramenta | Descrição |
|------------|-----------|
| **Composer** | Gerenciador de dependências PHP |
| **Git** | Controle de versão |
| **VS Code** | Editor recomendado |
| **Laravel Pint** | Code style fixer |

---

## 🏗️ **ARQUITETURA**

### **Padrões Utilizados**



### **Camadas da Aplicação**

| Camada | Descrição | Diretório |
|--------|-----------|-----------|
| **Presentation** | Controllers, Views, Resources | `app/Http/Controllers/` |
| **Application** | Services, DTOs | `app/Services/`, `app/DTOs/` |
| **Domain** | Models, Contracts | `app/Models/`, `app/Contracts/` |
| **Infrastructure** | Repositories, Migrations | `app/Repositories/`, `database/` |

---

## 👥 **NÍVEIS DE USUÁRIO**

### 👑 **Administrador**


---

## 🔐 **PERMISSÕES**

### **Matriz de Permissões**

| Ação | Admin | Secretário | Membro Comum |
|------|:-----:|:----------:|:------------:|
| `criar_membro` | ✅ | ✅ * | ❌ |
| `editar_membro` | ✅ | ✅ * | ✅ ** |
| `excluir_membro` | ✅ | ✅ * | ❌ |
| `excluir_propria_conta` | ✅ | ❌ | ✅ |
| `dashboard` | ✅ | ✅ | ❌ |
| `ver_membros` | ✅ | ✅ | ✅ |
| `ver_membro` | ✅ | ✅ | ✅ |
| `exportar_membros` | ✅ | ✅ * | ❌ |
| `gerenciar_secretarios` | ✅ | ❌ | ❌ |
| `ver_financeiro` | ✅ | ❌ | ❌ |
| `configurar_sistema` | ✅ | ❌ | ❌ |

> `*` Apenas membros da sua congregação  
> `**` Apenas o próprio perfil

---

## 📂 **ESTRUTURA DO PROJETO**
