<?php

namespace App\States;

use Spatie\ModelStates\State;
use Spatie\ModelStates\StateConfig;

abstract class AppointmentState extends State
{
    public abstract function getInfo();
    public static function config(): StateConfig
    {
        return parent::config()
            ->default(Pending::class)
            ->allowTransition(Pending::class, Accepted::class, PendingToAccepted::class)
            ->allowTransition(Accepted::class, Rescheduled::class, AcceptedToRescheduled::class)
            ->allowTransition([Pending::class, Accepted::class], Cancelled::class, ToCancelled::class);
    }
}
