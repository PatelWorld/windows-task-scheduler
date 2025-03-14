<?php

namespace PatelWorld\TaskScheduler;

use PatelWorld\TaskScheduler\Contracts\CommandExecutorInterface;
use PatelWorld\TaskScheduler\Exceptions\CommandExecutionException;

class CommandExecutor implements CommandExecutorInterface
{
    private string $outputBuffer = '';
    private string $errorBuffer = '';
    private int $exitCode = 0;

    public function execute(string $command): string
    {
        $descriptor = [
            1 => ['pipe', 'w'], // stdout
            2 => ['pipe', 'w']  // stderr
        ];

        $process = proc_open($command, $descriptor, $pipes);

        if (!is_resource($process)) {
            throw new CommandExecutionException("Failed to execute command: $command");
        }

        $this->outputBuffer = stream_get_contents($pipes[1]);
        $this->errorBuffer = stream_get_contents($pipes[2]);

        fclose($pipes[1]);
        fclose($pipes[2]);

        $this->exitCode = proc_close($process);

        if ($this->exitCode !== 0) {
            throw new CommandExecutionException(
                "Command failed with exit code {$this->exitCode}: {$this->errorBuffer}",
                $this->exitCode
            );
        }

        return $this->outputBuffer;
    }

    public function getOutputBuffer(): string
    {
        return $this->outputBuffer;
    }

    public function getErrorBuffer(): string
    {
        return $this->errorBuffer;
    }

    public function getExitCode(): int
    {
        return $this->exitCode;
    }
}
