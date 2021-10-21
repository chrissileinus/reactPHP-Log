<?php

namespace Chrissileinus\React\Log;

abstract class Enum
{
  const NONE = null;

  final private function __construct()
  {
    throw new NotSupportedException();
  }

  final private function __clone()
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
