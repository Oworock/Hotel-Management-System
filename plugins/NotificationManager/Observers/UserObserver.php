<?php

namespace Plugins\NotificationManager\Observers;

use App\Models\User;
use Plugins\NotificationManager\Services\NotificationManagerService;

class UserObserver
{
    public function created(User $user): void
    {
        if ($user->role === 'customer') {
            return;
        }

        app(NotificationManagerService::class)->notify(
            'staff.created',
            app(NotificationManagerService::class)->payloadForStaff($user)
        );
    }

    public function updated(User $user): void
    {
        if ($user->role === 'customer' || !$user->wasChanged('status')) {
            return;
        }

        app(NotificationManagerService::class)->notify(
            'staff.status_changed',
            app(NotificationManagerService::class)->payloadForStaff($user, [
                'old_status' => $user->getOriginal('status'),
            ])
        );
    }
}
