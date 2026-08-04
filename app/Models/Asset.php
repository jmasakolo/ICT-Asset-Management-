<?php

namespace App\Models;

use Database\Factories\AssetFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name', 'category', 'model', 'serial_number', 'asset_tag', 'status', 'condition',
    'value', 'assigned_user_id', 'received_at', 'warranty_expires_at',
])]
class Asset extends Model
{
    /** @use HasFactory<AssetFactory> */
    use HasFactory;

    // 'repair' and 'configuration' are intake workflow states: an asset sits
    // there until the Asset Team moves it to 'active' (or 'retired').
    public const STATUSES = ['active', 'maintenance', 'repair', 'configuration', 'retired'];

    public const CONDITIONS = ['new', 'old'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'received_at' => 'date',
            'warranty_expires_at' => 'date',
        ];
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_user_id');
    }

    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(MaintenanceRecord::class);
    }
}
