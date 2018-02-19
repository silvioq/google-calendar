<?php

namespace Silvioq\GoogleCalendar\Exception;

class InvalidAccessTokenException extends \RuntimeException
{
    private $authUrl;

    public function setAuthUrl(string $authUrl):self
    {
        $this->authUrl = $authUrl;
        return $this;
    }

    public function getAuthUrl():string
    {
        return $this->authUrl ?? '';
    }
}
