<?php

namespace App\States\Donation;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class DonationState extends State
{
    abstract function getInfo();
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Pending::class);
    }
}
