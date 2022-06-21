<?php
/*
 * Created on Wed Nov 03 2021
 *
 * Copyright (c) 2021 Christian Backus (Chrissileinus)
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source code.
 */

namespace Chrissileinus\React\Log;

use \Chrissileinus\Template;

class Writer
{
  static public string $lineReset = "";
  static public string $lineEnd = PHP_EOL;

  static public string $lineFormat = " {rubric%10s}:{level%-10s} ⁞ {message}";

  static private string $timeZone = "GMT";
  static private string $timeFormat = "Y.m.d H:i:s ⁞";

  static private $colorizeLevel;

  static private array $ignore = [];

  static private $postWrite;

  static private array $targets = [];

  static public function config(array $config)
  {
    extract($config);
    if (isset($lineReset))
      self::$lineReset = $lineReset;
    if (isset($lineEnd))
      self::$lineEnd = $lineEnd;
    if (isset($lineFormat))
      self::$lineFormat = $lineFormat;
    if (isset($postWrite) && is_callable($postWrite))
      self::$postWrite = $postWrite;

    if (isset($timeZone) && in_array($timeZone, timezone_identifiers_list()))
      self::$timeZone = $timeZone;
    if (isset($timeFormat))
      self::$timeFormat = $timeFormat;

    if (isset($ignore))
      self::$ignore = $ignore;

    if (isset($colorizeLevel) && is_array($colorizeLevel))
      self::$colorizeLevel = array_merge(self::$colorizeLevel, $colorizeLevel);
  }

  static public function targets(array $targets)
  {
    self::$targets = [];
    foreach ($targets as $target) {
      if (is_resource($target->stream) && stream_get_meta_data($target->stream)['wrapper_type'] == "plainfile") {
        $target->stream = new \React\Stream\WritableResourceStream($target->stream);
        $target->isFile = true;
      }
      if ($target instanceof Target) self::$targets[] = $target;
    }
  }

  static public function formatedTime()
  {
    return (new \DateTime("now", new \DateTimeZone(self::$timeZone)))->format(self::$timeFormat);
  }

  static public function formatedTimeFromTimestamp(int $timestamp): string
  {
    return (new \DateTime())->setTimezone(new \DateTimeZone(self::$timeZone))->setTimestamp($timestamp)->format(self::$timeFormat);
  }

  static public function write(string $output, bool $writeIntoFile = true, $level = Level::NONE, string $rubric = '')
  {
    foreach (self::$targets as $target) {
      if (
        ($level->value >= $target->minLevel->value ||
          $target->minLevel->value == Level::NONE->value) &&
        !(is_array(self::$ignore) &&
          array_key_exists($rubric, self::$ignore) &&
          $level == self::$ignore[$rubric]) &&
        !(is_array($target->ignore) &&
          array_key_exists($rubric, $target->ignore) &&
          $level == $target->ignore[$rubric])
      ) {
        $tmp = $output;
        if (!$target->noTimestamp) {
          $tmp = self::formatedTime() . $tmp;
        }
        if ($target->noDecoration) {
          $tmp = preg_replace('/\e[[][^A-Za-z]*[A-Za-z]/', '', $tmp);
        }

        if (
          $target->stream instanceof \React\Stream\WritableStreamInterface
        ) {
          if ($target->isFile && $writeIntoFile) {
            $target->stream->write($tmp . PHP_EOL);
          }
          if (!$target->isFile) $target->stream->write(self::$lineReset . $tmp . self::$lineEnd);
        }
        if ($target->stream instanceof \React\Socket\LimitingServer) {
          foreach ($target->stream->getConnections() as $connection) {
            $connection->write(self::$lineReset . $tmp . self::$lineEnd);
          }
        }
      }
    }
  }

  static public function log($level, $message, $rubric, callable $postWrite = null)
  {
    $replacements = [
      'rubric' => $rubric,
      'level' => $level->name,
      'message' => $message,
    ];

    $output = Template\Str::replaceF(self::$lineFormat, $replacements);

    self::write($output, true, $level, $rubric);

    if (is_callable($postWrite)) {
      call_user_func($postWrite, $output, $level, $rubric);
      return;
    }
    if (is_callable(self::$postWrite)) {
      call_user_func(self::$postWrite, $output, $level, $rubric);
    }
  }

  static public function debug($message, $rubric, callable $postWrite = null)
  {
    self::log(Level::DEBUG, $message, $rubric, $postWrite);
  }

  static public function info($message, $rubric, callable $postWrite = null)
  {
    self::log(Level::INFO, $message, $rubric, $postWrite);
  }

  static public function notice($message, $rubric, callable $postWrite = null)
  {
    self::log(Level::NOTICE, $message, $rubric, $postWrite);
  }

  static public function warning($message, $rubric, callable $postWrite = null)
  {
    self::log(Level::WARNING, $message, $rubric, $postWrite);
  }

  static public function error($message, $rubric, callable $postWrite = null)
  {
    self::log(Level::ERROR, $message, $rubric, $postWrite);
  }

  static public function critical($message, $rubric, callable $postWrite = null)
  {
    self::log(Level::CRITICAL, $message, $rubric, $postWrite);
  }

  static public function alert($message, $rubric, callable $postWrite = null)
  {
    self::log(Level::ALERT, $message, $rubric, $postWrite);
  }

  static public function emergency($message, $rubric, callable $postWrite = null)
  {
    self::log(Level::EMERGENCY, $message, $rubric, $postWrite);
  }
}
