<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user)],
            // Password is required to create a user but optional on edit —
            // leaving it blank keeps the current password unchanged.
            'password' => [$user ? 'sometimes' : 'required', 'nullable', 'string', 'min:8'],
            'role' => ['required', Rule::in(User::ROLES)],
        ];
    }
}
