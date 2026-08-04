<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    // Distinct from the separate `admin` guard/Admin model — these are the
    // two roles a regular (web/sanctum) account can hold. 'ict_asset_team'
    // can create/edit assets; 'manager' is read-only oversight.
    public const ROLE_ICT_ASSET_TEAM = 'ict_asset_team';

    public const ROLE_MANAGER = 'manager';

    public const ROLES = [self::ROLE_ICT_ASSET_TEAM, self::ROLE_MANAGER];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class, 'assigned_user_id');
    }

    public function canManageAssets(): bool
    {
        return $this->role === self::ROLE_ICT_ASSET_TEAM;
    }

    /**
     * Always store lowercase so the unique constraint and login lookup can't
     * be bypassed by case variants of the same address.
     */
    protected function email(): Attribute
    {
        return Attribute::make(set: fn (string $value) => Str::lower($value));
    }
}
