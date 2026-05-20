<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'priority' => ['required', Rule::in(['baixa', 'media', 'alta'])],
            'status' => ['required', Rule::in(['pendente', 'andamento', 'concluida'])],
            'due_date' => ['required', 'date', 'after_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'O título da tarefa é obrigatório.',
            'title.min' => 'O título deve ter no mínimo 3 caracteres.',
            'title.max' => 'O título não pode ter mais de 255 caracteres.',
            'description.max' => 'A descrição não pode ter mais de 1000 caracteres.',
            'priority.required' => 'A prioridade é obrigatória.',
            'priority.in' => 'Prioridade inválida. Use: baixa, media ou alta.',
            'status.required' => 'O status é obrigatório.',
            'status.in' => 'Status inválido. Use: pendente, andamento ou concluida.',
            'due_date.required' => 'A data limite é obrigatória.',
            'due_date.date' => 'A data limite deve ser uma data válida.',
            'due_date.after_or_equal' => 'A data limite não pode ser anterior a hoje.',
        ];
    }
}
