<?php

namespace Chrissileinus\React\Log;

class Target
{
  public $stream;
  public $minLevel;
  public $isFile;
  public $ignore;

  function __construct($stream, $minLevel = Level::NONE, $ignore = [], bool $isFile = false)
  {
    $this->stream = $stream;
    $this->minLevel = $minLevel;
    $this->isFile = $isFile;
    $this->ignore = $ignore;
  }
}
