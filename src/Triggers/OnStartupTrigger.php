<?php

namespace PatelWorld\TaskScheduler\Triggers;

class OnStartupTrigger extends AbstractTrigger
{
    public function __construct()
    {
        $this->type = 'ONSTART';
    }

    public function toCommandArgs(): string
    {
        return "/SC ONSTART";
    }
}
