<?php
/*
 * Created on Wed Nov 03 2021
 *
 * Copyright (c) 2021 Christian Backus (Chrissileinus)
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Chrissileinus\React\Log;

abstract class Enum
{
  const NONE = null;

  final private function __construct()
  {
    throw new NotSupportedException();
  }

  private function __clone()
  {
    throw new NotSupportedException();
  }

  final public static function toArray()
  {
    return (new \ReflectionClass(static::class))->getConstants();
  }

  final public static function isValid($value)
  {
    return in_array($value, static::toArray());
  }

  final public static function getName($value)
  { {
      return array_search($value, static::toArray()) ?: 'NONE';
    }
  }

  final public static function getConstant($value)
  {
    return static::toArray()[$value];
  }
}
