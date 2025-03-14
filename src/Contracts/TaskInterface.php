<?php

namespace Patelworld\TaskScheduler\Contracts;

interface TaskInterface
{
  /**
   * Get task name
   * @return string Task name
   */
  public function getName(): string;

  /**
   * Set task name
   * @param string $name Task name
   * @return TaskInterface For method chaining
   */
  public function setName(string $name): TaskInterface;

  /**
   * Get task description
   * @return string Task description
   */
  public function getDescription(): string;

  /**
   * Set task description
   * @param string $description Task description
   * @return TaskInterface For method chaining
   */
  public function setDescription(string $description): TaskInterface;

  /**
   * Get task executable path
   * @return string Executable path
   */
  public function getExecutablePath(): string;

  /**
   * Set task executable path
   * @param string $path Executable path
   * @return TaskInterface For method chaining
   */
  public function setExecutablePath(string $path): TaskInterface;

  /**
   * Get task arguments
   * @return string Task arguments
   */
  public function getArguments(): string;

  /**
   * Set task arguments
   * @param string $arguments Task arguments
   * @return TaskInterface For method chaining
   */
  public function setArguments(string $arguments): TaskInterface;

  /**
   * Get task working directory
   * @return string Working directory
   */
  public function getWorkingDirectory(): string;

  /**
   * Set task working directory
   * @param string $directory Working directory
   * @return TaskInterface For method chaining
   */
  public function setWorkingDirectory(string $directory): TaskInterface;

  /**
   * Get task trigger
   * @return TriggerInterface Trigger configuration
   */
  public function getTrigger(): TriggerInterface;

  /**
   * Set task trigger
   * @param TriggerInterface $trigger Trigger configuration
   * @return TaskInterface For method chaining
   */
  public function setTrigger(TriggerInterface $trigger): TaskInterface;

  /**
   * Get task user context
   * @return string User context
   */
  public function getUserContext(): string;

  /**
   * Set task user context
   * @param string $userContext User context
   * @return TaskInterface For method chaining
   */
  public function setUserContext(string $userContext): TaskInterface;

  /**
   * Get task enabled status
   * @return bool Enabled status
   */
  public function isEnabled(): bool;

  /**
   * Set task enabled status
   * @param bool $enabled Enabled status
   * @return TaskInterface For method chaining
   */
  public function setEnabled(bool $enabled): TaskInterface;

  /**
   * Convert task to command-line arguments
   * @return string Command-line arguments
   */
  public function toCommandArgs(): string;
}
