<?php

namespace Silvioq\GoogleCalendar\Tests\Converter;

use Silvioq\GoogleCalendar\Converter\GoogleEventConverter;
use Silvioq\GoogleCalendar\GoogleEventInterface;
use PHPUnit\Framework\TestCase;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventCreator;

class GoogleEventConverterTest extends TestCase
{
    public function testToGoogleConversionOnAllDay()
    {
        $event = $this->getMockBuilder(GoogleEventInterface::class)
                ->getMock();

        $event->expects($this->once())
            ->method('getSummary')
            ->willReturn('summary');

        $event->expects($this->once())
            ->method('getDescription')
            ->willReturn('description');

        $event->expects($this->once())
            ->method('getEnd')
            ->willReturn(new \DateTime('1986-06-22'));

        $event->expects($this->once())
            ->method('getStart')
            ->willReturn(new \DateTime('1986-06-22'));

        $event->expects($this->once())
            ->method('getAllDay')
            ->willReturn(true)
            ;

        $conversor = new GoogleEventConverter();
        $ge = $conversor->toGoogleEvent($event);
        $this->assertInstanceOf(Google_Service_Calendar_Event::class, $ge);
        $this->assertSame('summary', $ge->summary);
        $this->assertSame('description', $ge->description);
        $this->assertNotNull($ge->start);
        $this->assertNotNull($ge->end);
        $this->assertSame('1986-06-22', $ge->start->getDate());
        $this->assertSame('1986-06-22', $ge->end->getDate());
    }

    public function testToGoogleConversionWithTime()
    {
        $event = $this->getMockBuilder(GoogleEventInterface::class)
                ->getMock();

        $event->expects($this->once())
            ->method('getSummary')
            ->willReturn('summary');

        $event->expects($this->once())
            ->method('getDescription')
            ->willReturn('description');

        $event->expects($this->once())
            ->method('getEnd')
            ->willReturn(new \DateTime('1986-06-22 16:56'));

        $event->expects($this->once())
            ->method('getStart')
            ->willReturn(new \DateTime('1986-06-22 16:55'));

        $event->expects($this->once())
            ->method('getAllDay')
            ->willReturn(false)
            ;

        $conversor = new GoogleEventConverter();
        $ge = $conversor->toGoogleEvent($event);
        $this->assertInstanceOf(Google_Service_Calendar_Event::class, $ge);
        $this->assertSame('summary', $ge->summary);
        $this->assertSame('description', $ge->description);
        $this->assertNotNull($ge->start);
        $this->assertNotNull($ge->end);
        $this->assertSame('1986-06-22T16:55:00+00:00', $ge->start->getDatetime());
        $this->assertSame('1986-06-22T16:56:00+00:00', $ge->end->getDatetime());
    }

    public function testGoogleToOwn()
    {
        $ge = unserialize('O:29:"Google_Service_Calendar_Event":59:{s:17:"'."\0".'*'."\0".'collection_key";s:10:"recurrence";s:16:"anyoneCanAddSelf";N;s:18:"'."\0".'*'."\0".'attachmentsType";s:39:"Google_Service_Calendar_EventAttachment";s:22:"'."\0".'*'."\0".'attachmentsDataType";s:5:"array";s:16:"'."\0".'*'."\0".'attendeesType";s:37:"Google_Service_Calendar_EventAttendee";s:20:"'."\0".'*'."\0".'attendeesDataType";s:5:"array";s:16:"attendeesOmitted";N;s:7:"colorId";N;s:21:"'."\0".'*'."\0".'conferenceDataType";s:38:"Google_Service_Calendar_ConferenceData";s:25:"'."\0".'*'."\0".'conferenceDataDataType";s:0:"";s:7:"created";s:24:"2007-02-23T18:15:23.000Z";s:14:"'."\0".'*'."\0".'creatorType";s:36:"Google_Service_Calendar_EventCreator";s:18:"'."\0".'*'."\0".'creatorDataType";s:0:"";s:11:"description";N;s:10:"'."\0".'*'."\0".'endType";s:37:"Google_Service_Calendar_EventDateTime";s:14:"'."\0".'*'."\0".'endDataType";s:0:"";s:18:"endTimeUnspecified";N;s:4:"etag";s:18:""2344509160012000"";s:25:"'."\0".'*'."\0".'extendedPropertiesType";s:47:"Google_Service_Calendar_EventExtendedProperties";s:29:"'."\0".'*'."\0".'extendedPropertiesDataType";s:0:"";s:13:"'."\0".'*'."\0".'gadgetType";s:35:"Google_Service_Calendar_EventGadget";s:17:"'."\0".'*'."\0".'gadgetDataType";s:0:"";s:21:"guestsCanInviteOthers";N;s:15:"guestsCanModify";N;s:23:"guestsCanSeeOtherGuests";N;s:11:"hangoutLink";N;s:8:"htmlLink";s:116:"https://www.google.com/calendar/event?eid=MGYwdWZ0Y25vODFpY2U5YXBsanNmMWh1djggb3Z0aGRuZGY1dW03ODBqZW5kdGhvc2VwcDBAZw";s:7:"iCalUID";s:37:"0f0uftcno81ice9apljsf1huv8@google.com";s:2:"id";s:26:"0f0uftcno81ice9apljsf1huv8";s:4:"kind";s:14:"calendar#event";s:8:"location";N;s:6:"locked";N;s:16:"'."\0".'*'."\0".'organizerType";s:38:"Google_Service_Calendar_EventOrganizer";s:20:"'."\0".'*'."\0".'organizerDataType";s:0:"";s:24:"'."\0".'*'."\0".'originalStartTimeType";s:37:"Google_Service_Calendar_EventDateTime";s:28:"'."\0".'*'."\0".'originalStartTimeDataType";s:0:"";s:11:"privateCopy";N;s:10:"recurrence";N;s:16:"recurringEventId";N;s:16:"'."\0".'*'."\0".'remindersType";s:38:"Google_Service_Calendar_EventReminders";s:20:"'."\0".'*'."\0".'remindersDataType";s:0:"";s:8:"sequence";i:1;s:13:"'."\0".'*'."\0".'sourceType";s:35:"Google_Service_Calendar_EventSource";s:17:"'."\0".'*'."\0".'sourceDataType";s:0:"";s:12:"'."\0".'*'."\0".'startType";s:37:"Google_Service_Calendar_EventDateTime";s:16:"'."\0".'*'."\0".'startDataType";s:0:"";s:6:"status";s:9:"confirmed";s:7:"summary";s:18:"Vs Arsenal - Local";s:12:"transparency";N;s:7:"updated";s:24:"2007-02-23T18:16:20.006Z";s:10:"visibility";N;s:25:"'."\0".'*'."\0".'internal_gapi_mappings";a:0:{}s:12:"'."\0".'*'."\0".'modelData";a:0:{}s:12:"'."\0".'*'."\0".'processed";a:0:{}s:7:"creator";O:36:"Google_Service_Calendar_EventCreator":7:{s:11:"displayName";s:13:"Silvio Quadri";s:5:"email";s:17:"silvioq@gmail.com";s:2:"id";N;s:4:"self";N;s:25:"'."\0".'*'."\0".'internal_gapi_mappings";a:0:{}s:12:"'."\0".'*'."\0".'modelData";a:0:{}s:12:"'."\0".'*'."\0".'processed";a:0:{}}s:9:"organizer";O:38:"Google_Service_Calendar_EventOrganizer":7:{s:11:"displayName";s:25:"Partidos de Nueva Chicago";s:5:"email";s:52:"ovthdndf5um780jendthosepp0@group.calendar.google.com";s:2:"id";N;s:4:"self";b:1;s:25:"'."\0".'*'."\0".'internal_gapi_mappings";a:0:{}s:12:"'."\0".'*'."\0".'modelData";a:0:{}s:12:"'."\0".'*'."\0".'processed";a:0:{}}s:5:"start";O:37:"Google_Service_Calendar_EventDateTime":6:{s:4:"date";N;s:8:"dateTime";s:25:"2007-02-23T19:00:00-03:00";s:8:"timeZone";N;s:25:"'."\0".'*'."\0".'internal_gapi_mappings";a:0:{}s:12:"'."\0".'*'."\0".'modelData";a:0:{}s:12:"'."\0".'*'."\0".'processed";a:0:{}}s:3:"end";O:37:"Google_Service_Calendar_EventDateTime":6:{s:4:"date";N;s:8:"dateTime";s:25:"2007-02-23T21:00:00-03:00";s:8:"timeZone";N;s:25:"'."\0".'*'."\0".'internal_gapi_mappings";a:0:{}s:12:"'."\0".'*'."\0".'modelData";a:0:{}s:12:"'."\0".'*'."\0".'processed";a:0:{}}s:9:"reminders";O:38:"Google_Service_Calendar_EventReminders":7:{s:17:"'."\0".'*'."\0".'collection_key";s:9:"overrides";s:16:"'."\0".'*'."\0".'overridesType";s:37:"Google_Service_Calendar_EventReminder";s:20:"'."\0".'*'."\0".'overridesDataType";s:5:"array";s:10:"useDefault";b:1;s:25:"'."\0".'*'."\0".'internal_gapi_mappings";a:0:{}s:12:"'."\0".'*'."\0".'modelData";a:0:{}s:12:"'."\0".'*'."\0".'processed";a:0:{}}}');
        $this->assertNotNull($ge);

        $conversor = new GoogleEventConverter();
        $this->assertInstanceOf( GoogleEventInterface::class, $event = $conversor->toEvent($ge));
        $this->assertSame('Vs Arsenal - Local', $event->getSummary());
        $this->assertSame(null, $event->getDescription());
        $this->assertSame('0f0uftcno81ice9apljsf1huv8', $event->getEventId());
        $this->assertEquals(new \DateTime('2007-02-23T19:00:00.000000-0300'), $event->getStart());
        $this->assertEquals(new \DateTime('2007-02-23T21:00:00.000000-0300'), $event->getEnd());
        $this->assertSame(false, $event->getAllDay());
    }

    public function testGoogleToOwnAllDay()
    {
        $ge = unserialize('O:29:"Google_Service_Calendar_Event":59:{s:17:"'."\0".'*'."\0".'collection_key";s:10:"recurrence";s:16:"anyoneCanAddSelf";N;s:18:"'."\0".'*'."\0".'attachmentsType";s:39:"Google_Service_Calendar_EventAttachment";s:22:"'."\0".'*'."\0".'attachmentsDataType";s:5:"array";s:16:"'."\0".'*'."\0".'attendeesType";s:37:"Google_Service_Calendar_EventAttendee";s:20:"'."\0".'*'."\0".'attendeesDataType";s:5:"array";s:16:"attendeesOmitted";N;s:7:"colorId";N;s:21:"'."\0".'*'."\0".'conferenceDataType";s:38:"Google_Service_Calendar_ConferenceData";s:25:"'."\0".'*'."\0".'conferenceDataDataType";s:0:"";s:7:"created";s:24:"2018-02-19T15:05:46.000Z";s:14:"'."\0".'*'."\0".'creatorType";s:36:"Google_Service_Calendar_EventCreator";s:18:"'."\0".'*'."\0".'creatorDataType";s:0:"";s:11:"description";s:37:"Todo el día, esta es la descripción";s:10:"'."\0".'*'."\0".'endType";s:37:"Google_Service_Calendar_EventDateTime";s:14:"'."\0".'*'."\0".'endDataType";s:0:"";s:18:"endTimeUnspecified";N;s:4:"etag";s:18:""3038105492735000"";s:25:"'."\0".'*'."\0".'extendedPropertiesType";s:47:"Google_Service_Calendar_EventExtendedProperties";s:29:"'."\0".'*'."\0".'extendedPropertiesDataType";s:0:"";s:13:"'."\0".'*'."\0".'gadgetType";s:35:"Google_Service_Calendar_EventGadget";s:17:"'."\0".'*'."\0".'gadgetDataType";s:0:"";s:21:"guestsCanInviteOthers";N;s:15:"guestsCanModify";N;s:23:"guestsCanSeeOtherGuests";N;s:11:"hangoutLink";N;s:8:"htmlLink";s:116:"https://www.google.com/calendar/event?eid=MjMwdDM4bTcxcHFpcHBkbzN2M2o3cGFpMDIgb3Z0aGRuZGY1dW03ODBqZW5kdGhvc2VwcDBAZw";s:7:"iCalUID";s:37:"230t38m71pqippdo3v3j7pai02@google.com";s:2:"id";s:26:"230t38m71pqippdo3v3j7pai02";s:4:"kind";s:14:"calendar#event";s:8:"location";s:78:"Estadio de Nueva Chicago, Justo Antonio Suárez 6913, C1440BHI CABA, Argentina";s:6:"locked";N;s:16:"'."\0".'*'."\0".'organizerType";s:38:"Google_Service_Calendar_EventOrganizer";s:20:"'."\0".'*'."\0".'organizerDataType";s:0:"";s:24:"'."\0".'*'."\0".'originalStartTimeType";s:37:"Google_Service_Calendar_EventDateTime";s:28:"'."\0".'*'."\0".'originalStartTimeDataType";s:0:"";s:11:"privateCopy";N;s:10:"recurrence";N;s:16:"recurringEventId";N;s:16:"'."\0".'*'."\0".'remindersType";s:38:"Google_Service_Calendar_EventReminders";s:20:"'."\0".'*'."\0".'remindersDataType";s:0:"";s:8:"sequence";i:0;s:13:"'."\0".'*'."\0".'sourceType";s:35:"Google_Service_Calendar_EventSource";s:17:"'."\0".'*'."\0".'sourceDataType";s:0:"";s:12:"'."\0".'*'."\0".'startType";s:37:"Google_Service_Calendar_EventDateTime";s:16:"'."\0".'*'."\0".'startDataType";s:0:"";s:6:"status";s:9:"confirmed";s:7:"summary";s:12:"Todo el día";s:12:"transparency";s:11:"transparent";s:7:"updated";s:24:"2018-02-19T15:05:46.422Z";s:10:"visibility";N;s:25:"'."\0".'*'."\0".'internal_gapi_mappings";a:0:{}s:12:"'."\0".'*'."\0".'modelData";a:0:{}s:12:"'."\0".'*'."\0".'processed";a:0:{}s:7:"creator";O:36:"Google_Service_Calendar_EventCreator":7:{s:11:"displayName";N;s:5:"email";s:17:"silvioq@gmail.com";s:2:"id";N;s:4:"self";N;s:25:"'."\0".'*'."\0".'internal_gapi_mappings";a:0:{}s:12:"'."\0".'*'."\0".'modelData";a:0:{}s:12:"'."\0".'*'."\0".'processed";a:0:{}}s:9:"organizer";O:38:"Google_Service_Calendar_EventOrganizer":7:{s:11:"displayName";s:25:"Partidos de Nueva Chicago";s:5:"email";s:52:"ovthdndf5um780jendthosepp0@group.calendar.google.com";s:2:"id";N;s:4:"self";b:1;s:25:"'."\0".'*'."\0".'internal_gapi_mappings";a:0:{}s:12:"'."\0".'*'."\0".'modelData";a:0:{}s:12:"'."\0".'*'."\0".'processed";a:0:{}}s:5:"start";O:37:"Google_Service_Calendar_EventDateTime":6:{s:4:"date";s:10:"2018-03-19";s:8:"dateTime";N;s:8:"timeZone";N;s:25:"'."\0".'*'."\0".'internal_gapi_mappings";a:0:{}s:12:"'."\0".'*'."\0".'modelData";a:0:{}s:12:"'."\0".'*'."\0".'processed";a:0:{}}s:3:"end";O:37:"Google_Service_Calendar_EventDateTime":6:{s:4:"date";s:10:"2018-03-20";s:8:"dateTime";N;s:8:"timeZone";N;s:25:"'."\0".'*'."\0".'internal_gapi_mappings";a:0:{}s:12:"'."\0".'*'."\0".'modelData";a:0:{}s:12:"'."\0".'*'."\0".'processed";a:0:{}}s:9:"reminders";O:38:"Google_Service_Calendar_EventReminders":7:{s:17:"'."\0".'*'."\0".'collection_key";s:9:"overrides";s:16:"'."\0".'*'."\0".'overridesType";s:37:"Google_Service_Calendar_EventReminder";s:20:"'."\0".'*'."\0".'overridesDataType";s:5:"array";s:10:"useDefault";b:0;s:25:"'."\0".'*'."\0".'internal_gapi_mappings";a:0:{}s:12:"'."\0".'*'."\0".'modelData";a:0:{}s:12:"'."\0".'*'."\0".'processed";a:0:{}}}');
        $this->assertNotNull($ge);

        $conversor = new GoogleEventConverter();
        $this->assertInstanceOf( GoogleEventInterface::class, $event = $conversor->toEvent($ge));
        $this->assertSame('Todo el día', $event->getSummary());
        $this->assertSame('Todo el día, esta es la descripción', $event->getDescription());
        $this->assertSame('230t38m71pqippdo3v3j7pai02', $event->getEventId());
        $this->assertEquals(new \DateTime('2018-03-19T00:00:00.000000+0000'), $event->getStart());
        $this->assertEquals(new \DateTime('2018-03-20T00:00:00.000000+0000'), $event->getEnd());
        $this->assertSame(true, $event->getAllDay());
    }

    /**
     * Test deconversion from Google_Service_Calendar_Attendee
     */
    public function testAttendeeDeconversion()
    {
        $ge = unserialize('O:29:"Google_Service_Calendar_Event":60:{s:17:"' . "\0" . '*' . "\0" . 'collection_key";s:10:"recurrence";s:16:"anyoneCanAddSelf";N;s:18:"' . "\0" . '*' . "\0" . 'attachmentsType";s:39:"Google_Service_Calendar_EventAttachment";s:22:"' . "\0" . '*' . "\0" . 'attachmentsDataType";s:5:"array";s:16:"' . "\0" . '*' . "\0" . 'attendeesType";s:37:"Google_Service_Calendar_EventAttendee";s:20:"' . "\0" . '*' . "\0" . 'attendeesDataType";s:5:"array";s:16:"attendeesOmitted";N;s:7:"colorId";N;s:21:"' . "\0" . '*' . "\0" . 'conferenceDataType";s:38:"Google_Service_Calendar_ConferenceData";s:25:"' . "\0" . '*' . "\0" . 'conferenceDataDataType";s:0:"";s:7:"created";s:24:"2018-02-22T10:47:47.000Z";s:14:"' . "\0" . '*' . "\0" . 'creatorType";s:36:"Google_Service_Calendar_EventCreator";s:18:"' . "\0" . '*' . "\0" . 'creatorDataType";s:0:"";s:11:"description";N;s:10:"' . "\0" . '*' . "\0" . 'endType";s:37:"Google_Service_Calendar_EventDateTime";s:14:"' . "\0" . '*' . "\0" . 'endDataType";s:0:"";s:18:"endTimeUnspecified";N;s:4:"etag";s:18:""3038598710503000"";s:25:"' . "\0" . '*' . "\0" . 'extendedPropertiesType";s:47:"Google_Service_Calendar_EventExtendedProperties";s:29:"' . "\0" . '*' . "\0" . 'extendedPropertiesDataType";s:0:"";s:13:"' . "\0" . '*' . "\0" . 'gadgetType";s:35:"Google_Service_Calendar_EventGadget";s:17:"' . "\0" . '*' . "\0" . 'gadgetDataType";s:0:"";s:21:"guestsCanInviteOthers";N;s:15:"guestsCanModify";N;s:23:"guestsCanSeeOtherGuests";N;s:11:"hangoutLink";N;s:8:"htmlLink";s:90:"https://www.google.com/calendar/event?eid=N2czMDZqazhtamIzOWxsYmxiNDg1ZGcxbGcgc2lsdmlvcUBt";s:7:"iCalUID";s:37:"7g306jk8mjb39llblb485dg1lg@google.com";s:2:"id";s:26:"7g306jk8mjb39llblb485dg1lg";s:4:"kind";s:14:"calendar#event";s:8:"location";N;s:6:"locked";N;s:16:"' . "\0" . '*' . "\0" . 'organizerType";s:38:"Google_Service_Calendar_EventOrganizer";s:20:"' . "\0" . '*' . "\0" . 'organizerDataType";s:0:"";s:24:"' . "\0" . '*' . "\0" . 'originalStartTimeType";s:37:"Google_Service_Calendar_EventDateTime";s:28:"' . "\0" . '*' . "\0" . 'originalStartTimeDataType";s:0:"";s:11:"privateCopy";N;s:10:"recurrence";N;s:16:"recurringEventId";N;s:16:"' . "\0" . '*' . "\0" . 'remindersType";s:38:"Google_Service_Calendar_EventReminders";s:20:"' . "\0" . '*' . "\0" . 'remindersDataType";s:0:"";s:8:"sequence";i:1;s:13:"' . "\0" . '*' . "\0" . 'sourceType";s:35:"Google_Service_Calendar_EventSource";s:17:"' . "\0" . '*' . "\0" . 'sourceDataType";s:0:"";s:12:"' . "\0" . '*' . "\0" . 'startType";s:37:"Google_Service_Calendar_EventDateTime";s:16:"' . "\0" . '*' . "\0" . 'startDataType";s:0:"";s:6:"status";s:9:"tentative";s:7:"summary";s:17:"Evento de mañana";s:12:"transparency";N;s:7:"updated";s:24:"2018-02-22T12:24:03.169Z";s:10:"visibility";N;s:25:"' . "\0" . '*' . "\0" . 'internal_gapi_mappings";a:0:{}s:12:"' . "\0" . '*' . "\0" . 'modelData";a:0:{}s:12:"' . "\0" . '*' . "\0" . 'processed";a:0:{}s:7:"creator";O:36:"Google_Service_Calendar_EventCreator":7:{s:11:"displayName";N;s:5:"email";s:17:"silvioq@gmail.com";s:2:"id";N;s:4:"self";b:1;s:25:"' . "\0" . '*' . "\0" . 'internal_gapi_mappings";a:0:{}s:12:"' . "\0" . '*' . "\0" . 'modelData";a:0:{}s:12:"' . "\0" . '*' . "\0" . 'processed";a:0:{}}s:9:"organizer";O:38:"Google_Service_Calendar_EventOrganizer":7:{s:11:"displayName";N;s:5:"email";s:17:"silvioq@gmail.com";s:2:"id";N;s:4:"self";b:1;s:25:"' . "\0" . '*' . "\0" . 'internal_gapi_mappings";a:0:{}s:12:"' . "\0" . '*' . "\0" . 'modelData";a:0:{}s:12:"' . "\0" . '*' . "\0" . 'processed";a:0:{}}s:5:"start";O:37:"Google_Service_Calendar_EventDateTime":6:{s:4:"date";s:10:"2018-02-23";s:8:"dateTime";N;s:8:"timeZone";N;s:25:"' . "\0" . '*' . "\0" . 'internal_gapi_mappings";a:0:{}s:12:"' . "\0" . '*' . "\0" . 'modelData";a:0:{}s:12:"' . "\0" . '*' . "\0" . 'processed";a:0:{}}s:3:"end";O:37:"Google_Service_Calendar_EventDateTime":6:{s:4:"date";s:10:"2018-02-23";s:8:"dateTime";N;s:8:"timeZone";N;s:25:"' . "\0" . '*' . "\0" . 'internal_gapi_mappings";a:0:{}s:12:"' . "\0" . '*' . "\0" . 'modelData";a:0:{}s:12:"' . "\0" . '*' . "\0" . 'processed";a:0:{}}s:9:"attendees";a:1:{i:0;O:37:"Google_Service_Calendar_EventAttendee":13:{s:16:"additionalGuests";N;s:7:"comment";N;s:11:"displayName";N;s:5:"email";s:26:"silvioq.invitedo@gmail.com";s:2:"id";N;s:8:"optional";N;s:9:"organizer";N;s:8:"resource";N;s:14:"responseStatus";s:11:"needsAction";s:4:"self";N;s:25:"' . "\0" . '*' . "\0" . 'internal_gapi_mappings";a:0:{}s:12:"' . "\0" . '*' . "\0" . 'modelData";a:0:{}s:12:"' . "\0" . '*' . "\0" . 'processed";a:0:{}}}s:9:"reminders";O:38:"Google_Service_Calendar_EventReminders":8:{s:17:"' . "\0" . '*' . "\0" . 'collection_key";s:9:"overrides";s:16:"' . "\0" . '*' . "\0" . 'overridesType";s:37:"Google_Service_Calendar_EventReminder";s:20:"' . "\0" . '*' . "\0" . 'overridesDataType";s:5:"array";s:10:"useDefault";b:0;s:25:"' . "\0" . '*' . "\0" . 'internal_gapi_mappings";a:0:{}s:12:"' . "\0" . '*' . "\0" . 'modelData";a:0:{}s:12:"' . "\0" . '*' . "\0" . 'processed";a:0:{}s:9:"overrides";a:1:{i:0;O:37:"Google_Service_Calendar_EventReminder":5:{s:6:"method";s:5:"popup";s:7:"minutes";i:10;s:25:"' . "\0" . '*' . "\0" . 'internal_gapi_mappings";a:0:{}s:12:"' . "\0" . '*' . "\0" . 'modelData";a:0:{}s:12:"' . "\0" . '*' . "\0" . 'processed";a:0:{}}}}}');
        
        $this->assertNotNull($ge);

        $conversor = new GoogleEventConverter();
        $this->assertInstanceOf( GoogleEventInterface::class, $event = $conversor->toEvent($ge));

        $this->assertCount(1,$event->getAttendees());
        $this->assertSame('silvioq.invitedo@gmail.com',$event->getAttendees()[0]);
    }

}
// vim:sw=4 ts=4 sts=4 et
