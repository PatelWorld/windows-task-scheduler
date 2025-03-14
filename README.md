# Windows Task Scheduler PHP Library

A PHP library for managing Windows Task Scheduler tasks using SOLID principles and design patterns.

## Installation

```bash
composer require patelworld/windows-task-scheduler
```

## Usage

### Creating a daily task

```php
use PatelWorld\TaskScheduler\Factory\TaskFactory;
use PatelWorld\TaskScheduler\WindowsTaskScheduler;

$factory = new TaskFactory();
$scheduler = new WindowsTaskScheduler();

// Create a daily task that runs at 8:00 AM
$task = $factory->createDailyTask(
    'BackupDatabase',
    'C:\\scripts\\backup.bat',
    '08:00'
);

$scheduler->createTask($task);
```

### Listing all tasks

```php
$tasks = $scheduler->getAllTasks();
foreach ($tasks as $task) {
    echo $task->getName() . "\n";
}
```

### Deleting a task

```php
$scheduler->deleteTask('BackupDatabase');
```

## License

MIT

