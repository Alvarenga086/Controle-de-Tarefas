# Controle de Tarefas

Este projeto é uma aplicação Laravel básica de controle de tarefas de equipe.
O sistema inclui autenticação de usuários e uma API REST para cadastro, listagem, atualização e remoção de tarefas.

## Requisitos atendidos

- Laravel com autenticação de usuários
- Banco de dados SQLite local
- API REST para tarefas
- Endpoints de cadastro, listagem, atualização e remoção
- Filtros e paginação na listagem
- Relação entre tarefas e usuário responsável

> O campo "responsável" é representado pelo usuário autenticado que cria a tarefa. O sistema só permite que cada usuário acesse e gerencie suas próprias tarefas.

## Como executar

1. Copie o arquivo de ambiente:
   ```powershell
   copy .env.example .env
   ```
2. Instale as dependências PHP:
   ```powershell
   composer install
   ```
3. Gere a chave da aplicação:
   ```powershell
   php artisan key:generate
   ```
4. Crie o arquivo SQLite se necessário:
   ```powershell
   if (-not (Test-Path database\database.sqlite)) { New-Item database\database.sqlite -ItemType File }
   ```
5. Execute as migrations:
   ```powershell
   php artisan migrate
   ```
6. Inicie o servidor local:
   ```powershell
   php artisan serve
   ```

7. Abra o navegador em:
   ```text
   http://127.0.0.1:8000
   ```

## Credenciais de teste

- Email: `daniel@teste.com`
- Senha: `daniel123`

## Como testar a autenticação

1. Acesse `http://127.0.0.1:8000/login`.
2. Faça login com as credenciais acima.
3. Após o login, os endpoints da API `/api/tasks` estarão disponíveis.

## Endpoints da API

Todos os endpoints abaixo exigem autenticação:

- `GET /api/tasks`
  - Lista tarefas com paginação.
  - Parâmetros opcionais: `search`, `status`, `responsible`, `priority`, `sort_by`, `sort_direction`, `page`.
- `GET /api/tasks/{task}`
  - Retorna os detalhes de uma tarefa.
- `POST /api/tasks`
  - Cria uma nova tarefa.
  - Campos obrigatórios: `title`, `priority`, `status`, `due_date`.
  - Campo opcional: `description`.
- `PUT /api/tasks/{task}`
  - Atualiza uma tarefa existente.
- `DELETE /api/tasks/{task}`
  - Remove uma tarefa.

## Modelo de dados

A tabela `tasks` possui os campos:

- `title`
- `description`
- `user_id` (responsável)
- `priority` (`baixa`, `media`, `alta`)
- `status` (`pendente`, `andamento`, `concluida`)
- `due_date`
- `created_at`, `updated_at`

## O que foi implementado

- Autenticação de usuário Laravel
- CRUD completo de tarefas
- Associação de tarefa ao usuário autenticado como responsável
- Filtros de busca por título, status, responsável e prioridade
- Ordenação e paginação de resultados
- Retorno JSON padronizado para a API

## O que ainda quero melhorar

Estou deixando o projeto funcional e pronto para entrega, mas ainda vou continuar desenvolvendo melhorias como:

- interface para criar e editar tarefas diretamente no front-end
- atribuição de tarefas para outros usuários
- autenticação de API com tokens (Sanctum)
- testes automatizados para garantir estabilidade
- documentação ainda mais completa para uso e deploy

Essas melhorias são próximas etapas que vou implementar para deixar o sistema mais completo.
