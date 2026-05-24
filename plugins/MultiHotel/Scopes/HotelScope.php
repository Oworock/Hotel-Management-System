<?php

namespace Plugins\MultiHotel\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class HotelScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Don't apply when running CLI commands (like migrations/seeding) unless running unit tests.
        if (app()->runningInConsole() && !app()->runningUnitTests()) {
            return;
        }

        // Avoid recursion: if resolving/loading the authenticated user, do not apply scope to the User model.
        if ($model instanceof \App\Models\User && !auth()->hasUser()) {
            return;
        }

        if (auth()->check()) {
            $user = auth()->user();

            if ($user->role === 'super_admin') {
                // Super Admins can filter by header or session active hotel
                $activeHotelId = null;
                if (request()->hasHeader('X-Hotel-ID')) {
                    $activeHotelId = request()->header('X-Hotel-ID');
                } elseif (request()->hasSession() && session()->has('active_hotel_id')) {
                    $activeHotelId = session('active_hotel_id');
                }
                if ($activeHotelId !== null && $activeHotelId !== '') {
                    $builder->where($model->getTable() . '.hotel_id', $activeHotelId);
                }
            } else {
                // Non-super admins (admin, receptionist, staff, etc.) are scoped to their assigned hotel
                $builder->where($model->getTable() . '.hotel_id', $user->hotel_id);
            }
        } elseif (!($model instanceof \App\Models\User) && request()->hasSession() && session()->has('active_hotel_id')) {
            $activeHotelId = session('active_hotel_id');
            if ($activeHotelId !== null && $activeHotelId !== '') {
                $builder->where($model->getTable() . '.hotel_id', $activeHotelId);
            }
        }
    }
}
