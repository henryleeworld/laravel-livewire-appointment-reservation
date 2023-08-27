<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use Illuminate\Console\Command;

class AppointmentClearExpiredCommand extends Command
{
    protected $signature = 'appointment:clear-expired';

    protected $description = 'Command description';

    public function handle(): void
    {
        Appointment::where('reserved_at', '<=', now()->subMinutes(config('appointment.reservation_time')))
            ->where('confirmed', false)
            ->delete();
    }
}
