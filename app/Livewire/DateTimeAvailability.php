<?php

namespace App\Livewire;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class DateTimeAvailability extends Component
{
    public string $date;

    public array $availableTimes = [];

    public Collection $appointments;

    public string $startTime = '';

    public ?int $appointmentId = null;

    public function mount(): void
    {
        $this->date = now()->format('Y-m-d');

        $this->getIntervalsAndAvailableTimes();
    }

    public function updatedDate(): void
    {
        $this->getIntervalsAndAvailableTimes();
    }

    public function render()
    {
        $appointment = $this->appointmentId ? Appointment::find($this->appointmentId) : null;

        return view('livewire.date-time-availability', [
            'appointment' => $appointment
        ]);
    }

    public function save(): void
    {
        $this->validate([
            'startTime' => 'required',
        ]);

        $this->appointmentId = Appointment::create([
            'start_time' => Carbon::parse($this->startTime),
            'reserved_at' => now()
        ])->id;
    }

    public function confirmAppointment(): void
    {
        $appointment = Appointment::find($this->appointmentId);
        if (!$appointment || Carbon::parse($appointment->reserved_at)->diffInMinutes(now()) > config('appointment.reservation_time')) {
            $this->redirectRoute('dashboard');
            return;
        }
        $appointment->confirmed = true;
        $appointment->save();

        $this->redirectRoute('appointment-confirmed', $this->appointmentId);
    }

    public function cancelAppointment(): void
    {
        Appointment::find($this->appointmentId)?->delete();

        $this->reset('appointment');
    }

    protected function getIntervalsAndAvailableTimes(): void
    {
        $this->reset('availableTimes');

        $carbonIntervals = Carbon::parse($this->date . ' 8 am')->toPeriod($this->date . ' 8 pm', 30, 'minute');

        $this->appointments = Appointment::whereDate('start_time', $this->date)->get();

        foreach ($carbonIntervals as $interval) {
            $this->availableTimes[$interval->format('h:i A')] = !$this->appointments->contains('start_time', $interval);
        }
    }
}
