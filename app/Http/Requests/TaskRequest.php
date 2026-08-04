<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Shared by both store and update so the create and edit forms can never
 * validate differently.
 */
class TaskRequest extends FormRequest
{
    /**
     * The route itself requires login (see routes/web.php); tasks have no
     * owner column, so any logged-in user may manage any task — a single
     * shared list, not per-user data.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:5000'],
            'due_date' => ['nullable', 'date'],
            'priority' => ['required', Rule::in(Task::PRIORITIES)],
            'is_done' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Unchecked checkboxes are simply absent from the payload, so normalise
     * the value before validation rather than letting it fall through as null.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_done' => $this->boolean('is_done'),
        ]);

        if (is_string($this->title)) {
            $this->merge(['title' => trim($this->title)]);
        }
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Please give the task a title.',
            'priority.in' => 'Pick a priority of high, medium or low.',
            'due_date.date' => 'Enter the due date as a real calendar date.',
        ];
    }
}
