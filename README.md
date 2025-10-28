# Projeto Farmácia (IntraFarma)

Este é um sistema de gestão de estoque de medicamentos para controle de entradas, saídas, lotes e validades, construído com Laravel.

Este guia destina-se à configuração e execução do projeto em um ambiente de desenvolvimento local usando XAMPP e uma instalação manual do PostgreSQL.

## Stack do Ambiente

* Servidor: Apache (via XAMPP)
* PHP: (via XAMPP)
* Banco de Dados: PostgreSQL (instalado manualmente)
* Backend: Laravel
* Frontend: Tailwind CSS (compilado com Vite)
* Gerenciadores de Pacotes: Composer (PHP) e NPM (Node.js)

---

## Pré-requisitos

Antes de começar, garanta que você tenha os seguintes softwares instalados e funcionando em seu sistema:

1. XAMPP: (Contém Apache e PHP). Baixe em https://www.apachefriends.org/index.html
2. PostgreSQL: (O servidor de banco de dados). Baixe em https://www.postgresql.org/download/
3. Git: Para clonar o repositório.
4. Composer: O gerenciador de pacotes para PHP. Baixe em https://getcomposer.org/download/
5. Node.js (com npm): Para o Tailwind/Vite. Baixe a versão LTS em https://nodejs.org/

---

## Guia de Instalação (Passo a Passo)

Siga estes passos para configurar o ambiente e rodar o projeto.

### 1. Clonar o Repositório

Primeiro, clone o projeto do GitHub para sua pasta de projetos (ex: C:\Projetos\):

git clone https://github.com/vaiserk/intrafarma.git
cd intrafarma

---

### 2. Configurar o Banco de Dados (PostgreSQL)

Este projeto não usa migrations do Laravel para criar o banco inicial. Ele usa um script SQL pronto.

Abra seu gerenciador de banco de dados (pgAdmin, DBeaver, etc.).

Crie um novo banco de dados. Ex: farmacia_db.

Abra o arquivo database/schema_farmacia.sql que está no projeto.

Execute o conteúdo completo deste arquivo dentro do banco farmacia_db que você acabou de criar.

Isso criará todas as tabelas, views, funções e triggers necessários.

---

### 3. Configurar o Apache (XAMPP)

Precisamos configurar o XAMPP para que um domínio (ex: farmacia.localhost) aponte para a pasta public do seu projeto.

#### 3.1. Ativar Virtual Hosts (VHosts)

Abra o painel do XAMPP, clique em "Config" do Apache e abra o arquivo httpd.conf.

Procure (Ctrl+F) pela linha: 
#Include conf/extra/httpd-vhosts.conf

Apague o # do início para "descomentar" a linha.

Salve o httpd.conf e reinicie o Apache (Stop/Start).

#### 3.2. Adicionar seu Site ao httpd-vhosts.conf

Abra o arquivo: C:\xampp\apache\conf\extra\httpd-vhosts.conf.

Adicione este bloco no final do arquivo (ajuste o caminho DocumentRoot se necessário):

<VirtualHost *:80>
    ServerName farmacia.localhost
    DocumentRoot "C:/Projetos/intrafarma/public"

    <Directory "C:/Projetos/intrafarma/public">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

#### 3.3. Editar Arquivo hosts do Windows

Abra o Bloco de Notas como Administrador.

Vá em Arquivo > Abrir....

Navegue até C:\Windows\System32\drivers\etc.

Mude o filtro de "Documentos de Texto (*.txt)" para "Todos os arquivos (*.*)".

Abra o arquivo chamado hosts.

Adicione esta linha no final do arquivo:

127.0.0.1 farmacia.localhost

Salve o arquivo hosts e reinicie o Apache no XAMPP mais uma vez.

---

### 4. Configurar o Projeto (Laravel)

#### 4.1. Criar o Arquivo .env

Na raiz do projeto (intrafarma), copie o arquivo de exemplo:

copy .env.example .env

#### 4.2. Editar o .env

Abra o arquivo .env que você acabou de criar e configure-o para se conectar ao seu banco:

APP_URL=http://farmacia.localhost

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432            # (ou a porta do seu PostgreSQL, ex: 5433)
DB_DATABASE=farmacia_db # (O banco que você criou no Passo 2)
DB_USERNAME=postgres    # (Seu usuário real do PostgreSQL)
DB_PASSWORD=sua_senha_real_aqui # (Sua senha real do PostgreSQL)

SESSION_DRIVER=file

#### 4.3. Gerar a Chave do App

No terminal, na raiz do projeto, rode:

php artisan key:generate

---

### 5. Instalar Dependências

Atenção: Se o composer ou npm falharem por "comando não reconhecido", reinicie seu terminal. Se persistir, instale-os (veja os Pré-requisitos).

#### 5.1. Dependências do PHP (Backend)

Rode o Composer para instalar a pasta vendor/:

composer install

Erro Comum: Se o composer install falhar por falta da extensão zip, abra seu C:\xampp\php\php.ini, procure (Ctrl+F) por ;extension=zip e apague o ponto-e-vírgula (;) do início. Salve o arquivo e tente novamente.

#### 5.2. Dependências do JS/CSS (Frontend)

Rode o NPM para instalar o Tailwind (pasta node_modules/):

npm install

---

## Rodando o Projeto

Para rodar a aplicação, você precisa de dois processos rodando ao mesmo tempo:

1. O Servidor Apache (PHP):

No painel do XAMPP, garanta que o módulo Apache esteja rodando (botão "Start" verde).

2. O Servidor Vite (Tailwind/CSS):

Abra um terminal na raiz do projeto (C:\Projetos\intrafarma).

*para instalar as dependencias do projeto:

-pnpm install

Rode o comando para iniciar o projeto:
-pnpm run dev


Deixe este terminal aberto enquanto você estiver programando. Ele irá recompilar seu CSS automaticamente.

---

## Acesso

Com o Apache e o npm run dev rodando, abra seu navegador e acesse:

http://farmacia.localhost
