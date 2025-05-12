<?php


abstract class Result
{
  abstract public function isOk(): bool;
  abstract public function isErr(): bool;
  abstract public function unwrap(): mixed;
  abstract public function unwrapErr(): mixed;

  public static function Ok(mixed $value): self
  {
    return new Ok($value);
  }

  public static function Err(mixed $error): self
  {
    return new Err($error);
  }
}

class Ok extends Result
{
  private mixed $value;

  public function __construct(mixed $value)
  {
    $this->value = $value;
  }

  public function isOk(): bool
  {
    return true;
  }

  public function isErr(): bool
  {
    return false;
  }

  public function unwrap(): mixed
  {
    return $this->value;
  }

  public function unwrapErr(): mixed
  {
    throw new RuntimeException("Tried to unwrapErr an Ok result.");
  }
}
class Err extends Result
{
  private mixed $error;

  public function __construct(mixed $error)
  {
    $this->error = $error;
  }

  public function isOk(): bool
  {
    return false;
  }

  public function isErr(): bool
  {
    return true;
  }

  public function unwrap(): mixed
  {
    throw new RuntimeException("Tried to unwrap an Err result: {$this->error}");
  }

  public function unwrapErr(): mixed
  {
    return $this->error;
  }
}
