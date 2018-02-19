<?php

namespace Silvioq\GoogleCalendar;

/**
 * Class GoogleEvent
 *
 * @author Silvioq <silvioq@gmail.com>
 */
class GoogleEvent implements GoogleEventInterface
{
    /**
     * @var string
     */
    private $calendarId;

    /**
     * @var string
     */
    private $eventId;

    /**
     * @var \DateTime
     */
    private $start;

    /**
     * @var \DateTime
     */
    private $end;

    /**
     * @var string|null
     */
    private $summary;

    /**
     * @var string|null
     */
    private $description;

    /**
     * @var array
     */
    private $attendees = array();

    /**
     * @var bool
     */
    private $allDay = false;


    public function setCalendarId(string $calendarId):GoogleEventInterface
    {
        $this->calendarId = $calendarId;

        return $this;
    }

    public function getCalendarId():string
    {
        return $this->calendarId;
    }

    public function setEventId(string $eventId):GoogleEventInterface
    {
        $this->eventId = $eventId;

        return $this;
    }

    public function getEventId():string
    {
        return $this->eventId;
    }

    public function setStart(\DateTime $start):GoogleEventInterface
    {
        $this->start = $start;

        return $this;
    }

    public function getStart():\DateTime
    {
        if (null === $this->start)
            throw new \LogicException('Must set event start first.');

        return $this->start;
    }

    public function setEnd(\DateTime $end):GoogleEventInterface
    {
        $this->end = $end;

        return $this;
    }

    public function getEnd():\DateTime
    {
        if (null === $this->end)
            throw new \LogicException('Must set event end first.');

        return $this->end;
    }

    public function setSummary($summary):GoogleEventInterface
    {
        $this->summary = $summary;

        return $this;
    }

    public function getSummary()
    {
        return $this->summary;
    }

    public function setDescription($description):GoogleEventInterface
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function addAttendee(string $attendee):GoogleEventInterface
    {
        if (!in_array($attendee, $this->attendees)) {
            array_push($this->attendees, $attendee);
        }

        return $this;
    }

    public function removeAttendee(string $attendee):GoogleEventInterface
    {
        foreach ($this->attendees as $k => $e) {
            if ($e === $attendee) {
                unset($this->attendees[$k]);
                $this->attendees = array_values($this->attendees);
                return $this;
            }
        }

        return $this;
    }

    public function getAttendees():array
    {
        return $this->attendees;
    }

    public function setAllDay(bool $allDay):GoogleEventInterface
    {
        $this->allDay = $allDay;

        return $this;
    }

    public function getAllDay():bool
    {
        return $this->allDay;
    }
}
// vim:sw=4 ts=4 sts=4 et
