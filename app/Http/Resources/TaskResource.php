<?php

namespace App\Http\Resources;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * The single JSON shape for a task, used by every API endpoint so clients only
 * ever have to learn one representation.
 *
 * @mixin Task
 */
class TaskResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'notes' => $this->notes,
            // Date only — the column is a DATE, so a timestamp would imply a
            // precision the app does not have.
            'due_date' => $this->due_date?->toDateString(),
            'priority' => $this->priority,
            'is_done' => $this->is_done,
            // Derived server-side so every client agrees on what "overdue"
            // means, rather than each reimplementing the date comparison.
            'is_overdue' => $this->isOverdue(),
            'is_due_today' => $this->isDueToday(),
            'completed_at' => $this->completed_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
