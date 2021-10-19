<?php
namespace Chrissileinus\React\Log;

abstract class Level extends Enum {
  const DEBUG     = 100;
  const INFO      = 200;
  const NOTICE    = 300;
  const WARNING   = 400;
  const ERROR     = 500;
  const CRITICAL  = 600;
  const ALERT     = 700;
  const EMERGENCY = 800;
}
