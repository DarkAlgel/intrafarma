# Projeto Farmácia (IntraFarma)

Este é um sistema de gestão de estoque de medicamentos para controle de entradas, saídas, lotes e validades, construído com PHP - Laravel.

Este guia destina-se à configuração e execução do projeto em um ambiente de desenvolvimento local.

## Stack do Ambiente

* PHP: 8.2+ (incluído no projeto como versão portátil)
* Banco de Dados: PostgreSQL (pgsql)
* Backend: Laravel 11 (PHP)
* Frontend: Tailwind CSS (compilado com Vite)
* Gerenciadores de Pacotes: Composer (PHP) e NPM/PNPM (Node.js)

---

## Pré-requisitos

Antes de começar, garanta que você tenha os seguintes softwares instalados:

1. **Git**: Para clonar o repositório
2. **PHP 8.2+**: Incluído no projeto como versão portátil.
3. **Composer**: O gerenciador de pacotes para PHP. Baixe em https://getcomposer.org/download/
4. **Node.js (com npm/pnpm)**: Para o Tailwind/Vite. Baixe a versão LTS em https://nodejs.org/

**Nota**: O projeto inclui uma versão portátil do PHP 8.2 configurada, então você não precisa instalar PHP separadamente.

---

## Guia de Instalação (Passo a Passo)

Siga estes passos para configurar o ambiente e rodar o projeto.

### 1. Clonar o Repositório

Clone o projeto para sua pasta de projetos:

```bash
git clone https://github.com/DarkAlgel/intrafarma.git
cd intrafarma
```

---

### 2. Instalar Dependências do PHP

O projeto inclui uma versão portátil do PHP 8.2 pré-configurada. Execute:

```bash
composer install
```

**Nota**: O Composer automaticamente usará o PHP portátil incluído no projeto (`tools/php82/bin/`).

---

### 3. Configurar o Ambiente

#### 3.1. Criar o Arquivo .env

Copie o arquivo de configuração de exemplo:

```bash
copy .env.example .env
```

#### 3.2. Configurar Banco de Dados PostgreSQL (pgsql)

No arquivo `.env`, configure a conexão com seu Postgres local:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=farmacia_db
DB_USERNAME=postgres
DB_PASSWORD=123
```

#### 3.3. Criar Banco e Importar Schema (pgsql)

```bash
psql -h 127.0.0.1 -U postgres -d farmacia_db -f database/scripts/schema_farmacia.sql
```

#### 3.4. Gerar Chave da Aplicação

```bash
php artisan key:generate
```

#### 3.5. Executar Migrações (opcional)

Se preferir usar migrações do Laravel:

```bash
php artisan migrate --force
```

Caso já tenha importado o schema `.sql`, as migrações podem não ser necessárias.

---

### 4. Instalar Dependências do Frontend

Para o Tailwind CSS e Vite:

```bash
npm install
# ou se preferir pnpm:
pnpm install
```

---

## Rodando o Projeto

Para rodar a aplicação, você precisa de dois processos rodando ao mesmo tempo:

### 1. Servidor PHP (Backend)

Inicie o servidor de desenvolvimento do PHP:

```bash
php -S 127.0.0.1:9100 -t public
```

**Alternativa**: Se preferir usar o Artisan (pode ter problemas de porta):
```bash
php artisan serve
```

### 2. Servidor Vite (Frontend - Tailwind/CSS)

Em outro terminal, inicie o Vite para compilação automática do CSS:

```bash
npm run dev
# ou se usar pnpm:
pnpm run dev
```

Deixe este terminal aberto enquanto você estiver desenvolvendo. Ele irá recompilar seu CSS automaticamente.

---

## Acesso

Com ambos os servidores rodando, abra seu navegador e acesse:

**http://127.0.0.1:9100/**

---

## Comandos Úteis

```bash
# Verificar versão do PHP
php -v

# Limpar cache de configuração
php artisan config:clear

# Executar migrações
php artisan migrate --force

# Gerar nova chave da aplicação
php artisan key:generate

# Instalar dependências PHP
composer install

# Instalar dependências Frontend
npm install
# ou
pnpm install
```

---

## Troubleshooting (Solução de Problemas)

### Problema: "openssl extension is required"

**Solução**: 

Foi implementado um sistema de login completo para o Intrafarma com as seguintes funcionalidades:

1. **Autenticação de Usuários**:
   - Login com email e senha
   - Registro de novos usuários
   - Proteção de rotas com middleware de autenticação
   - Logout seguro

2. **Interface de Usuário**:
   - Tela de login responsiva e moderna
   - Tela de registro de novos usuários
   - Dashboard administrativo após login
   - Página inicial com links para o sistema

3. **Usuário Padrão**:
   - Email: admin@intrafarma.com
   - Senha: admin123

4. **Como Usar**:
   - Execute as migrações: `php artisan migrate`
   - Execute os seeders: `php artisan db:seed`
   - Acesse a aplicação e faça login com as credenciais padrão

## Sistema de Login com Confirmação por Email

Foi implementado um sistema completo de autenticação com confirmação por email e recuperação de senha.

### Funcionalidades
- **Autenticação de Usuários**: Login seguro com validação de credenciais
- **Registro de Novos Usuários**: Formulário de cadastro com validação e confirmação por email
- **Confirmação por Email**: Sistema de verificação de email com reenvio de link
- **Recuperação de Senha**: Sistema completo de recuperação de senha por email
- **Interface Responsiva**: Telas de login, registro, verificação e recuperação com Bootstrap 5
- **Dashboard Protegido**: Página de dashboard acessível apenas para usuários autenticados e com email verificado
- **Logout Seguro**: Funcionalidade de logout com proteção CSRF

### Processo de Registro com Confirmação
1. Usuário preenche o formulário de registro
2. Sistema cria a conta e envia email de verificação
3. Usuário é redirecionado para página de verificação
4. Após clicar no link do email, a conta é verificada
5. Usuário pode acessar o dashboard completo

### Recuperação de Senha
1. Usuário clica em "Esqueceu sua senha?" na tela de login
2. Informa o email cadastrado
3. Recebe link para redefinir a senha
4. Define nova senha e retorna ao login

### Usuário Administrador Padrão
- **Email**: admin@intrafarma.com
- **Senha**: admin123
- **Status**: Email já verificado

### Rotas Disponíveis
- `/` - Página inicial
- `/login` - Tela de login
- `/register` - Tela de registro
- `/email/verify` - Página de verificação de email
- `/password/reset` - Solicitação de recuperação de senha
- `/password/reset/{token}` - Redefinição de senha
- `/dashboard` - Dashboard (requer autenticação e email verificado)

### Configuração de Email

#### Desenvolvimento (Padrão)
Por padrão, o sistema está configurado para **salvar emails em logs** (arquivo `storage/logs/laravel.log`).

#### Produção - Configurar Email Real
Para enviar emails reais, edite o arquivo `.env` e configure as variáveis de email:

**Opção 1: Gmail SMTP (Recomendado para testes)**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seuemail@gmail.com
MAIL_PASSWORD="sua_senha_de_app"  # Use senha de app, não a senha normal
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=seuemail@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

**Como obter senha de app do Gmail:**
1. Ative a verificação em duas etapas na sua conta Google
2. Acesse: https://myaccount.google.com/apppasswords
3. Crie uma senha de app para "Mail"
4. Use essa senha no `MAIL_PASSWORD` (coloque entre aspas se tiver espaços)

**Opção 2: Outros serviços**
- **Mailgun**: Configure `MAIL_HOST=smtp.mailgun.org`
- **Mailtrap**: Configure `MAIL_HOST=smtp.mailtrap.io` (ideal para testes)
- **Outlook**: Configure `MAIL_HOST=smtp.office365.com`

#### Limpar cache após configurar email
```bash
php artisan config:clear
php artisan cache:clear
```

#### Testar configuração de email
1. Registre um novo usuário no sistema
2. Verifique se o email de verificação foi enviado
3. Em desenvolvimento, verifique os logs: `storage/logs/laravel.log`
4. Em produção, verifique a caixa de entrada do email

### Como Testar o Sistema Completo

#### 1. Preparação Inicial
```bash
# Limpar cache
php artisan config:clear
php artisan cache:clear

# Executar migrações
php artisan migrate --force

# Criar usuário administrador
php artisan db:seed
```

#### 2. Testar Login
- Acesse: http://127.0.0.1:9100/login
- Faça login com: admin@intrafarma.com / admin123
- Você será redirecionado para o dashboard

#### 3. Testar Registro de Novo Usuário
- Acesse: http://127.0.0.1:9100/register
- Cadastre um novo usuário com seu email real
- Verifique o email de confirmação (logs ou email real)
- Clique no link de verificação
- Faça login com o novo usuário

#### 4. Testar Recuperação de Senha
- Na tela de login, clique em "Esqueceu sua senha?"
- Digite seu email
- Verifique o email com o link de recuperação
- Redefina a senha
- Faça login com a nova senha

#### 5. Verificar Logs de Email (Desenvolvimento)
```bash
# Ver últimos emails enviados
tail -n 50 storage/logs/laravel.log | grep "MAIL"

# Ou abra o arquivo completo
notepad storage/logs/laravel.log
```O projeto já inclui um `php.ini` configurado. Se o erro persistir:
1. Verifique se está usando o PHP correto: `php -v` (deve mostrar 8.2.29)
2. Verifique se o `php.ini` existe em `tools/php82/bin/php.ini`

### Problema: "could not find driver" (pgsql)

**Solução**: 
1. Execute: `php artisan config:clear`
2. Habilite as extensões `pgsql` e `pdo_pgsql` no `php.ini`
3. Verifique se o `.env` tem: `DB_CONNECTION=pgsql` e credenciais corretas

### Problema: Servidor não inicia na porta 8000-8010

**Solução**: Use o servidor embutido do PHP:
```bash
php -S 127.0.0.1:9100 -t public
```

### Problema: "composer install" falha

**Solução**: 
1. Verifique se o Composer está instalado: `composer --version`
2. Se usar PHP do sistema, atualize para PHP 8.2+
3. Use o PHP portátil incluído no projeto

### Problema: Erro de permissão no Windows

**Solução**: 
1. Execute o terminal como Administrador
2. Ou mude as permissões da pasta do projeto

### Problema: "npm run dev" não funciona

**Solução**:
1. Verifique se o Node.js está instalado: `node --version`
2. Execute: `npm install` ou `pnpm install`
3. Tente: `npm run build` para compilação estática

### Problema: Email não está sendo enviado

**Solução**:
1. Verifique a configuração no `.env` (MAIL_MAILER, MAIL_HOST, etc.)
2. Limpe o cache: `php artisan config:clear && php artisan cache:clear`
3. Em desenvolvimento, verifique os logs: `storage/logs/laravel.log`
4. Verifique se a senha do email está entre aspas se tiver espaços
5. Para Gmail, certifique-se de usar "senha de app", não a senha normal

### Problema: Usuário não consegue fazer login após registro

**Solução**:
1. Verifique se o email foi confirmado (link no email ou logs)
2. Verifique se o middleware de verificação está aplicado corretamente
3. Verifique os logs para erros de autenticação
4. Tente fazer logout e login novamente

---

## Rotas do Sistema

### Rotas Públicas
- `/` - Página inicial
- `/login` - Tela de login
- `/register` - Tela de registro de novos usuários
- `/password/reset` - Solicitação de recuperação de senha
- `/password/reset/{token}` - Redefinição de senha

### Rotas de Autenticação
- `/email/verify` - Página de verificação de email (requer login)
- `/email/verify/{id}/{hash}` - Confirmação de email via link
- `/email/resend` - Reenvio de email de verificação

### Rotas Protegidas
- `/dashboard` - Dashboard principal (requer login + email verificado)
- `/logout` - Logout do sistema

---

## Funcionalidades Implementadas

### ✅ Autenticação Completa
- Login com email e senha
- Registro de novos usuários com validação
- Confirmação de email obrigatória
- Recuperação de senha por email
- Logout seguro com proteção CSRF

### ✅ Interface Responsiva
- Layout moderno com Bootstrap 5
- Telas de login/registro profissionais
- Dashboard administrativo
- Páginas de erro personalizadas

### ✅ Sistema de Email
- Configuração flexível (log ou SMTP)
- Templates de email profissionais
- Suporte para Gmail, Mailgun, Mailtrap
- Logs detalhados em desenvolvimento

### ✅ Segurança
- Proteção contra CSRF
- Hash de senhas com bcrypt
- Middleware de autenticação
- Middleware de verificação de email
- Sessões seguras

### ✅ Banco de Dados
- PostgreSQL
- Schema SQL completo (opcional) e migrações
- Sistema de cache integrado
- Jobs para processamento assíncrono

---

## Arquivos de Configuração Importantes

### `.env` - Configurações do Ambiente
Configure aqui: banco de dados, email, debug, app key, etc.

### `config/mail.php` - Configuração de Email
Definições de servidores SMTP e drivers de email

### `config/auth.php` - Configuração de Autenticação
Guards, providers e configurações de autenticação

### `routes/web.php` - Rotas da Aplicação
Todas as rotas HTTP do sistema

### `database/migrations/` - Migrações do Banco
Estrutura das tabelas do banco de dados

---

## Próximos Passos (Sugestões)

1. **Implementar CRUD de Medicamentos**
   - Cadastro de produtos
   - Controle de estoque
   - Gestão de lotes e validades

2. **Sistema de Permissões**
   - Diferentes níveis de usuário (admin, funcionário, etc.)
   - Controle de acesso por rotas

3. **Relatórios e Dashboard**
   - Gráficos de estoque
   - Relatórios de movimentação
   - Alertas de validade

4. **Melhorias de Interface**
   - Tema escuro/claro
   - Internacionalização
   - Mobile-first responsivo

5. **Integrações**
   - Notificações por WhatsApp
   - Backup automático
   - Exportação de relatórios

---

## Estrutura do Projeto

```
intrafarma/
├── app/
│   ├── Http/
│   │   └── Controllers/     # Controladores (Auth, Dashboard)
│   ├── Models/              # Modelos (User)
│   └── Providers/         # Provedores de serviço
├── config/                  # Arquivos de configuração
│   ├── app.php             # Configurações principais
│   ├── auth.php            # Autenticação
│   ├── mail.php            # Configuração de email
│   └── database.php        # Banco de dados
├── database/
│   ├── migrations/         # Migrações do banco
│   ├── seeders/           # Seeders (AdminUserSeeder)
│   ├── scripts/           # Scripts SQL adicionais
│   └── schema_farmacia.sql    # Schema PostgreSQL
├── public/                  # Arquivos públicos
│   ├── index.php          # Entrada da aplicação
│   └── favicon.ico        # Ícone do site
├── resources/
│   ├── views/             # Templates Blade
│   │   ├── auth/         # Telas de auth (login, register, verify)
│   │   ├── layouts/      # Layouts base (app)
│   │   └── dashboard.blade.php  # Dashboard
│   ├── css/              # Arquivos CSS
│   └── js/               # Arquivos JavaScript
├── routes/
│   └── web.php           # Rotas da aplicação
├── storage/
│   ├── logs/             # Logs da aplicação
│   └── framework/        # Cache e sessões
├── tools/
│   └── php82/            # PHP portátil 8.2
│       └── bin/          # PHP executável
├── .env                  # Configurações do ambiente
├── .env.example         # Exemplo de configurações
├── composer.json        # Dependências PHP
├── package.json         # Dependências Node.js
└── README.md           # Este arquivo
```
