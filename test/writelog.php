<?php
require __DIR__ . '/../vendor/autoload.php';

use Chrissileinus\React\Log;

Log\Writer::targets([
  new Log\Target(
    new React\Stream\WritableResourceStream(STDOUT, null, 8192),
    Log\Level::NONE
  ),
  new Log\Target(
    fopen("./test.log", 'w+'),
    Log\Level::NONE
  ),
]);

$statusBar = function () {
  Log\Writer::write(Log\Writer::$lineReset . "test" . PHP_EOL . "test" . "\r", false);
};

Log\Writer::config([
  'timeZone' => "Europe/Berlin",
  'lineReset' => "\e[2K\e[1A\e[2K\r",
  'lineEnd' => PHP_EOL . PHP_EOL,
  'postWrite' => $statusBar
]);

Log\Writer::debug("oh toll", "main");
Log\Writer::info("es läuft", "main");
React\EventLoop\Loop::addPeriodicTimer(1, function () {
  Log\Writer::warning("hmmm", "main");
  Log\Writer::error("nicht", "main");
  Log\Writer::notice("lalala", "main");
  Log\Writer::critical("hilfe alles läuft schief........", "main");
  Log\Writer::log(Log\Level::NONE, "nulllllll", "main");
});

echo "\e[?25l" . PHP_EOL; // hide cursor in cli

//  on signal SIGINT
React\EventLoop\Loop::addSignal(SIGINT, function () {
  Log\Writer::info(
    sprintf(
      "Stop with pid:%d",
      posix_getpid()
    ),
    'main'
  );

  React\EventLoop\Loop::stop();
});

React\EventLoop\Loop::run();

echo "\e[?25h" . PHP_EOL; // show cursor in cli