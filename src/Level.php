<?php
/*
 * Created on Wed Nov 03 2021
 *
 * Copyright (c) 2021 Christian Backus (Chrissileinus)
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Chrissileinus\React\Log;

abstract class Level extends Enum
{
  const DEBUG     = 100;
  const INFO      = 200;
  const NOTICE    = 300;
  const WARNING   = 400;
  const ERROR     = 500;
  const CRITICAL  = 600;
  const ALERT     = 700;
  const EMERGENCY = 800;
  const GLOBAL    = 10000;
}
