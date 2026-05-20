# Decisões Técnicas - Controle de Tarefas

## Arquitetura

### Stack
- **Framework**: Laravel 13.8
- **Banco de Dados**: SQLite (desenvolvimento) ou MySQL (produção)
- **API**: REST com autenticação de sessão
- **Autenticação**: Laravel Breeze (scaffold built-in)

### Estrutura de Pastas
```
app/
├── Http/
│   ├── Controllers/
│   │   └── TaskController.php     # Controlador CRUD
│   └── Requests/
│       └── StoreTaskRequest.php   # Validações customizadas
├── Models/
│   ├── Task.php                   # Modelo de tarefa
│   └── User.php                   # Modelo de usuário
└── Providers/
    └── AppServiceProvider.php     # Provedor de serviços
```

## Decisões de Design

### 1. API em `routes/web.php` vs `routes/api.php`

**Decisão**: API em `routes/web.php` com prefixo `/api`.

**Motivo**:
- Simplifica autenticação usando sessão Laravel
- Evita necessidade de tokens de API (Sanctum)
- Ideal para teste técnico com escopo controlado
- Mantém CSRF protection automaticamente

**Alternativa rejeitada**: `routes/api.php` com Sanctum
- Seria mais complexo para um projeto de teste
- Exigiria token/chave de API

### 2. Validação de Data Limite

**Decisão**: `after_or_equal:today`

**Motivo**:
- Evita criar tarefas com prazos passados
- Validação de negócio importante
- Mensagem de erro clara para o usuário

### 3. Relacionamento User-Task

**Decisão**: `Task belongsTo User` e `User hasMany Task` com `onDelete('cascade')`.

**Motivo**:
- Garante que tarefas órfãs sejam removidas
- Sem dados inconsistentes no banco
- Simplifica lógica de autorização

### 4. Autorização em Controller

**Decisão**: Validar `user_id` em cada operação.

**Motivo**:
- Garante que usuários só vejam suas tarefas
- Código explícito e fácil de auditar
- Não depende de policies/gates complexas

**Código**:
```php
if ($task->user_id !== auth()->id()) {
    throw new AuthorizationException('Acesso negado.');
}
```

### 5. Filtros na Listagem

**Decisão**: Múltiplos filtros independentes (search, status, priority, responsible).

**Motivo**:
- Flexibilidade para o usuário
- Queries otimizadas com índices
- Fácil de testar e debugar

**Exemplo**:
```
GET /api/tasks?search=auth&status=andamento&priority=alta
```

### 6. Respostas JSON Padronizadas

**Decisão**: Todas as respostas com `success`, `message` e `data`.

**Motivo**:
- Frontend sabe sempre o status da operação
- Mensagens amigáveis para o usuário
- Fácil de logar erros

**Formato**:
```json
{
  "success": true,
  "message": "Tarefa criada com sucesso.",
  "data": { ... }
}
```

## Segurança

### 1. Validação de Input
- Comprimento mínimo e máximo
- Enums para campos fixos (priority, status)
- Datas validadas

### 2. Autenticação
- Middleware `auth` em todas as rotas de API
- Uso de sessão (não tokens)
- CSRF protection automática

### 3. Autorização
- Verificação de `user_id` antes de criar/editar/deletar
- Mensagens de erro que não revelam estrutura

## Performance

### 1. Índices (Potenciais)
```sql
CREATE INDEX idx_tasks_user_id ON tasks(user_id);
CREATE INDEX idx_tasks_status ON tasks(status);
CREATE INDEX idx_tasks_priority ON tasks(priority);
```

### 2. Eager Loading
```php
Task::with('user')  // Evita N+1 queries
```

### 3. Paginação
- Padrão: 10 tarefas por página
- Customizável via `per_page`

## Testes

### Cobertura Esperada
- ✅ CRUD básico (Create, Read, Update, Delete)
- ✅ Validação de campos
- ✅ Autorização (própria vs alheia)
- ✅ Filtros e busca
- ✅ Paginação

### Não Implementado (Escopo)
- Testes automatizados (PHPUnit)
- Cache (Redis)
- Logging avançado
- Auditoria de alterações

## Melhorias Futuras

1. **Frontend**: Vue.js com Inertia
2. **Autenticação**: API tokens com Sanctum
3. **Testes**: PHPUnit com Factory/Seeder
4. **Documentação**: Swagger/OpenAPI
5. **Features**: Comentários, anexos, notificações
6. **Performance**: Caching, índices, queue

## Padrões Seguidos

- **PSR-4**: Autoloading de classes
- **RESTful**: Verbos HTTP corretos (GET, POST, PUT, DELETE)
- **Laravel**: Conventions over configuration
- **SOLID**: Separação de responsabilidades

## Dependências

### Produção
- `laravel/framework: ^13.8`
- `laravel/tinker: ^3.0`

### Desenvolvimento
- `laravel/breeze: ^2.4`
- `laravel/pint: ^1.27`
- `pestphp/pest: ^4.7`

## Notas Finais

Este projeto foi desenvolvido com foco em:
1. **Clareza**: Código legível e bem estruturado
2. **Segurança**: Validações e autorização
3. **Funcionalidade**: CRUD completo e testável
4. **Documentação**: READMEs e exemplos de teste
