# Guia de Teste da API

Este documento fornece exemplos práticos para testar a API de tarefas.

## 1. Criar uma tarefa

**Requisição:**
```bash
POST http://127.0.0.1:8000/api/tasks
Content-Type: application/json

{
  "title": "Implementar autenticação",
  "description": "Adicionar sistema de login",
  "priority": "alta",
  "status": "andamento",
  "due_date": "2026-06-15"
}
```

**Resposta esperada (201 Created):**
```json
{
  "success": true,
  "message": "Tarefa criada com sucesso.",
  "data": {
    "id": 1,
    "title": "Implementar autenticação",
    "description": "Adicionar sistema de login",
    "priority": "alta",
    "status": "andamento",
    "due_date": "2026-06-15",
    "user_id": 1,
    "user": {
      "id": 1,
      "name": "Usuario Teste",
      "email": "teste@example.com"
    },
    "created_at": "2026-05-20T21:45:00.000000Z",
    "updated_at": "2026-05-20T21:45:00.000000Z"
  }
}
```

## 2. Listar tarefas com filtros

**Requisição simples:**
```
GET http://127.0.0.1:8000/api/tasks
```

**Com busca:**
```
GET http://127.0.0.1:8000/api/tasks?search=autenticação
```

**Com filtro de status:**
```
GET http://127.0.0.1:8000/api/tasks?status=andamento
```

**Com filtro de prioridade:**
```
GET http://127.0.0.1:8000/api/tasks?priority=alta
```

**Com múltiplos filtros:**
```
GET http://127.0.0.1:8000/api/tasks?search=auth&status=andamento&priority=alta&sort_by=due_date&sort_direction=asc
```

**Resposta esperada (200 OK):**
```json
{
  "data": [
    {
      "id": 1,
      "title": "Implementar autenticação",
      "description": "Adicionar sistema de login",
      "priority": "alta",
      "status": "andamento",
      "due_date": "2026-06-15",
      "user": {
        "id": 1,
        "name": "Usuario Teste"
      },
      "created_at": "2026-05-20T21:45:00.000000Z"
    }
  ],
  "links": {
    "first": "http://127.0.0.1:8000/api/tasks?page=1",
    "last": "http://127.0.0.1:8000/api/tasks?page=1",
    "prev": null,
    "next": null
  },
  "meta": {
    "current_page": 1,
    "from": 1,
    "last_page": 1,
    "path": "http://127.0.0.1:8000/api/tasks",
    "per_page": 10,
    "to": 1,
    "total": 1
  }
}
```

## 3. Atualizar uma tarefa

**Requisição:**
```bash
PUT http://127.0.0.1:8000/api/tasks/1
Content-Type: application/json

{
  "title": "Implementar autenticação com 2FA",
  "description": "Adicionar sistema de login com autenticação em dois fatores",
  "priority": "alta",
  "status": "concluida",
  "due_date": "2026-06-10"
}
```

**Resposta esperada (200 OK):**
```json
{
  "success": true,
  "message": "Tarefa atualizada com sucesso.",
  "data": {
    "id": 1,
    "title": "Implementar autenticação com 2FA",
    "priority": "alta",
    "status": "concluida",
    "due_date": "2026-06-10"
  }
}
```

## 4. Deletar uma tarefa

**Requisição:**
```
DELETE http://127.0.0.1:8000/api/tasks/1
```

**Resposta esperada (200 OK):**
```json
{
  "success": true,
  "message": "Tarefa removida com sucesso."
}
```

## 5. Testes de erro esperados

### Validação: título muito curto
```json
{
  "title": "AB"
}
```

**Resposta (422 Unprocessable Entity):**
```json
{
  "success": false,
  "message": "Erro de validação.",
  "errors": {
    "title": ["O título deve ter no mínimo 3 caracteres."]
  }
}
```

### Validação: prioridade inválida
```json
{
  "priority": "urgente"
}
```

**Resposta (422 Unprocessable Entity):**
```json
{
  "success": false,
  "message": "Erro de validação.",
  "errors": {
    "priority": ["Prioridade inválida. Use: baixa, media ou alta."]
  }
}
```

### Não autorizado
```
DELETE http://127.0.0.1:8000/api/tasks/999 (tarefa de outro usuário)
```

**Resposta (403 Forbidden):**
```json
{
  "success": false,
  "message": "Você não tem permissão para deletar esta tarefa."
}
```

## Teste rápido com curl

```powershell
# Criando tarefa
$body = @{
    title = "Tarefa de teste"
    description = "Teste da API"
    priority = "media"
    status = "pendente"
    due_date = "2026-06-30"
} | ConvertTo-Json

curl -X POST http://127.0.0.1:8000/api/tasks `
  -H "Content-Type: application/json" `
  -d $body

# Listando tarefas
curl http://127.0.0.1:8000/api/tasks
```

## Ferramentas recomendadas para teste

- **Postman** (https://www.postman.com/downloads/)
- **Insomnia** (https://insomnia.rest/)
- **curl** (built-in no PowerShell)
- **Thunder Client** (extensão VS Code)
