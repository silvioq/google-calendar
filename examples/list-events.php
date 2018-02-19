<?php

require __DIR__ . '/../vendor/autoload.php';

if (2 !== count($argv)) {
    print "Usage:\n  $argv[0] \"calendar_id\"\n\n";
    exit(1);
}

$gc = new \Silvioq\GoogleCalendar\GoogleCalendar;
$gc->setClientSecretPath(__DIR__ . '/../.data/cs.json')
  ->setCredentialsPath(__DIR__ . '/../.data/cp');

try
{
    $nextPage = null;
    $counter = 0;
    $events = $gc->getEvents($argv[1], null, null, $nextPage, 10);
    while (true) {
        foreach($events as $e) {
            echo $counter ++ . ": " ;
            echo var_dump($e);
            echo PHP_EOL;
        }
        if (null === $nextPage)
            break;

        $events = $gc->getEvents($argv[1], null, null, $nextPage, 10);

        if ($counter > 500) die;
    }
} catch (\Silvioq\GoogleCalendar\Exception\InvalidAccessTokenException $e) {
    print "Invalid access token. May be you must run " . __DIR__ . "/build-at.php first\n";
    exit(1);
}

echo "Total $counter" . PHP_EOL;
// vim:sw=4 ts=4 sts=4 et
