<?php

namespace Silvioq\GoogleCalendar;

class ClientConfigurator
{
    private $applicationName;
    private $clientSecretPath;
    private $credentialsPath;
    private $accessToken;
    private $refreshToken;

    public function __construct($applicationName, $clientSecretPath, $credentialsPath, $accessToken, $refreshToken)
    {
        $this->applicationName = $applicationName;
        $this->clientSecretPath = $clientSecretPath;
        $this->credentialsPath = $credentialsPath;
        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
    }

    public function configure(\Google_Client $client):self
    {
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

        return $this;
    }
}
// vim:sw=4 ts=4 sts=4 et
