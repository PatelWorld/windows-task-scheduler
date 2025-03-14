<?php

namespace PatelWorld\TaskScheduler;

use PatelWorld\TaskScheduler\Contracts\TaskInterface;
use PatelWorld\TaskScheduler\Contracts\TriggerInterface;

class Task implements TaskInterface
{
    private string $name;
    private string $description = '';
    private string $executablePath;
    private string $arguments = '';
    private string $workingDirectory = '';
    private TriggerInterface $trigger;
    private string $userContext = 'SYSTEM';
    private bool $enabled = true;

    public function __construct(string $name, string $executablePath, TriggerInterface $trigger)
    {
        $this->name = $name;
        $this->executablePath = $executablePath;
        $this->trigger = $trigger;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): TaskInterface
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): TaskInterface
    {
        $this->description = $description;
        return $this;
    }

    public function getExecutablePath(): string
    {
        return $this->executablePath;
    }

    public function setExecutablePath(string $path): TaskInterface
    {
        $this->executablePath = $path;
        return $this;
    }

    public function getArguments(): string
    {
        return $this->arguments;
    }

    public function setArguments(string $arguments): TaskInterface
    {
        $this->arguments = $arguments;
        return $this;
    }

    public function getWorkingDirectory(): string
    {
        return $this->workingDirectory;
    }

    public function setWorkingDirectory(string $directory): TaskInterface
    {
        $this->workingDirectory = $directory;
        return $this;
    }

    public function getTrigger(): TriggerInterface
    {
        return $this->trigger;
    }

    public function setTrigger(TriggerInterface $trigger): TaskInterface
    {
        $this->trigger = $trigger;
        return $this;
    }

    public function getUserContext(): string
    {
        return $this->userContext;
    }

    public function setUserContext(string $userContext): TaskInterface
    {
        $this->userContext = $userContext;
        return $this;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): TaskInterface
    {
        $this->enabled = $enabled;
        return $this;
    }

    public function toCommandArgs(): string
    {
        $command = "/TN \"{$this->name}\" /TR \"{$this->executablePath}";

        if (!empty($this->arguments)) {
            $command .= " {$this->arguments}";
        }

        $command .= "\"";

        if (!empty($this->workingDirectory)) {
            $command .= " /RU {$this->userContext} /RP * /RL HIGHEST";
        } else {
            $command .= " /RU {$this->userContext}";
        }

        return $command . " " . $this->trigger->toCommandArgs();
    }
}
