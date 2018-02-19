<?php

namespace Silvioq\GoogleCalendar\Tests;

use Silvioq\GoogleCalendar\GoogleEvent;
use PHPUnit\Framework\TestCase;


Class GoogleEventTest extends TestCase
{
    public function testGoogleEvent()
    {
        $event = (new GoogleEvent())
            ->setCalendarId('calendarId')
            ->setEventId('eventId')
            ->setAllDay(true)
            ->setStart(new \DateTime('1986-06-22'))
            ->setEnd(new \DateTime('1986-06-25'))
            ->setDescription('Description')
            ->setSummary('Summary')
            ->addAttendee('a@gmail.com')
            ->addAttendee('a2@gmail.com')
            ;

        $this->assertSame('calendarId', $event->getCalendarId());
        $this->assertSame('eventId', $event->getEventId());
        $this->assertSame(true, $event->getAllDay());
        $this->assertSame('1986-06-22', $event->getStart()->format('Y-m-d'));
        $this->assertSame('1986-06-25', $event->getEnd()->format('Y-m-d'));
        $this->assertSame('Summary', $event->getSummary());
        $this->assertSame('Description', $event->getDescription());
        $this->assertSame(['a@gmail.com', 'a2@gmail.com'], $event->getAttendees());
    }

    public function testGoogleEventAttendeeDuplicates()
    {
        $event = (new GoogleEvent())
            ->addAttendee('a@gmail.com')
            ->addAttendee('a@gmail.com')
            ->addAttendee('b@gmail.com')
            ->addAttendee('b@gmail.com')
            ;
        $this->assertSame(['a@gmail.com', 'b@gmail.com'], $event->getAttendees());
    }

    public function testGoogleEventRemoveAteendee()
    {
        $event = (new GoogleEvent())
            ->addAttendee('a@gmail.com')
            ->addAttendee('a@gmail.com')
            ->addAttendee('b@gmail.com')
            ->addAttendee('b@gmail.com')
            ;
        $this->assertSame(['a@gmail.com', 'b@gmail.com'], $event->getAttendees());

        $event->removeAttendee('a@gmail.com');
        $this->assertSame(['b@gmail.com'], $event->getAttendees());

        $event->removeAttendee('a@gmail.com');
        $this->assertSame(['b@gmail.com'], $event->getAttendees());

        $event->removeAttendee('b@gmail.com');
        $this->assertSame([], $event->getAttendees());
    }

    /**
     * @expectedException \LogicException
     */
    public function testInvalidStartEventDate()
    {
        $event = new GoogleEvent();
        $event->getStart();
    }

    /**
     * @expectedException \LogicException
     */
    public function testInvalidEndEventDate()
    {
        $event = new GoogleEvent();
        $event->getEnd();
    }
}

// vim:sw=4 ts=4 sts=4 et
