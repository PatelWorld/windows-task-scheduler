<?php

namespace PatelWorld\TaskScheduler\Contracts;

interface TriggerInterface
{
  /**
   * Get trigger type
   * @return string Trigger type
   */
  public function getType(): string;

  /**
   * Convert trigger to command-line arguments
   * @return string Command-line representation
   */
  public function toCommandArgs(): string;
}
