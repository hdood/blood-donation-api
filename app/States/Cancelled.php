<?php

namespace App\States;


use App\States\AppointmentState;

class Cancelled extends AppointmentState
{
    public function getInfo()
    {
        return ["state" => "cancelled", "appointment" => $this->getModel()];
    }
}
