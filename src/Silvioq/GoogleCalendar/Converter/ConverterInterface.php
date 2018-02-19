<?php

namespace Silvioq\GoogleCalendar\Converter;

use Google_Service_Calendar_Event;
use Silvioq\GoogleCalendar\GoogleEventInterface;

interface ConverterInterface
{
    public function toGoogleEvent(GoogleEventInterface $event):Google_Service_Calendar_Event;
    public function toEvent(Google_Service_Calendar_Event $googleEvent):GoogleEventInterface;
}
// vim:sw=4 ts=4 sts=4 et
