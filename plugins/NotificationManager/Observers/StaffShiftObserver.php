<?php

namespace Plugins\NotificationManager\Observers;

use App\Models\StaffShift;
use Plugins\NotificationManager\Services\NotificationManagerService;

class StaffShiftObserver
{
    public function created(StaffShift $shift): void
    {
        app(NotificationManagerService::class)->notify(
            'staff.shift_started',
            app(NotificationManagerService::class)->payloadForShift($shift)
        );
    }

    public function updated(StaffShift $shift): void
    {
        if ($shift->wasChanged('clock_out_at') && $shift->clock_out_at) {
            app(NotificationManagerService::class)->notify(
                'staff.shift_ended',
                app(NotificationManagerService::class)->payloadForShift($shift)
            );
        }
    }
}
