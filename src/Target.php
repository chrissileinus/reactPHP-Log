<?php
/*
 * Created on Wed Nov 03 2021
 *
 * Copyright (c) 2021 Christian Backus (Chrissileinus)
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

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
