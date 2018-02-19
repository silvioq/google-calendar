<?php

namespace Silvioq\GoogleCalendar;

/**
 * Class GoogleCalendar
 *
 * Google Calendar conector
 * Inspired on fungio/FungioGoogleCalendarBundle, @see https://github.com/fungio/FungioGoogleCalendarBundle/blob/master/Service/GoogleCalendar.php
 *
 * @author Silvioq <silvioq@gmail.com>
 */
class GoogleCalendar
{
    /**
     * @var string
     */
    private $applicationName;

    /**
     * @var string|null
     */
    private $credentialsPath;

    /**
     * @var string|null
     */
    private $clientSecretPath;

    /**
     * @var string|null
     */
    private $accessToken;

    /**
     * @var string|null
     */
    private $refreshToken;

    /**
     * @var \Google_Client|null
     */
    private $client;

    /**
     * @param string $applicationName
     */
    public function __construct(string $applicationName = null, \Google_Client $client = null)
    {
        $this->applicationName = $applicationName ?? __CLASS__;
        $this->client = $client;
        $this->converter = new Converter\GoogleEventConverter();
    }

    /**
     * Set credential path
     */
    public function setCredentialsPath(string $credentialsPath):self
    {
        if (file_exists($credentialsPath) && !is_writable($credentialsPath)) {
            throw new \InvalidArgumentException(sprintf("Invalid credential path %s. Check path and permissions", $credentialsPath));
        }

        if (!file_exists($credentialsPath) && !is_writable(dirname($credentialsPath))) {
            throw new \InvalidArgumentException(sprintf("Invalid credential path %s. Check directory and permissions", $credentialsPath));
        }

        $this->credentialsPath = $credentialsPath;

        return $this;
    }

    /**
     * Set client secret path
     */
    public function setClientSecretPath(string $secretPath):self
    {
        if (false === ($realPath = realpath($secretPath))) {
            throw new \InvalidArgumentException(sprintf("Invalid client secret path %s. Check path and permissions", $secretPath));
        }

        $this->clientSecretPath = $realPath;

        return $this;
    }

    /**
     * Set access token
     */
    public function setAccessToken(string $accessToken):self
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    /**
     * Set refresh token
     */
    public function setRefreshToken(string $refreshToken):self
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }

    /**
     * @param GoogleEventInterface $event
     *
     * @return self
     */
    public function addEvent(GoogleEventInterface $event):self
    {
        return $this->getCalendarService()->events->insert($event->getCalendarId(),
                $this->converter->toGoogleEvent($event));
    }

    /**
     * List shared and available calendars
     *
     * @return Google_Service_Calendar_CalendarListEntry[]
     */
    public function listCalendars():array
    {
        /** @var \Google_Service_Calendar_CalendarList */
        $calendarList = $this->getCalendarService()->calendarList->listCalendarList();

        return $calendarList->getItems();
    }

    /**
     * List all evens between times
     */
    public function getEvents(string $calendarId, \DateTime $from = null, \DateTime $to = null, &$pageToken = null, $maxResults = 100):array
    {
        $opts = ['orderBy' => 'updated', 'maxResults' => $maxResults];
        if (null !== $from)
            $opts['timeMin'] = $from->format(\DateTime::RFC3339);

        if (null !== $to)
            $opts['timeMax'] = $from->format(\DateTime::RFC3339);

        if (null !== $pageToken)
            $opts['pageToken'] = $pageToken;

        $events = $this->getCalendarService()->events->listEvents($calendarId, $opts);
        $pageToken = $events->getNextPageToken();

        return array_map( function($ge) { $event = new GoogleEvent(); return $this->converter->toEvent($ge, $event); }, $events->getItems());
    }

    private function getCalendarService():\Google_Service_Calendar
    {
        return new \Google_Service_Calendar($this->getClient());
    }

    public function buildClientWithAuthToken(string $authCode, \Google_Client $client = null)
    {
        if (null === $client)
            $client = new \Google_Client();

        $client->setApplicationName($this->applicationName);
        $client->setAuthConfig($this->clientSecretPath);
        $client->setScopes(implode(' ', [\Google_Service_Calendar::CALENDAR]));
        $client->setApprovalPrompt("auto");
        $client->setAccessType("offline");

        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
        file_put_contents($this->credentialsPath, json_encode($accessToken));

        $this->client = $client;
        return $this;
    }

    /**
     * Return google client
     *
     * @return \Google_Client
     */
    private function getClient():\Google_Client
    {
        if (null !== $this->client)
            return $this->client;

        $client = new \Google_Client();

        $client->setApplicationName($this->applicationName);
        $client->setAuthConfig($this->clientSecretPath);
        $client->setScopes(implode(' ', [\Google_Service_Calendar::CALENDAR]));
        $client->setApprovalPrompt("auto");
        $client->setAccessType("offline");

        if (null === $this->accessToken && file_exists($this->credentialsPath)) {
            $accessToken = json_decode(file_get_contents($this->credentialsPath), true);
        } else {
            $accessToken = $this->accessToken;
        }

        if ($accessToken)
            $client->setAccessToken($accessToken);

        if ($client->getRefreshToken()) {
            $this->refreshToken = $client->getRefreshToken();
        }

        if ($client->isAccessTokenExpired()) {
            if (null === $this->refreshToken) {
                $refreshToken = $this->refreshToken;
            } else {
                $refreshToken = $client->getRefreshToken();
            }

            if (null === $refreshToken) {
                throw (new Exception\InvalidAccessTokenException('Token expired. Must inform refresh token.'))->setAuthUrl($client->createAuthUrl());
            }

            $res = $client->fetchAccessTokenWithRefreshToken($refreshToken);
            if (!isset($res['access_token'])) {
                throw (new Exception\InvalidAccessTokenException('Token expired. Must get new access token.'))->setAuthUrl($client->createAuthUrl());
            }

            file_put_contents($this->credentialsPath, json_encode($client->getAccessToken()));
        }

        return $this->client = $client;
    }
}
// vim:sw=4 ts=4 sts=4 et
