<?php

namespace App\States;

use App\Models\Admin;
use App\Models\Appointment;
use App\Notifications\AppointmentCancelled;
use Illuminate\Support\Facades\Notification;
use Spatie\ModelStates\Transition;

class ToCancelled extends Transition
{
    private Appointment $appointment;


    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function handle(): Appointment
    {

        $state = $this->appointment->state;

        if (is_a($state, Pending::class)) {
            $this->appointment->delete();
            return true;
        }
        $this->appointment->state = new Cancelled($this->appointment);

        $this->appointment->save();

        $admins = Admin::all();

        Notification::send($admins, new AppointmentCancelled($this->appointment));

        return $this->appointment;
    }
    public function canTransition(): bool
    {
        return $this->appointment->state->equals(Pending::class) || $this->appointment->state->equals(Accepted::class) || $this->appointment->state->equals(Rescheduled::class);
    }
}
