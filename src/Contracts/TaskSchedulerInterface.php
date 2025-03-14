<?php

namespace PatelWorld\TaskScheduler\Contracts;

interface TaskSchedulerInterface
{
    /**
     * Create a new task
     * @param TaskInterface $task Task to create
     * @return bool Success status
     * @throws \TaskScheduler\Exceptions\TaskSchedulerException
     */
    public function createTask(TaskInterface $task): bool;

    /**
     * Delete an existing task
     * @param string $taskName Name of task to delete
     * @return bool Success status
     * @throws \TaskScheduler\Exceptions\TaskSchedulerException
     */
    public function deleteTask(string $taskName): bool;

    /**
     * Enable a task
     * @param string $taskName Name of task to enable
     * @return bool Success status
     * @throws \TaskScheduler\Exceptions\TaskSchedulerException
     */
    public function enableTask(string $taskName): bool;

    /**
     * Disable a task
     * @param string $taskName Name of task to disable
     * @return bool Success status
     * @throws \TaskScheduler\Exceptions\TaskSchedulerException
     */
    public function disableTask(string $taskName): bool;

    /**
     * Get task information
     * @param string $taskName Name of task to retrieve
     * @return TaskInterface|null Task object or null if not found
     * @throws \TaskScheduler\Exceptions\TaskSchedulerException
     */
    public function getTask(string $taskName): ?TaskInterface;

    /**
     * Get all tasks
     * @return array List of all tasks
     * @throws \TaskScheduler\Exceptions\TaskSchedulerException
     */
    public function getAllTasks(): array;

    /**
     * Run a task immediately
     * @param string $taskName Name of task to run
     * @return bool Success status
     * @throws \TaskScheduler\Exceptions\TaskSchedulerException
     */
    public function runTask(string $taskName): bool;

    /**
     * Stop a running task
     * @param string $taskName Name of task to stop
     * @return bool Success status
     * @throws \TaskScheduler\Exceptions\TaskSchedulerException
     */
    public function stopTask(string $taskName): bool;
}