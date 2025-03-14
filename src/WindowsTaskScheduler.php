<?php

namespace PatelWorld\TaskScheduler;

use PatelWorld\TaskScheduler\Contracts\TaskSchedulerInterface;
use PatelWorld\TaskScheduler\Contracts\TaskInterface;
use PatelWorld\TaskScheduler\Contracts\CommandExecutorInterface;
use PatelWorld\TaskScheduler\Contracts\TriggerInterface;
use PatelWorld\TaskScheduler\Exceptions\TaskSchedulerException;


class WindowsTaskScheduler implements TaskSchedulerInterface
{
    private CommandExecutorInterface $executor;
    private string $schtasksPath = 'schtasks.exe';

    public function __construct(?CommandExecutorInterface $executor = null)
    {
        $this->executor = $executor ?? new CommandExecutor();
    }

    public function createTask(TaskInterface $task): bool
    {
        try {
            $command = "{$this->schtasksPath} /Create {$task->toCommandArgs()}";

            if (!$task->isEnabled()) {
                $command .= " /DISABLE";
            }

            $this->executor->execute($command);
            return true;
        } catch (\Exception $e) {
            throw new TaskSchedulerException(
                "Failed to create task '{$task->getName()}': " . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    public function deleteTask(string $taskName): bool
    {
        try {
            $command = "{$this->schtasksPath} /Delete /TN \"{$taskName}\" /F";
            $this->executor->execute($command);
            return true;
        } catch (\Exception $e) {
            throw new TaskSchedulerException(
                "Failed to delete task '{$taskName}': " . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    public function enableTask(string $taskName): bool
    {
        try {
            $command = "{$this->schtasksPath} /Change /TN \"{$taskName}\" /ENABLE";
            $this->executor->execute($command);
            return true;
        } catch (\Exception $e) {
            throw new TaskSchedulerException(
                "Failed to enable task '{$taskName}': " . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    public function disableTask(string $taskName): bool
    {
        try {
            $command = "{$this->schtasksPath} /Change /TN \"{$taskName}\" /DISABLE";
            $this->executor->execute($command);
            return true;
        } catch (\Exception $e) {
            throw new TaskSchedulerException(
                "Failed to disable task '{$taskName}': " . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    public function getTask(string $taskName): ?TaskInterface
    {
        try {
            $command = "{$this->schtasksPath} /Query /TN \"{$taskName}\" /FO CSV /V";
            $output = $this->executor->execute($command);

            return $this->parseTaskFromCsvOutput($output, $taskName);
        } catch (\Exception $e) {
            // If task doesn't exist, return null instead of throwing an exception
            if (
                strpos($e->getMessage(), 'ERROR: The system cannot find the file specified') !== false ||
                strpos($e->getMessage(), 'Cannot find the task') !== false
            ) {
                return null;
            }

            throw new TaskSchedulerException(
                "Failed to get task '{$taskName}': " . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    public function getAllTasks(): array
    {
        try {
            $command = "{$this->schtasksPath} /Query /FO CSV /V";
            $output = $this->executor->execute($command);
            var_dump($output);
            return $this->parseTasksFromCsvOutput($output);
        } catch (\Exception $e) {
            throw new TaskSchedulerException(
                "Failed to get all tasks: " . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    public function runTask(string $taskName): bool
    {
        try {
            $command = "{$this->schtasksPath} /Run /TN \"{$taskName}\"";
            $this->executor->execute($command);
            return true;
        } catch (\Exception $e) {
            throw new TaskSchedulerException(
                "Failed to run task '{$taskName}': " . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    public function stopTask(string $taskName): bool
    {
        try {
            $command = "{$this->schtasksPath} /End /TN \"{$taskName}\"";
            $this->executor->execute($command);
            return true;
        } catch (\Exception $e) {
            throw new TaskSchedulerException(
                "Failed to stop task '{$taskName}': " . $e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Parse tasks from CSV output
     * 
     * @param string $csvOutput CSV output from schtasks command
     * @return array Array of Task objects
     */
    private function parseTasksFromCsvOutput(string $csvOutput): array
    {
        $lines = explode("\n", trim($csvOutput));

        if (count($lines) <= 1) {
            return [];
        }

        // Parse the header line to get the field indices
        $headerLine = str_getcsv(trim($lines[0]));
        $taskNameIndex = array_search('"TaskName"', $headerLine);

        if ($taskNameIndex === false) {
            throw new TaskSchedulerException("Invalid CSV format: TaskName field not found");
        }

        $tasks = [];
        // Start from 1 to skip the header
        for ($i = 1; $i < count($lines); $i++) {
            $line = trim($lines[$i]);
            if (empty($line)) {
                continue;
            }

            $values = str_getcsv($line);
            $taskName = trim($values[$taskNameIndex], '"');

            // Skip system tasks (those that start with \Microsoft\)
            if (strpos($taskName, '\Microsoft\\') === 0) {
                continue;
            }

            try {
                $task = $this->getTask($taskName);
                if ($task !== null) {
                    $tasks[] = $task;
                }
            } catch (TaskSchedulerException $e) {
                // Log the error but continue processing other tasks
                error_log("Error parsing task '$taskName': " . $e->getMessage());
            }
        }

        return $tasks;
    }

    /**
     * Parse a single task from CSV output
     * 
     * @param string $csvOutput CSV output from schtasks command
     * @param string $taskName Task name to parse
     * @return TaskInterface|null Task object or null if not found
     */
    private function parseTaskFromCsvOutput(string $csvOutput, string $taskName): ?TaskInterface
    {
        $lines = explode("\n", trim($csvOutput));

        if (count($lines) <= 1) {
            return null;
        }

        // Parse the header line to get the field indices
        $headerLine = str_getcsv(trim($lines[0]));

        // Find indices for important fields
        $indices = $this->findFieldIndices($headerLine);

        // Parse the task data (second line)
        $taskData = str_getcsv(trim($lines[1]));

        // Create appropriate trigger
        $triggerType = $this->parseTriggerType(trim($taskData[$indices['scheduleType']], '"'));
        $trigger = $this->createTriggerFromCsv($triggerType, $taskData, $indices);

        // Extract the command and arguments
        $taskRunValue = trim($taskData[$indices['taskToRun']], '"');
        list($executable, $arguments) = $this->parseExecutableAndArguments($taskRunValue);

        // Create the task object
        $factory = new Factory\TaskFactory();
        $task = $factory->createTask($taskName, $executable, $trigger);

        // Set additional properties
        if (!empty($arguments)) {
            $task->setArguments($arguments);
        }

        // Set task enabled status
        $statusValue = trim($taskData[$indices['status']], '"');
        $task->setEnabled($statusValue === 'Ready' || $statusValue === 'Running');

        // Set user context
        if (isset($indices['runAs'])) {
            $task->setUserContext(trim($taskData[$indices['runAs']], '"'));
        }

        // Set description if available
        if (isset($indices['comment'])) {
            $description = trim($taskData[$indices['comment']], '"');
            if (!empty($description)) {
                $task->setDescription($description);
            }
        }

        return $task;
    }

    /**
     * Find field indices in CSV header
     * 
     * @param array $headerLine CSV header line
     * @return array Field indices
     */
    private function findFieldIndices(array $headerLine): array
    {
        $indices = [];
        $fieldMappings = [
            'taskName' => ['"TaskName"'],
            'status' => ['"Status"'],
            'scheduleType' => ['"Schedule Type"', '"ScheduleType"'],
            'taskToRun' => ['"Task To Run"', '"Command"'],
            'startTime' => ['"Start Time"', '"StartTime"'],
            'startDate' => ['"Start Date"', '"StartDate"'],
            'days' => ['"Days"', '"DaysOfWeek"'],
            'months' => ['"Months"', '"MonthsOfYear"'],
            'runAs' => ['"Run As User"', '"Author"'],
            'comment' => ['"Comment"', '"Description"']
        ];

        foreach ($fieldMappings as $key => $possibleNames) {
            foreach ($possibleNames as $name) {
                $index = array_search($name, $headerLine);
                if ($index !== false) {
                    $indices[$key] = $index;
                    break;
                }
            }
        }

        return $indices;
    }

    /**
     * Parse trigger type from schedule type string
     * 
     * @param string $scheduleType Schedule type string from CSV
     * @return string Trigger type
     */
    private function parseTriggerType(string $scheduleType): string
    {
        $typeMap = [
            'Daily' => 'DAILY',
            'Weekly' => 'WEEKLY',
            'Monthly' => 'MONTHLY',
            'One Time Only' => 'ONCE',
            'At system startup' => 'ONSTART',
            'At logon' => 'ONLOGON',
            'On idle' => 'ONIDLE'
        ];

        return $typeMap[$scheduleType] ?? 'DAILY';
    }

    /**
     * Create trigger from CSV data
     * 
     * @param string $triggerType Trigger type
     * @param array $taskData Task data from CSV
     * @param array $indices Field indices
     * @return TriggerInterface Trigger object
     */
    private function createTriggerFromCsv(string $triggerType, array $taskData, array $indices): Contracts\TriggerInterface
    {
        $factory = new Factory\TriggerFactory();

        switch ($triggerType) {
            case 'DAILY':
                $startTime = isset($indices['startTime']) ? trim($taskData[$indices['startTime']], '"') : '08:00';
                return $factory->createDailyTrigger($startTime);

            case 'WEEKLY':
                $startTime = isset($indices['startTime']) ? trim($taskData[$indices['startTime']], '"') : '08:00';
                $daysOfWeek = isset($indices['days']) ? $this->parseDaysOfWeek(trim($taskData[$indices['days']], '"')) : ['MON'];
                return $factory->createWeeklyTrigger($startTime, 1, $daysOfWeek);

            case 'MONTHLY':
                $startTime = isset($indices['startTime']) ? trim($taskData[$indices['startTime']], '"') : '08:00';
                // Parse days and months - this is simplified, would need more complex parsing in real implementation
                return $factory->createMonthlyTrigger($startTime);

            case 'ONCE':
                $startTime = isset($indices['startTime']) ? trim($taskData[$indices['startTime']], '"') : '08:00';
                $startDate = isset($indices['startDate']) ? trim($taskData[$indices['startDate']], '"') : date('m/d/Y');
                return $factory->createOnceTrigger($startTime, $startDate);

            case 'ONSTART':
                return $factory->createOnStartupTrigger();

            case 'ONLOGON':
                return $factory->createOnLogonTrigger();

            case 'ONIDLE':
                return $factory->createOnIdleTrigger();

            default:
                // Default to daily trigger
                return $factory->createDailyTrigger();
        }
    }

    /**
     * Parse days of week from string
     * 
     * @param string $daysString Days string from CSV
     * @return array Array of day abbreviations
     */
    private function parseDaysOfWeek(string $daysString): array
    {
        $daysMapping = [
            'Monday' => 'MON',
            'Tuesday' => 'TUE',
            'Wednesday' => 'WED',
            'Thursday' => 'THU',
            'Friday' => 'FRI',
            'Saturday' => 'SAT',
            'Sunday' => 'SUN'
        ];

        $result = [];
        foreach ($daysMapping as $fullName => $abbr) {
            if (strpos($daysString, $fullName) !== false) {
                $result[] = $abbr;
            }
        }

        return !empty($result) ? $result : ['MON'];
    }

    /**
     * Parse executable and arguments from task run string
     * 
     * @param string $taskRunValue Task run value from CSV
     * @return array [executable, arguments]
     */
    private function parseExecutableAndArguments(string $taskRunValue): array
    {
        // Check if the command is quoted
        if (preg_match('/^"([^"]+)"(.*)$/', $taskRunValue, $matches)) {
            $executable = $matches[1];
            $arguments = trim($matches[2]);
        } else {
            // Otherwise, split on first space
            $parts = explode(' ', $taskRunValue, 2);
            $executable = $parts[0];
            $arguments = isset($parts[1]) ? trim($parts[1]) : '';
        }

        return [$executable, $arguments];
    }

    /**
     * Set path to schtasks.exe
     * 
     * @param string $path Path to schtasks.exe
     * @return self
     */
    public function setSchtasksPath(string $path): self
    {
        $this->schtasksPath = $path;
        return $this;
    }

    /**
     * Get path to schtasks.exe
     * 
     * @return string Path to schtasks.exe
     */
    public function getSchtasksPath(): string
    {
        return $this->schtasksPath;
    }
}
