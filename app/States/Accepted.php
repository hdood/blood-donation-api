<?php

namespace App\States;

use App\States\AppointmentState;

class Accepted extends AppointmentState
{
    public function getInfo()
    {
        return ["state" => "accepted", "appointment" => $this->getModel()];
    }
}
