<?php

namespace App\States;

use App\Models\Appointment;
use App\Notifications\AppointmentAccepted;
use Spatie\ModelStates\Transition;

class PendingToAccepted extends Transition
{
    private Appointment $appointment;


    public function __construct(Appointment $appointment)
    {
        $this->appointment = $appointment;
    }

    public function handle(): Appointment
    {
        $this->appointment->state = new Accepted($this->appointment);
        $this->appointment->save();

        $donor = $this->appointment->donor;
        $donor->notify(new AppointmentAccepted($this->appointment));

        return $this->appointment;
    }
    public function canTransition(): bool
    {
        return $this->appointment->state->equals(Pending::class);
    }
}
