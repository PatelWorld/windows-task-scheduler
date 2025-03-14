<?php

namespace PatelWorld\TaskScheduler\Triggers;

class MonthlyTrigger extends AbstractTrigger
{
  private string $startTime;
  private array $daysOfMonth;
  private array $months;

  public function __construct(string $startTime = '08:00', array $daysOfMonth = [1], array $months = [])
  {
    $this->type = 'MONTHLY';
    $this->startTime = $startTime;
    $this->daysOfMonth = $daysOfMonth;
    $this->months = $months ?: range(1, 12);
  }

  public function toCommandArgs(): string
  {
    $daysString = implode(',', $this->daysOfMonth);
    $monthsString = implode(',', $this->months);
    return "/SC MONTHLY /ST {$this->startTime} /D {$daysString} /M {$monthsString}";
  }

  public function setStartTime(string $startTime): self
  {
    $this->startTime = $startTime;
    return $this;
  }

  public function setDaysOfMonth(array $daysOfMonth): self
  {
    $this->daysOfMonth = $daysOfMonth;
    return $this;
  }

  public function setMonths(array $months): self
  {
    $this->months = $months;
    return $this;
  }

  public function getStartTime(): string
  {
    return $this->startTime;
  }

  public function getDaysOfMonth(): array
  {
    return $this->daysOfMonth;
  }

  public function getMonths(): array
  {
    return $this->months;
  }
}
