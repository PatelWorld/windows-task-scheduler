<?php

namespace PatelWorld\TaskScheduler\Factory;

use PatelWorld\TaskScheduler\Contracts\TriggerInterface;
use PatelWorld\TaskScheduler\Triggers\DailyTrigger;
use PatelWorld\TaskScheduler\Triggers\WeeklyTrigger;
use PatelWorld\TaskScheduler\Triggers\MonthlyTrigger;
use PatelWorld\TaskScheduler\Triggers\OnceTrigger;
use PatelWorld\TaskScheduler\Triggers\OnIdleTrigger;
use PatelWorld\TaskScheduler\Triggers\OnStartupTrigger;
use PatelWorld\TaskScheduler\Triggers\OnLogonTrigger;

class TriggerFactory
{
    public function createDailyTrigger(string $startTime = '08:00', int $daysInterval = 1): TriggerInterface
    {
        return new DailyTrigger($startTime, $daysInterval);
    }

    public function createWeeklyTrigger(string $startTime = '08:00', int $weeksInterval = 1, array $daysOfWeek = ['MON']): TriggerInterface
    {
        return new WeeklyTrigger($startTime, $weeksInterval, $daysOfWeek);
    }

    public function createMonthlyTrigger(string $startTime = '08:00', array $daysOfMonth = [1], array $months = []): TriggerInterface
    {
        return new MonthlyTrigger($startTime, $daysOfMonth, $months);
    }

    public function createOnceTrigger(string $startTime, string $startDate): TriggerInterface
    {
        return new OnceTrigger($startTime, $startDate);
    }

    public function createOnIdleTrigger(int $idleTime = 10): TriggerInterface
    {
        return new OnIdleTrigger($idleTime);
    }

    public function createOnStartupTrigger(): TriggerInterface
    {
        return new OnStartupTrigger();
    }

    public function createOnLogonTrigger(): TriggerInterface
    {
        return new OnLogonTrigger();
    }
}
