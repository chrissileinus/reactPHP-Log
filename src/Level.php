<?php
/*
 * Created on Wed Nov 03 2021
 *
 * Copyright (c) 2021 Christian Backus (Chrissileinus)
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Chrissileinus\React\Log;

enum Level: int
{
  case NONE      = 0;
  case DEBUG     = 100;
  case INFO      = 200;
  case NOTICE    = 300;
  case WARNING   = 400;
  case ERROR     = 500;
  case CRITICAL  = 600;
  case ALERT     = 700;
  case EMERGENCY = 800;
  case GLOBAL    = 10000;

  static function byName(string $name)
  {
    return unserialize('E:' . strlen("\Chrissileinus\React\Log\Level") + 1 + strlen($name) . ':"' . "\Chrissileinus\React\Log\Level" . ':' . $name . '";');
  }
}
