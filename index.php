<?php

require 'vendor/autoload.php';

use PatelWorld\TaskScheduler\Factory\TaskFactory;
use PatelWorld\TaskScheduler\WindowsTaskScheduler;

$t = new TaskFactory();
$wts = new WindowsTaskScheduler();
$task = $t->createDailyTask("Open Paint", __DIR__ . "\\task.bat", "16:52");
$wts->createTask($task);
