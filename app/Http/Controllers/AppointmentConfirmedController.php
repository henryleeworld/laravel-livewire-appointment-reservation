<?php

namespace App\Http\Controllers;

use App\Models\Appointment;

class AppointmentConfirmedController extends Controller
{
    /**
     * Invoke the controller method.
     */
    public function __invoke(Appointment $appointment)
    {
        return view('appointment-confirmed', [
            'appointment' => $appointment
        ]);
    }
}
