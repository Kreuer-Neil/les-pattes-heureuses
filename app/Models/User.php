<?php

namespace App\Models;

use App\Enums\Roles;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'avatar',
        'email',
        'password',
        'user_role_id',
        'must_change_password',
    ];

    /**
     * The accessors to append to the model's array/JSON form.
     *
     * @var list<string>
     */
    protected $appends = [
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

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
            'two_factor_confirmed_at' => 'datetime',
            'must_change_password' => 'boolean',
        ];
    }

    public function userRole(): BelongsTo
    {
        return $this->belongsTo(UserRole::class);
    }

    public function role(): string
    {
        return $this->userRole->name;
    }

    public function getRoleAttribute(): string
    {
        return $this->role();
    }

    public function isAdmin(): bool
    {
        return $this->role() === Roles::Admin->value;
    }

    /**
     * Default signature for the admin-composed reply feature.
     */
    public function defaultSignature(): string
    {
        $title = $this->isAdmin()
            ? __('mail.signature.admin_title')
            : __('mail.signature.volunteer_title');

        return __('mail.signature.template', ['name' => $this->name, 'role' => $title]);
    }

    // Basically a "::where(role -> admin)"
    public function scopeAdmins(Builder $query): Builder
    {
        return $query->whereHas('userRole', fn (Builder $role) => $role->where('name', Roles::Admin->value));
    }
}
