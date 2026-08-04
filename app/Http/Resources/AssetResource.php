<?php

namespace App\Http\Resources;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Asset
 */
class AssetResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'category' => $this->category,
            'model' => $this->model,
            'serial_number' => $this->serial_number,
            'asset_tag' => $this->asset_tag,
            'status' => $this->status,
            'condition' => $this->condition,
            'value' => (float) $this->value,
            'assigned_user_id' => $this->assigned_user_id,
            'department_id' => $this->department_id,
            'location_id' => $this->location_id,
            'received_at' => $this->received_at?->toDateString(),
            'warranty_expires_at' => $this->warranty_expires_at?->toDateString(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
