<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

#[Fillable(['actor_type', 'actor_label', 'action', 'subject_type', 'subject_id', 'description'])]
class AuditLog extends Model
{
    /**
     * Record an audit entry for the currently authenticated admin or user.
     */
    public static function record(string $action, ?Model $subject, string $description): void
    {
        $admin = Auth::guard('admin')->user();
        $user = $admin ? null : Auth::guard('web')->user();

        static::create([
            'actor_type' => $admin ? 'admin' : ($user ? 'user' : 'system'),
            'actor_label' => $admin?->email ?? $user?->email ?? 'system',
            'action' => $action,
            'subject_type' => $subject ? class_basename($subject) : null,
            'subject_id' => $subject?->getKey(),
            'description' => $description,
        ]);
    }
}
