<?php
namespace Chrissileinus\React\Log;

class Target {
  public $stream;
  public $minLevel;
  public $isFile;

  function __construct($stream, $minLevel = Level::NONE, bool $isFile = false) {
    $this->stream = $stream;
    $this->minLevel = $minLevel;
    $this->isFile = $isFile;
  }
}