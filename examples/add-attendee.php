<?php

require __DIR__ . '/../vendor/autoload.php';

if (4 !== count($argv)) {
    print "Usage:\n  $argv[0] \"calendar_id\" \"event_id\" \"attendee\"\n\n";
    exit(1);
}

$gc = new \Silvioq\GoogleCalendar\GoogleCalendar;
$gc->setClientSecretPath(__DIR__ . '/../.data/cs.json')
  ->setCredentialsPath(__DIR__ . '/../.data/cp');

$event = $gc->getEvent($argv[1], $argv[2]);
$event->addAttendee($argv[3]);

$gc->updateEvent($event);
$event = $gc->getEvent($argv[1], $argv[2]);
var_dump($event);

// vim:sw=4 ts=4 sts=4 et
