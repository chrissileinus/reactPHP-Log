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
  public $ignore;
  public $isFile;
  public $noDecoration;

  function __construct(
    $stream,
    $minLevel = Level::NONE,
    $ignore = [],
    bool $isFile = false,
    bool $noDecoration = false,
    bool $noTimestamp = false
  ) {
    $this->stream = $stream;
    $this->minLevel = $minLevel;
    $this->ignore = $ignore;
    $this->isFile = $isFile;
    $this->noDecoration = $noDecoration;
    $this->noTimestamp = $noTimestamp;
  }
}
