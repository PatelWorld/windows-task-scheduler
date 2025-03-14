<?php

namespace PatelWorld\TaskScheduler\Triggers;

class WeeklyTrigger extends AbstractTrigger
{
  private string $startTime;
  private int $weeksInterval;
  private array $daysOfWeek;

  public function __construct(string $startTime = '08:00', int $weeksInterval = 1, array $daysOfWeek = ['MON'])
  {
    $this->type = 'WEEKLY';
    $this->startTime = $startTime;
    $this->weeksInterval = $weeksInterval;
    $this->daysOfWeek = $daysOfWeek;
  }

  public function toCommandArgs(): string
  {
    $daysString = implode(',', $this->daysOfWeek);
    return "/SC WEEKLY /ST {$this->startTime} /MO {$this->weeksInterval} /D {$daysString}";
  }

  public function setStartTime(string $startTime): self
  {
    $this->startTime = $startTime;
    return $this;
  }

  public function setWeeksInterval(int $weeksInterval): self
  {
    $this->weeksInterval = $weeksInterval;
    return $this;
  }

  public function setDaysOfWeek(array $daysOfWeek): self
  {
    $this->daysOfWeek = $daysOfWeek;
    return $this;
  }

  public function getStartTime(): string
  {
    return $this->startTime;
  }

  public function getWeeksInterval(): int
  {
    return $this->weeksInterval;
  }

  public function getDaysOfWeek(): array
  {
    return $this->daysOfWeek;
  }
}
