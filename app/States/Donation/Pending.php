<?php

namespace App\States\Donation;


class Pending extends DonationState
{


    public function getInfo()
    {
        return ["name" => "pending"];
    }
}
