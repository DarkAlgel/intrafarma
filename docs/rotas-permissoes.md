# Rotas e Permissões do Portal do Paciente

## Rotas
- `/me/medicamentos` (`paciente.medicamentos`) — lista de medicamentos disponíveis
- `/me/historico` (`paciente.historico`) — histórico de dispensações do paciente
- `/me/configuracoes` (`paciente.configuracoes`) — acesso às configurações de conta

## Permissões
- `paciente_ver_medicamentos` — exibe e permite acessar a lista de medicamentos
- `paciente_ver_historico` — exibe e permite acessar o histórico do paciente
- `ver_minha_conta` e `alterar_senha` — configuram acesso às páginas de conta e senha

## Navbar
- Ítens são exibidos dinamicamente conforme as permissões do usuário autenticado
- Rotas administrativas usam: `ver_estoque`, `ver_dispensacoes`, `gerenciar_usuarios`

## Proteção de Acesso
- Todas as rotas acima requerem autenticação
- Middleware `perm:<codigo>` bloqueia acesso direto via URL (403) quando não permitido