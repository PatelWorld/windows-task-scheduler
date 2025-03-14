<?php

namespace PatelWorld\TaskScheduler\Triggers;

class OnIdleTrigger extends AbstractTrigger
{
    private int $idleTime;

    public function __construct(int $idleTime = 10)
    {
        $this->type = 'ONIDLE';
        $this->idleTime = $idleTime;
    }

    public function toCommandArgs(): string
    {
        return "/SC ONIDLE /I {$this->idleTime}";
    }

    public function setIdleTime(int $idleTime): self
    {
        $this->idleTime = $idleTime;
        return $this;
    }
    
    public function getIdleTime(): int
    {
        return $this->idleTime;
    }
}