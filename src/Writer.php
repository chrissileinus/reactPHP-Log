<?php

namespace Chrissileinus\React\Log;

class Writer
{
  static public string $lineReset = "\r\e[K";
  static public string $lineEnd = PHP_EOL;

  static public string $lineFormat = "%s ⁞%s %10s\e[0m:%s%-10s \e[0m⁞ %s\e[0m";

  static private string $timeZone = "GMT";
  static private string $timeFormat = "Y.m.d H:i:s";

  static private array $ignore = [];

  static private string $colorizeReset = "\e[0m";
  static private array $colorizeLevel = [
    Level::DEBUG => "\e[36m",
    Level::INFO => "\e[0m",
    Level::NOTICE => "\e[33m",
    Level::WARNING => "\e[31m\e[2m",
    Level::ERROR => "\e[31m",
    Level::CRITICAL => "\e[31m\e[1m",
    Level::ALERT => "\e[31m\e[1m\e[4m",
    Level::EMERGENCY => "\e[31m\e[1m\e[7m",
    Level::GLOBAL => "\e[0m",
  ];

  static private array $styleFilter = [
    '/(error):/' => "\e[31m$1\e[0m:",
    '/(state):/' => "\e[34m$1\e[0m:",
    '/(\S+)::(\S+)/' => "\e[36m$1\e[0m::\e[36m$2\e[0m",
  ];

  static private $postWrite;

  static public array $history = [];

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

  static public function write(string $output, bool $writeIntoFile = true, $level = Level::NONE, string $rubric = '')
  {
    foreach (self::$targets as $target) {
      if (
        ($level >= $target->minLevel ||
          $target->minLevel == Level::NONE) &&
        !(is_array(self::$ignore) &&
          array_key_exists($rubric, self::$ignore) &&
          $level == self::$ignore[$rubric]) &&
        !(is_array($target->ignore) &&
          array_key_exists($rubric, $target->ignore) &&
          $level == $target->ignore[$rubric])
      ) {
        if ($target->stream instanceof \React\Stream\WritableResourceStream) {
          if ($target->isFile && $writeIntoFile) {
            $tmp = $output;
            // $tmp = preg_replace('/\e[[][A-Za-z0-9]{1,2};?[0-9]*m?/', '', $tmp);
            $tmp = trim($tmp, "\e[2K\e[1A\e[K");
            $tmp = trim($tmp) . PHP_EOL;
            $target->stream->write($tmp);
          }
          if (!$target->isFile) $target->stream->write($output);
        }
        if ($target->stream instanceof \React\Socket\LimitingServer) {
          foreach ($target->stream->getConnections() as $connection) {
            $connection->write($output);
          }
        }
      }
    }
  }

  static protected function pushInHistory(string $string)
  {
    array_push(self::$history, $string);
    if (count(self::$history) > 50) array_shift(self::$history);
  }

  static public function log($level, $message, $rubric, callable $postWrite = null)
  {
    $highlight = "";
    ksort(self::$colorizeLevel);
    foreach (self::$colorizeLevel as $_Level => $_Color) {
      if ($level >= $_Level) {
        $highlight = $_Color;
      }
    }

    $output = sprintf(
      self::$lineFormat,
      (new \DateTime("now", new \DateTimeZone(self::$timeZone)))->format(self::$timeFormat),
      $highlight,
      $rubric,
      $highlight,
      Level::getName($level),
      preg_replace(array_keys(self::$styleFilter), array_values(self::$styleFilter), $message),
    );

    self::write(self::$lineReset . $output . self::$lineEnd, true, $level, $rubric);
    self::pushInHistory(self::$lineReset . $output . self::$lineEnd);

    if (is_callable($postWrite)) {
      call_user_func($postWrite);
      return;
    }
    if (is_callable(self::$postWrite)) {
      call_user_func(self::$postWrite);
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
