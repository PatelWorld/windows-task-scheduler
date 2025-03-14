<?php

namespace PatelWorld\TaskScheduler\Triggers;

use PatelWorld\TaskScheduler\Contracts\TriggerInterface;

abstract class AbstractTrigger implements TriggerInterface
{
  protected string $type;

  public function getType(): string
  {
    return $this->type;
  }
}
