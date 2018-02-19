<?php

namespace Silvioq\GoogleCalendar;

/**
 * Interface GoogleEventInterface
 *
 * @author Silvioq <silvioq@gmail.com>
 */
interface GoogleEventInterface
{
    public function setCalendarId(string $calendarId):GoogleEventInterface;
    public function getCalendarId():string;

    public function setEventId(string $eventId):GoogleEventInterface;
    public function getEventId():string;

    public function setStart(\DateTime $start):GoogleEventInterface;
    public function getStart():\DateTime;

    public function setEnd(\DateTime $end):GoogleEventInterface;
    public function getEnd():\DateTime;

    public function setSummary($summary):GoogleEventInterface;
    public function getSummary();

    public function setDescription($description):GoogleEventInterface;
    public function getDescription();

    public function addAttendee(string $attendee):GoogleEventInterface;
    public function removeAttendee(string $attendee):GoogleEventInterface;
    public function getAttendees():array;

    public function setAllDay(bool $allDay):GoogleEventInterface;
    public function getAllDay():bool;
}
// vim:sw=4 ts=4 sts=4 et
