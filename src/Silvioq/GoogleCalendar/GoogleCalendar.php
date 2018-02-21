<?php

namespace Silvioq\GoogleCalendar;

use Symfony\Component\OptionsResolver\OptionsResolver;

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
     * @param array $options
     *   - application_name
     *   - google_client
     *   - converter: Instance of Converter\ConverterInterface or null
     */
    public function __construct(array $options = [])
    {
        $resolver = new OptionsResolver();
        $resolver->setDefaults([
            'application_name' => __CLASS__,
            'google_client' => null,
            'converter' => null,
            'credentials_path' => null,
        ]);

        $resolver->setAllowedTypes('application_name', 'string')
            ->setAllowedTypes('google_client', ['null', \Google_Client::class])
            ->setAllowedTypes('converter', ['null', Converter\ConverterInterface::class])
            ->setAllowedTypes('credentials_path', ['null','string'])
            ;

        $options = $resolver->resolve($options);

        $this->applicationName = $options['application_name'];
        $this->client = $options['google_client'];
        $this->converter = $options['converter'] ?? new Converter\GoogleEventConverter();
        if ($options['credentials_path'])
            $this->setCredentialsPath($options['credentials_path']);
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
     *
     * @param string $calendarId
     * @param \DateTime $from
     * @param \DateTime $to
     * @param $pageToken
     * @param int $maxResults
     *
     * @return array
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

        return array_map( function($ge) use($calendarId) {
                return $this->converter->toEvent($ge)->setCalendarId($calendarId); }, $events->getItems());
    }

    /**
     * Client generation from authentication code
     *
     * This function generates a client from auth code obtained from OAUTHv2
     * authentication.
     * After success authentication, credentials file (with authToken and
     * refreshToken) will be created
     *
     * See examples/build-at.php sript for use example
     *
     * @return self
     */
    public function buildClientWithAuthToken(string $authCode, \Google_Client $client = null):self
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

    private function getCalendarService():\Google_Service_Calendar
    {
        return new \Google_Service_Calendar($this->getClient());
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
        (new ClientConfigurator($this->applicationName, $this->clientSecretPath, $this->credentialsPath, $this->accessToken, $this->refreshToken))->configure($client);

        return $this->client = $client;
    }
}
// vim:sw=4 ts=4 sts=4 et
