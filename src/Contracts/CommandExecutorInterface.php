<?php

namespace PatelWorld\TaskScheduler\Contracts;

interface CommandExecutorInterface
{
    /**
     * Execute a command
     * @param string $command Command to execute
     * @return string Command output
     * @throws \TaskScheduler\Exceptions\CommandExecutionException
     */
    public function execute(string $command): string;
}