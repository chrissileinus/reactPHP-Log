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


Log\Writer::log(Log\Level::DEBUG, "oh toll", "main");
Log\Writer::log(Log\Level::INFO, "es läuft", "main");

Log\Writer::config([
  'timeZone' => "Europe/Berlin"
]);

Log\Writer::log(Log\Level::WARNING, "hmmm", "main");
Log\Writer::log(Log\Level::ERROR, "nicht", "main");
Log\Writer::log(Log\Level::NOTICE, "lalala", "main");
Log\Writer::log(Log\Level::CRITICAL, "hilfe alles läuft schief........", "main");
Log\Writer::log(Log\Level::NONE, "nulllllll", "main");