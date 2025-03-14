<?php

namespace PatelWorld\TaskScheduler\Triggers;

class OnLogonTrigger extends AbstractTrigger
{
    public function __construct()
    {
        $this->type = 'ONLOGON';
    }

    public function toCommandArgs(): string
    {
        return "/SC ONLOGON";
    }
}
