<?php

/**
 * Simple example for get the access tokens
 *
 * For get client secret path, goto https://console.developers.google.com/
 * create an application, add Calendar Api and create an json file credentials
 * (Create credentialas, OAuth client ID, Application type = "Other" and download
 * json file in __DIR__ . '/../.data/cp')
 *
 * At first time, this scripts will ask you for auth code, showing an OAUTH2 url.
 * After pasting code, access and refresh token will be saved at __DIR__ . '/../.data/cp'
 *
 * @author Silvioq <silvioq@gmail.com>
 */

require __DIR__ . '/../vendor/autoload.php';

$gc = new \Silvioq\GoogleCalendar\GoogleCalendar;
$gc->setClientSecretPath(__DIR__ . '/../.data/cs.json')
  ->setCredentialsPath(__DIR__ . '/../.data/cp');

try
{
    $calendars = $gc->listCalendars();
} catch (\Silvioq\GoogleCalendar\Exception\InvalidAccessTokenException $e) {
    $url = $e->getAuthUrl();
    print "Open your browser in " . $url . PHP_EOL;

    print "Follow the instructions and paste the code : ";
    $auth = fgets(STDIN);

    $gc->buildClientWithAuthToken(preg_replace('/\r?\n$/', '', $auth));

    $calendars = $gc->listCalendars();
}

$max = 0;
array_walk($calendars, function($c) use(&$max) {
        $max = max($max,strlen($c->getSummary()));
    });

if ($max > 80) $max = 80;

echo "ID                                                   Summary" . PHP_EOL;
echo "---------------------------------------------------- " . str_repeat('-', $max) . PHP_EOL;
   #  1234567890123456789012345678901234567890123456789012
   #           1         2         3         4         5
foreach ($calendars as $c) {
    print str_pad($c->getId(), 53) . substr($c->getSummary(),0,$max) . PHP_EOL;
}

// vim:sw=4 ts=4 sts=4 et
