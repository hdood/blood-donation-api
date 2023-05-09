<?php

namespace App\States;


use App\States\AppointmentState;

class Rescheduled extends AppointmentState
{
    public function getInfo()
    {
        return ["state" => "rescheduled", "appointment" => $this->getModel()];
    }
}
