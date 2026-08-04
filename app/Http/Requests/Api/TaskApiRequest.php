<?php

namespace App\Http\Requests\Api;

use App\Models\Task;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for the JSON API.
 *
 * Deliberately separate from the web TaskRequest: an HTML form always posts
 * every field, so the web request can normalise a missing checkbox to false.
 * An API client sends only the fields it wants to change, so doing that here
 * would silently reopen a completed task on any PATCH that omitted is_done.
 */
class TaskApiRequest extends FormRequest
{
    /**
     * The route itself requires a Sanctum token (see routes/api.php); tasks
     * have no owner column, so any authenticated user may manage any task —
     * a single shared list, not per-user data.
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
        $required = $this->isMethod('POST') ? 'required' : 'sometimes';

        return [
            'title' => [$required, 'string', 'max:255'],
            'notes' => ['sometimes', 'nullable', 'string', 'max:5000'],
            'due_date' => ['sometimes', 'nullable', 'date'],
            'priority' => ['sometimes', Rule::in(Task::PRIORITIES)],
            'is_done' => ['sometimes', 'boolean'],
        ];
    }

    /**
     * Trim the title so " " can't pass the required check, matching the web
     * form's behaviour. Only touches keys the client actually sent.
     */
    protected function prepareForValidation(): void
    {
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
