<?php

namespace App\Http\Requests\Api;

use App\Models\Asset;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Validation for the mobile intake API.
 *
 * Mirrors TaskApiRequest's approach: POST requires every field a fresh intake
 * needs, PATCH/PUT only validates what the client actually sent.
 */
class AssetIntakeRequest extends FormRequest
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
        $required = $this->isMethod('POST') ? 'required' : 'sometimes';

        return [
            'name' => [$required, 'string', 'max:255'],
            'category' => [$required, 'string', 'max:255'],
            'model' => ['sometimes', 'nullable', 'string', 'max:255'],
            'serial_number' => [
                'sometimes', 'nullable', 'string', 'max:255',
                Rule::unique('assets', 'serial_number')->ignore($this->route('asset')),
            ],
            'asset_tag' => [
                'sometimes', 'nullable', 'string', 'max:255',
                Rule::unique('assets', 'asset_tag')->ignore($this->route('asset')),
            ],
            'condition' => ['sometimes', Rule::in(Asset::CONDITIONS)],
            'status' => ['sometimes', Rule::in(Asset::STATUSES)],
            'value' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'assigned_user_id' => ['sometimes', 'nullable', 'exists:users,id'],
            'department_id' => ['sometimes', 'nullable', 'exists:departments,id'],
            'location_id' => ['sometimes', 'nullable', 'exists:locations,id'],
            'received_at' => ['sometimes', 'nullable', 'date'],
            'warranty_expires_at' => ['sometimes', 'nullable', 'date'],
        ];
    }
}
