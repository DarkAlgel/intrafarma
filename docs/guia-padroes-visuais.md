# Guia de Padrões Visuais (Intrafarma)

## Cores, tipografia e espaçamento
- Paleta: gradiente roxo na sidebar (`resources/views/layouts/app.blade.php:24`).
- Tipografia: fonte `Nunito` e `font-sans` (`resources/views/layouts/app.blade.php:15`, `resources/css/app.css:8`).
- Espaçamento: `px-6 py-4` em cabeçalhos e cartões (`resources/views/estoque/index.blade.php:50`, `resources/views/fornecedores/index.blade.php:77`).
- Badges de status: `status-success`, `status-warning`, `status-danger` (`resources/views/layouts/app.blade.php:155`).

## Estrutura de layout e componentes
- Layout base: `layouts.app` com Sidebar fixa e Main flexível (`resources/views/layouts/app.blade.php:205`).
- Sidebar e navegação: `.sidebar`, `.nav-link`, estado `.active` (`resources/views/layouts/app.blade.php:23`, `resources/views/layouts/app.blade.php:48`).
- Botões: `.btn-primary`, `.btn-secondary` (`resources/views/layouts/app.blade.php:53`, `resources/views/layouts/app.blade.php:69`).
- Tabelas: `.table-header`, `.table-cell`, `.table-row` (`resources/views/layouts/app.blade.php:85`, `resources/views/layouts/app.blade.php:94`).
- Cards: `.card` para contêineres principais (`resources/views/layouts/app.blade.php:105`).

## Padrões de interação e comportamento
- Links de navegação com realce em hover e ativo (`resources/views/layouts/app.blade.php:43`, `resources/views/layouts/app.blade.php:48`).
- Ordenação: cabeçalhos de tabela clicáveis com ícone de direção (`resources/views/fornecedores/index.blade.php:86`).
- Paginação: `links()` padrão Tailwind/Laravel (`resources/views/fornecedores/index.blade.php:152`).
- Toasts de feedback: sucesso/erro com animação (`resources/views/layouts/app.blade.php:210`).
- Responsividade: `md:ml-64`, `overflow-x-auto` em tabelas (`resources/views/fornecedores/index.blade.php:82`).

## Diretrizes de acessibilidade
- Cabeçalhos hierárquicos (`h1` para título da página) (`resources/views/fornecedores/index.blade.php:49`).
- Contraste suficiente nas badges e botões (`resources/views/layouts/app.blade.php:155`, `resources/views/layouts/app.blade.php:53`).
- Navegação semântica e tabela com `thead/tbody` (`resources/views/fornecedores/index.blade.php:83`).

## Campos relevantes por contexto
- Fornecedores: `nome`, `tipo`, `contato` (`resources/views/fornecedores/index.blade.php:114`).
- Tipos suportados: enum `fornecedor_tipo` (`database/scripts/schema_farmacia.sql:31`).
- Chaves e relações: `entradas.fornecedor_id → fornecedores.id` (`database/scripts/schema_farmacia.sql:686`).

## Convenções de código
- Controllers retornam `view()` com dados paginados (`app/Http/Controllers/FornecedorController.php:13`).
- Rotas protegidas por `auth` (`routes/web.php:29`).
- Estilos centralizados em `layouts.app` e Tailwind (`resources/views/layouts/app.blade.php:17`).