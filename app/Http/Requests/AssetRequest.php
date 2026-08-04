<?php

namespace App\Http\Requests;

use App\Models\Asset;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssetRequest extends FormRequest
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
        return [
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(Asset::STATUSES)],
            'value' => ['required', 'numeric', 'min:0'],
            'department_id' => ['nullable', 'exists:departments,id'],
            'location_id' => ['nullable', 'exists:locations,id'],
            // Only meaningful on the admin form — the self-service
            // controller always overwrites this with Auth::id() regardless
            // of what's submitted, so a user can't reassign via a crafted request.
            'assigned_user_id' => ['nullable', 'exists:users,id'],
        ];
    }
}
