<?php

namespace Silvioq\GoogleCalendar\Converter;

use Google_Service_Calendar_Event;
use Silvioq\GoogleCalendar\GoogleEventInterface;
use Silvioq\GoogleCalendar\GoogleEvent;

/**
 * Class GoogleEventConverter
 *
 * Google Calendar converter
 * Inspired on fungio/FungioGoogleCalendarBundle, @see https://github.com/fungio/FungioGoogleCalendarBundle/blob/master/Service/GoogleCalendar.php
 *
 * @author Silvioq <silvioq@gmail.com>
 */
class GoogleEventConverter implements ConverterInterface
{
    /**
     * Converts GoogleEventInterface in native google event
     *
     * @param GoogleEventInterface $event
     *
     * @return Google_Service_Calendar_Event
     */
    public function toGoogleEvent(GoogleEventInterface $event):Google_Service_Calendar_Event
    {
        $googleEvent = new \Google_Service_Calendar_Event();

        $googleEvent->setSummary($event->getSummary());
        $start = new \Google_Service_Calendar_EventDateTime();
        $end = new \Google_Service_Calendar_EventDateTime();
        if ($event->getAllDay()) {
            $start->setDate($event->getStart()->format('Y-m-d'));
            $end->setDate($event->getEnd()->format('Y-m-d'));
        } else {
            $start->setDateTime($event->getStart()->format(\DateTime::RFC3339));
            $end->setDateTime($event->getEnd()->format(\DateTime::RFC3339));
        }
        $googleEvent->setStart($start);
        $googleEvent->setEnd($end);

        $googleEvent->setStatus('tentative');
        $googleEvent->setDescription($event->getDescription());

        $googleEvent->attendees = array_map( function($email) {
                $attendee = new \Google_Service_Calendar_EventAttendee();
                $attendee->setEmail($email);
                return $attendee;
            }, $event->getAttendees());

        return $googleEvent;
    }

    /**
     * Build event from Google native event
     */
    public function toEvent(Google_Service_Calendar_Event $googleEvent):GoogleEventInterface
    {
        $event = new GoogleEvent();

        $event->setSummary($googleEvent->getSummary())
            ->setDescription($googleEvent->getDescription())
            ->setEventId($googleEvent->getId());

        $start = $googleEvent->getStart();
        if ($start->getDate()) {
            $event->setStart(new \DateTime($start->getDate()));
            $event->setAllDay(true);
        } else {
            $event->setStart(new \DateTime($start->getDateTime()));
            $event->setAllDay(false);
        }

        if ($start->getTimezone() )
            $event->getStart()->setTimezone(new \DateTimeZone($start->getTimezone()));

        $end = $googleEvent->getStart();
        if ($end->getDate()) {
            $event->setEnd(new \DateTime($end->getDate()));
        } else {
            $event->setEnd(new \DateTime($end->getDateTime()));
        }

        if ($end->getTimezone() )
            $event->getEnd()->setTimezone(new \DateTimeZone($end->getTimezone()));

        return $event;
    }
}
// vim:sw=4 ts=4 sts=4 et
