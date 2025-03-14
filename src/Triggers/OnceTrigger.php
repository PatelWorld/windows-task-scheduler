<?php

namespace PatelWorld\TaskScheduler\Triggers;

class OnceTrigger extends AbstractTrigger
{
    private string $startTime;
    private string $startDate;

    public function __construct(string $startTime, string $startDate)
    {
        $this->type = 'ONCE';
        $this->startTime = $startTime;
        $this->startDate = $startDate;
    }

    public function toCommandArgs(): string
    {
        return "/SC ONCE /ST {$this->startTime} /SD {$this->startDate}";
    }

    public function setStartTime(string $startTime): self
    {
        $this->startTime = $startTime;
        return $this;
    }

    public function setStartDate(string $startDate): self
    {
        $this->startDate = $startDate;
        return $this;
    }

    public function getStartTime(): string
    {
        return $this->startTime;
    }

    public function getStartDate(): string
    {
        return $this->startDate;
    }
}
