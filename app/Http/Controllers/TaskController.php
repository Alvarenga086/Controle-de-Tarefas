<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Task::with('user');

        // Busca por título
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        // Filtro por status
        if ($request->filled('status')) {
            $status = $request->status;
            if (in_array($status, ['pendente', 'andamento', 'concluida'])) {
                $query->where('status', $status);
            }
        }

        // Filtro por responsável (nome do usuário)
        if ($request->filled('responsible')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->responsible . '%');
            });
        }

        // Filtro por prioridade
        if ($request->filled('priority')) {
            $priority = $request->priority;
            if (in_array($priority, ['baixa', 'media', 'alta'])) {
                $query->where('priority', $priority);
            }
        }

        // Ordenação customizável
        $sortBy = $request->input('sort_by', 'due_date');
        $sortDirection = $request->input('sort_direction', 'asc');
        
        if (in_array($sortBy, ['due_date', 'priority', 'status', 'created_at'])) {
            $query->orderBy($sortBy, in_array($sortDirection, ['asc', 'desc']) ? $sortDirection : 'asc');
        }

        $tasks = $query->paginate($request->input('per_page', 10));

        return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'title' => ['required', 'string', 'min:3', 'max:255'],
                'description' => ['nullable', 'string', 'max:1000'],
                'priority' => ['required', Rule::in(['baixa', 'media', 'alta'])],
                'status' => ['required', Rule::in(['pendente', 'andamento', 'concluida'])],
                'due_date' => ['required', 'date', 'after_or_equal:today'],
            ], [
                'title.required' => 'O título da tarefa é obrigatório.',
                'title.min' => 'O título deve ter no mínimo 3 caracteres.',
                'priority.in' => 'Prioridade inválida. Use: baixa, media ou alta.',
                'status.in' => 'Status inválido. Use: pendente, andamento ou concluida.',
                'due_date.after_or_equal' => 'A data limite não pode ser anterior a hoje.',
            ]);

            $task = Task::create(array_merge($validated, [
                'user_id' => $request->user()->id,
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Tarefa criada com sucesso.',
                'data' => $task->load('user'),
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro de validação.',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task): JsonResponse
    {
        $this->authorizeTask($task);

        return response()->json($task->load('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task): JsonResponse
    {
        try {
            $this->authorizeTask($task);

            $validated = $request->validate([
                'title' => ['required', 'string', 'min:3', 'max:255'],
                'description' => ['nullable', 'string', 'max:1000'],
                'priority' => ['required', Rule::in(['baixa', 'media', 'alta'])],
                'status' => ['required', Rule::in(['pendente', 'andamento', 'concluida'])],
                'due_date' => ['required', 'date', 'after_or_equal:today'],
            ]);

            $task->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Tarefa atualizada com sucesso.',
                'data' => $task->load('user'),
            ]);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para editar esta tarefa.',
            ], 403);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): JsonResponse
    {
        try {
            $this->authorizeTask($task);

            $task->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tarefa removida com sucesso.',
            ], 200);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para deletar esta tarefa.',
            ], 403);
        }
    }

    protected function authorizeTask(Task $task): void
    {
        if ($task->user_id !== auth()->id()) {
            throw new \Illuminate\Auth\Access\AuthorizationException(
                'Você não tem permissão para acessar esta tarefa.'
            );
        }
    }
}
