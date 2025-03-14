<?php

namespace PatelWorld\TaskScheduler\Factory;

use PatelWorld\TaskScheduler\Contracts\TaskInterface;
use PatelWorld\TaskScheduler\Contracts\TriggerInterface;
use PatelWorld\TaskScheduler\Task;

class TaskFactory
{
    private TriggerFactory $triggerFactory;

    public function __construct(?TriggerFactory $triggerFactory = null)
    {
        $this->triggerFactory = $triggerFactory ?? new TriggerFactory();
    }

    public function createTask(string $name, string $executablePath, TriggerInterface $trigger): TaskInterface
    {
        return new Task($name, $executablePath, $trigger);
    }

    public function createDailyTask(string $name, string $executablePath, string $startTime = '08:00', int $daysInterval = 1): TaskInterface
    {
        $trigger = $this->triggerFactory->createDailyTrigger($startTime, $daysInterval);
        return $this->createTask($name, $executablePath, $trigger);
    }

    public function createWeeklyTask(string $name, string $executablePath, string $startTime = '08:00', int $weeksInterval = 1, array $daysOfWeek = ['MON']): TaskInterface
    {
        $trigger = $this->triggerFactory->createWeeklyTrigger($startTime, $weeksInterval, $daysOfWeek);
        return $this->createTask($name, $executablePath, $trigger);
    }

    public function createMonthlyTask(string $name, string $executablePath, string $startTime = '08:00', array $daysOfMonth = [1], array $months = []): TaskInterface
    {
        $trigger = $this->triggerFactory->createMonthlyTrigger($startTime, $daysOfMonth, $months);
        return $this->createTask($name, $executablePath, $trigger);
    }

    public function createOnceTask(string $name, string $executablePath, string $startTime, string $startDate): TaskInterface
    {
        $trigger = $this->triggerFactory->createOnceTrigger($startTime, $startDate);
        return $this->createTask($name, $executablePath, $trigger);
    }

    public function createOnIdleTask(string $name, string $executablePath, int $idleTime = 10): TaskInterface
    {
        $trigger = $this->triggerFactory->createOnIdleTrigger($idleTime);
        return $this->createTask($name, $executablePath, $trigger);
    }

    public function createOnStartupTask(string $name, string $executablePath): TaskInterface
    {
        $trigger = $this->triggerFactory->createOnStartupTrigger();
        return $this->createTask($name, $executablePath, $trigger);
    }

    public function createOnLogonTask(string $name, string $executablePath): TaskInterface
    {
        $trigger = $this->triggerFactory->createOnLogonTrigger();
        return $this->createTask($name, $executablePath, $trigger);
    }
}
