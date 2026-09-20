<?php

namespace App\Livewire;

use App\Models\Appointment;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Livewire\Attributes\Validate;

class DateTimeAvailability extends Component
{
    public string $date = '';

    public array $availableTimes = [];

    public Collection $appointments;

    #[Validate('required')]
    public string $startTime = '';

    public ?int $appointmentId = null;

    public function mount(): void
    {
        $this->date = now()->format('Y-m-d');

        $this->loadAvailableTimes();
    }

    public function updatedDate(): void
    {
        $this->loadAvailableTimes();
    }

    public function render(): View
    {
        return view('livewire.date-time-availability', [
            'appointment' => $this->appointment,
        ]);
    }

    public function save(): void
    {
        $this->validate();

        $appointment = Appointment::create([
            'start_time' => Carbon::parse($this->startTime),
            'reserved_at' => now(),
        ]);

        $this->appointmentId = $appointment->id;
    }

    public function confirmAppointment(): void
    {
        $appointment = $this->appointment;

        if (
            !$appointment ||
            Carbon::parse($appointment->reserved_at)
                ->diffInMinutes(now()) > config('app.reservation_time')
        ) {
            $this->redirectRoute('dashboard');

            return;
        }

        $appointment->update([
            'confirmed' => true,
        ]);

        $this->redirectRoute(
            'appointment-confirmed',
            ['appointment' => $appointment->id]
        );
    }

    public function cancelAppointment(): void
    {
        $this->appointment?->delete();

        $this->reset('appointmentId');
    }

    public function getAppointmentProperty(): ?Appointment
    {
        if (!$this->appointmentId) {
            return null;
        }

        return Appointment::find($this->appointmentId);
    }

    protected function loadAvailableTimes(): void
    {
        $this->availableTimes = [];

        $start = Carbon::parse($this->date . ' 08:00');
        $end = Carbon::parse($this->date . ' 20:00');

        $intervals = $start->toPeriod($end, '30 minutes');

        $this->appointments = Appointment::query()
            ->whereDate('start_time', $this->date)
            ->get();

        foreach ($intervals as $interval) {
            $formatted = $interval->format('h:i A');

            $this->availableTimes[$formatted] = !$this->appointments
                ->contains(
                    fn ($appointment) =>
                        Carbon::parse($appointment->start_time)
                            ->equalTo($interval)
                );
        }
    }
}
