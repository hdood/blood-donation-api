<?php

namespace App\States;

use App\Models\Appointment;
use App\Notifications\AppointmentRescheduled;
use Spatie\ModelStates\Transition;

class AcceptedToRescheduled extends Transition
{
    private Appointment $appointment;

    private array $data;

    public function __construct(Appointment $appointment, array $data)
    {
        $this->appointment = $appointment;

        $this->data = $data;
    }

    public function handle(): Appointment
    {
        $this->appointment->state = new Rescheduled($this->appointment);
        $this->appointment->date = $this->data['date'];
        $this->appointment->time = $this->data['time'];

        $this->appointment->save();

        $donor = $this->appointment->donor;
        $donor->notify(new AppointmentRescheduled($this->appointment));

        return $this->appointment;
    }
    public function canTransition(): bool
    {
        return $this->appointment->state->equals(Accepted::class) || $this->appointment->state->equals(Rescheduled::class);
    }
}
