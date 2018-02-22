<?php

namespace Silvioq\GoogleCalendar\Tests;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;
use Silvioq\GoogleCalendar\Converter\ConverterInterface;
use Silvioq\GoogleCalendar\GoogleEventInterface;
use Silvioq\GoogleCalendar\GoogleCalendar;

class GoogleCalendarTest extends TestCase
{
    const CREDENTIAL_DATA = '{"access_token":"at","token_type":"Bearer","expires_in":3600,"refresh_token":"rt","created":1519068710}';
    public function setUp()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('testDir'));
        file_put_contents(vfsStream::url('testDir/c.json'), self::CREDENTIAL_DATA);
    }

    public function testSimpleCreation()
    {
        $this->assertNotNull(new GoogleCalendar());

        $mockClient = $this->getMockBuilder(\Google_Client::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockConv = $this->getMockBuilder(ConverterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->assertNotNull(new GoogleCalendar([
            'google_client' => $mockClient,
        ]));

        $this->assertNotNull(new GoogleCalendar([
            'converter' => $mockConv,
        ]));
    }

    /**
     * @depends testSimpleCreation
     */
    public function testUrlBuilding()
    {
        $mockClient = $this->getMockBuilder(\Google_Client::class)
            ->disableOriginalConstructor()
            ->getMock();


        $mockClient->expects($this->once())
            ->method('fetchAccessTokenWithAuthCode')
            ->with('a')
            ->willReturn(json_decode(self::CREDENTIAL_DATA, true))
            ;

        $calendar = new GoogleCalendar([
            'credentials_path' => vfsStream::url('testDir/credentials.json')
        ]);

        $this->assertNotNull($calendar->buildClientWithAuthToken('a', $mockClient));
        $this->assertSame(self::CREDENTIAL_DATA, file_get_contents(vfsStream::url('testDir/credentials.json')));
    }

    /**
     * @depends testSimpleCreation
     */
    public function testListCalendars()
    {
        $mockClient = $this->getMockBuilder(\Google_Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockClient->expects($this->any())
            ->method('getLogger')
            ->willReturn(new \Psr\Log\NullLogger())
            ;

        $list = new \Google_Service_Calendar_CalendarList();
        $list->setItems( [1,2,3] );

        $mockClient->expects($this->once())
            ->method('execute')
            ->willReturn($list);

        $calendar = new GoogleCalendar([
            'google_client' => $mockClient,
        ]);

        $this->assertNotNull($items = $calendar->listCalendars());
        $this->assertSame( [1,2,3], $items);
    }

    /**
     * @depends testSimpleCreation
     */
    public function testAddEvent()
    {
        $mockClient = $this->getMockBuilder(\Google_Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockClient->expects($this->any())
            ->method('getLogger')
            ->willReturn(new \Psr\Log\NullLogger())
            ;

        $eventResponse = new \Google_Service_Calendar_Event();
        $eventResponse->setId( 'id' );

        $mockClient->expects($this->once())
            ->method('execute')
            ->willReturn($eventResponse);

        $calendar = new GoogleCalendar([
            'google_client' => $mockClient,
        ]);

        $event = (new \Silvioq\GoogleCalendar\GoogleEvent())
            ->setSummary('Summary')
            ->setCalendarId('calendarId')
            ->setStart(new \DateTime('tomorrow'))
            ->setEnd(new \DateTime('tomorrow'))
            ->setAllDay(true)
            ;

        $this->assertNotNull($calendar->addEvent($event));
        $this->assertSame( 'id', $event->getEventId());
    }

    /**
     * @depends testSimpleCreation
     */
    public function testGetEvent()
    {
        $mockClient = $this->getMockBuilder(\Google_Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockClient->expects($this->any())
            ->method('getLogger')
            ->willReturn(new \Psr\Log\NullLogger())
            ;

        $eventResponse = new \Google_Service_Calendar_Event();
        $eventResponse->setId('id');
        $eventResponse->setSummary('my summary');

        $start = new \Google_Service_Calendar_EventDateTime();
        $start->setDate('1986-06-22');
        $eventResponse->setStart($start);
        $eventResponse->setEnd(clone $start);
        $eventResponse->setStatus('confirmed');

        $mockClient->expects($this->once())
            ->method('execute')
            ->willReturn($eventResponse);

        $calendar = new GoogleCalendar([
            'google_client' => $mockClient,
        ]);

        $this->assertInstanceOf(GoogleEventInterface::class, $event = $calendar->getEvent('calendar_id', 'id'));
        $this->assertSame('calendar_id',$event->getCalendarId());
        $this->assertSame('my summary',$event->getSummary());
        $this->assertSame('1986-06-22', $event->getStart()->format('Y-m-d'));
        $this->assertSame(GoogleEventInterface::STATUS_CONFIRMED, $event->getStatus());
    }


    /**
     * @depends testSimpleCreation
     */
    public function testUpdateEvent()
    {
        $mockClient = $this->getMockBuilder(\Google_Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockClient->expects($this->any())
            ->method('getLogger')
            ->willReturn(new \Psr\Log\NullLogger())
            ;

        $eventResponse = new \Google_Service_Calendar_Event();
        $eventResponse->setId('id');
        $eventResponse->setSummary('my summary');

        $start = new \Google_Service_Calendar_EventDateTime();
        $start->setDate('1986-06-22');
        $eventResponse->setStart($start);
        $eventResponse->setEnd(clone $start);

        $mockClient->expects($this->once())
            ->method('execute')
            ->with($this->callback(function($data){
                    $this->assertInstanceOf(\GuzzleHttp\Psr7\Request::class, $data);
                    $e = json_decode($data->getBody()->getContents());
                    $this->assertSame('Summary', $e->summary);
                    return true;;
                }),
                'Google_Service_Calendar_Event'
            )
            ->willReturn($eventResponse);

        $calendar = new GoogleCalendar([
            'google_client' => $mockClient,
        ]);

        $event = (new \Silvioq\GoogleCalendar\GoogleEvent())
            ->setSummary('Summary')
            ->setCalendarId('calendarId')
            ->setStart(new \DateTime('tomorrow'))
            ->setEnd(new \DateTime('tomorrow'))
            ->setAllDay(true)
            ->setEventId('id')
            ;
        $this->assertInstanceOf(GoogleCalendar::class, $event = $calendar->updateEvent($event));
    }

    /**
     * @depends testSimpleCreation
     */
    public function testDeleteEvent()
    {
        $mockClient = $this->getMockBuilder(\Google_Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockClient->expects($this->any())
            ->method('getLogger')
            ->willReturn(new \Psr\Log\NullLogger())
            ;

        $mockClient->expects($this->once())
            ->method('execute')
            ->with($this->callback(function($data){
                    $this->assertInstanceOf(\GuzzleHttp\Psr7\Request::class, $data);
                    $this->assertSame('DELETE', $data->getMethod());
                    return true;
                })
            );

        $calendar = new GoogleCalendar([
            'google_client' => $mockClient,
        ]);

        $event = (new \Silvioq\GoogleCalendar\GoogleEvent())
            ->setSummary('Summary')
            ->setCalendarId('calendarId')
            ->setStart(new \DateTime('tomorrow'))
            ->setEnd(new \DateTime('tomorrow'))
            ->setAllDay(true)
            ->setEventId('id')
            ;
        $this->assertInstanceOf(GoogleCalendar::class, $event = $calendar->deleteEvent($event));
    }
}
// vim:sw=4 ts=4 sts=4 et
