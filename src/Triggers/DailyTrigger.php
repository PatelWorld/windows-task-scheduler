<?php

namespace PatelWorld\TaskScheduler\Triggers;

class DailyTrigger extends AbstractTrigger
{
    private string $startTime;
    private int $daysInterval;

    public function __construct(string $startTime = '08:00', int $daysInterval = 1)
    {
        $this->type = 'DAILY';
        $this->startTime = $startTime;
        $this->daysInterval = $daysInterval;
    }

    public function toCommandArgs(): string
    {
        return "/SC DAILY /ST {$this->startTime} /MO {$this->daysInterval}";
    }

    public function setStartTime(string $startTime): self
    {
        $this->startTime = $startTime;
        return $this;
    }

    public function setDaysInterval(int $daysInterval): self
    {
        $this->daysInterval = $daysInterval;
        return $this;
    }
    
    public function getStartTime(): string
    {
        return $this->startTime;
    }
    
    public function getDaysInterval(): int
    {
        return $this->daysInterval;
    }
}