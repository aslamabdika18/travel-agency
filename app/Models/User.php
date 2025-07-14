<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser, MustVerifyEmail, HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles,InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'contact',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Check if user has specific role
     */
    public function isAdmin(): bool
    {
        return $this->hasRole(['admin', 'super_admin']);
    }
    public function canAccessPanel(Panel $panel): bool
    {
    return $this->isAdmin();
    }

    /**
     * Check if user is customer
     */
    public function isCustomer(): bool
    {
        return $this->hasRole('customer');
    }

    /**
     * Get user's primary role name
     */
    public function getPrimaryRole(): string
    {
        return $this->roles->first()?->name ?? 'customer';
    }

    /**
     * Scope untuk filter berdasarkan role
     */
    public function scopeWithRole($query, $role)
    {
        return $query->whereHas('roles', function ($q) use ($role) {
            $q->where('name', $role);
        });
    }

    /**
     * Safely assign a role to user, ensuring no role conflicts
     */
    public function safeAssignRole($role)
    {
        // Jika user sudah memiliki role, jangan assign role baru
        if ($this->roles()->count() > 0) {
            return false;
        }

        $this->assignRole($role);
        return true;
    }

    /**
     * Check if user can be assigned customer role
     */
    public function canBeCustomer(): bool
    {
        return $this->roles()->count() === 0 || $this->hasRole('customer');
    }

    /**
     * Boot method - removed auto role assignment to prevent conflicts
     */
    protected static function boot()
    {
        parent::boot();

        // Auto role assignment removed to prevent conflicts
        // Role assignment should be handled explicitly in controllers/seeders
    }
}
