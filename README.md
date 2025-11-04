# Projeto Farmácia (IntraFarma)

Este é um sistema de gestão de estoque de medicamentos para controle de entradas, saídas, lotes e validades, construído com Laravel.

Este guia destina-se à configuração e execução do projeto em um ambiente de desenvolvimento local.

## Stack do Ambiente

* PHP: 8.2+ (incluído no projeto como versão portátil)
* Banco de Dados: SQLite (arquivo local)
* Backend: Laravel 11
* Frontend: Tailwind CSS (compilado com Vite)
* Gerenciadores de Pacotes: Composer (PHP) e NPM/PNPM (Node.js)

---

## Pré-requisitos

Antes de começar, garanta que você tenha os seguintes softwares instalados:

1. **Git**: Para clonar o repositório
2. **Composer**: O gerenciador de pacotes para PHP. Baixe em https://getcomposer.org/download/
3. **Node.js (com npm/pnpm)**: Para o Tailwind/Vite. Baixe a versão LTS em https://nodejs.org/

**Nota**: O projeto inclui uma versão portátil do PHP 8.2 configurada, então você não precisa instalar PHP separadamente.

---

## Guia de Instalação (Passo a Passo)

Siga estes passos para configurar o ambiente e rodar o projeto.

### 1. Clonar o Repositório

Clone o projeto para sua pasta de projetos:

```bash
git clone https://github.com/vaiserk/intrafarma.git
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

#### 3.2. Configurar Banco de Dados SQLite

O projeto está configurado para usar SQLite. O arquivo `.env` já está configurado corretamente, mas você pode verificar:

```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

#### 3.3. Gerar Chave da Aplicação

```bash
php artisan key:generate
```

#### 3.4. Executar Migrações

Crie as tabelas do banco de dados:

```bash
php artisan migrate --force
```

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

O sistema está integrado com o banco de dados existente e pronto para ser expandido com novas funcionalidades.O projeto já inclui um `php.ini` configurado. Se o erro persistir:
1. Verifique se está usando o PHP correto: `php -v` (deve mostrar 8.2.29)
2. Verifique se o `php.ini` existe em `tools/php82/bin/php.ini`

### Problema: "could not find driver" (SQLite)

**Solução**: 
1. Execute: `php artisan config:clear`
2. Verifique se o `.env` tem: `DB_CONNECTION=sqlite`
3. Verifique se existe o arquivo `database/database.sqlite`

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

---

## Estrutura do Projeto

```
intrafarma/
├── app/                 # Código da aplicação Laravel
├── database/           # Migrações e banco SQLite
├── public/             # Arquivos públicos (index.php)
├── resources/          # Views, CSS, JS
├── tools/              # PHP portátil 8.2
│   └── php82/bin/      # Executável e configuração PHP
├── .env                # Configurações do ambiente
└── composer.json       # Dependências PHP
```
