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

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('responsible')) {
            $query->whereHas('user', function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->responsible . '%');
            });
        }

        $tasks = $query->orderBy('due_date')->paginate(10);

        return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', Rule::in(['baixa', 'media', 'alta'])],
            'status' => ['required', Rule::in(['pendente', 'andamento', 'concluida'])],
            'due_date' => ['required', 'date'],
        ]);

        $task = Task::create(array_merge($validated, [
            'user_id' => $request->user()->id,
        ]));

        return response()->json($task->load('user'), 201);
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
        $this->authorizeTask($task);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority' => ['required', Rule::in(['baixa', 'media', 'alta'])],
            'status' => ['required', Rule::in(['pendente', 'andamento', 'concluida'])],
            'due_date' => ['required', 'date'],
        ]);

        $task->update($validated);

        return response()->json($task->load('user'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): JsonResponse
    {
        $this->authorizeTask($task);

        $task->delete();

        return response()->json(null, 204);
    }

    protected function authorizeTask(Task $task): void
    {
        if ($task->user_id !== auth()->id()) {
            abort(403, 'You are not authorized to access this task.');
        }
    }
}
