<?php

namespace PatelWorld\TaskScheduler;

use PatelWorld\TaskScheduler\Contracts\TaskInterface;

class TaskCollection implements \IteratorAggregate, \Countable
{
    private array $tasks = [];

    public function add(TaskInterface $task): self
    {
        $this->tasks[$task->getName()] = $task;
        return $this;
    }

    public function get(string $name): ?TaskInterface
    {
        return $this->tasks[$name] ?? null;
    }

    public function remove(string $name): self
    {
        if (isset($this->tasks[$name])) {
            unset($this->tasks[$name]);
        }
        return $this;
    }

    public function has(string $name): bool
    {
        return isset($this->tasks[$name]);
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->tasks);
    }

    public function count(): int
    {
        return count($this->tasks);
    }

    public function toArray(): array
    {
        return $this->tasks;
    }
}
