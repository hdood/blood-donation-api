<?php

namespace App\States;


class Pending extends AppointmentState
{
    public function getInfo()
    {
        return ["state" => "pending", "appointment" => $this->getModel()];
    }
}
