<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

// completed_at is fillable because the controller derives it from is_done;
// it is never read from user input (it has no validation rule).
#[Fillable(['title', 'notes', 'due_date', 'priority', 'is_done', 'completed_at'])]
class Task extends Model
{
    /**
     * Allowed priority values, highest urgency first.
     *
     * Single source of truth: the validation rules and the form's <select>
     * both read from here, so they cannot drift apart.
     */
    public const PRIORITIES = ['high', 'medium', 'low'];

    public const DEFAULT_PRIORITY = 'medium';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'is_done' => 'boolean',
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Order by urgency. Alphabetical sorting would put "high" after "low",
     * so map each priority to an explicit rank instead.
     */
    #[Scope]
    protected function orderByPriority(Builder $query): void
    {
        $query->orderByRaw(
            "case priority when 'high' then 1 when 'medium' then 2 else 3 end"
        );
    }

    /**
     * Tasks with a due date in the past that are still outstanding.
     */
    public function isOverdue(): bool
    {
        return ! $this->is_done
            && $this->due_date !== null
            && $this->due_date->isPast()
            && ! $this->due_date->isToday();
    }

    public function isDueToday(): bool
    {
        return ! $this->is_done
            && $this->due_date !== null
            && $this->due_date->isToday();
    }
}
