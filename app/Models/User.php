<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'status', 'phone', 'nationality', 'loyalty_points', 'is_blacklisted', 'functions', 'prefer_dark_mode', 'preferred_theme'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function isReceptionist(): bool
    {
        return $this->role === 'receptionist';
    }

    public function isKitchenManager(): bool
    {
        return $this->role === 'kitchen_manager';
    }

    public function isTuckShopManager(): bool
    {
        return $this->role === 'tuck_shop_manager';
    }

    public function isRestaurantManager(): bool
    {
        return $this->role === 'restaurant_manager';
    }

    public function hasFunction(string $function): bool
    {
        // Super Admin has all functions
        if ($this->isSuperAdmin()) {
            return true;
        }

        // Admin has all management functions except kitchen dashboard
        if ($this->isAdmin()) {
            if ($function === 'kitchen_dashboard') {
                return false;
            }
            return true;
        }

        // Default functions based on roles:
        if ($this->role === 'tuck_shop_manager' && $function === 'manage_tuck_shop') {
            return true;
        }
        if ($this->role === 'restaurant_manager' && $function === 'manage_restaurant') {
            return true;
        }
        if ($this->isKitchenManager() && $function === 'kitchen_dashboard') {
            return true;
        }
        if ($this->isReceptionist()) {
            if (in_array($function, ['manage_bookings', 'manage_guests', 'manage_rooms'])) {
                return true;
            }
        }

        // Check custom functions assigned to the user
        $assigned = $this->functions;
        if (is_array($assigned) && in_array($function, $assigned)) {
            return true;
        }

        return false;
    }

    public function shifts()
    {
        return $this->hasMany(StaffShift::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'customer_id');
    }

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
            'is_blacklisted' => 'boolean',
            'loyalty_points' => 'integer',
            'functions' => 'array',
            'prefer_dark_mode' => 'boolean',
        ];
    }
}
