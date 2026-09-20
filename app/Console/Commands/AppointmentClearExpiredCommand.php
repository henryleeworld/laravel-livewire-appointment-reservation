<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('appointment:clear-expired')]
#[Description('Command description')]
class AppointmentClearExpiredCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        Appointment::where('reserved_at', '<=', now()->subMinutes(config('app.reservation_time')))
            ->where('confirmed', false)
            ->delete();
    }
}
