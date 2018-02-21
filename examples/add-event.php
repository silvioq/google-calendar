<?php

require __DIR__ . '/../vendor/autoload.php';

if (3 !== count($argv)) {
    print "Usage:\n  $argv[0] \"calendar_id\" \"summary\"\n\n";
    exit(1);
}

$gc = new \Silvioq\GoogleCalendar\GoogleCalendar;
$gc->setClientSecretPath(__DIR__ . '/../.data/cs.json')
  ->setCredentialsPath(__DIR__ . '/../.data/cp');

$event = (new \Silvioq\GoogleCalendar\GoogleEvent())
    ->setSummary($argv[2])
    ->setCalendarId($argv[1])
    ->setStart(new \DateTime('tomorrow'))
    ->setEnd(new \DateTime('tomorrow'))
    ->setAllDay(true)
    ;

$gc->addEvent($event);
var_dump($event);

// vim:sw=4 ts=4 sts=4 et
