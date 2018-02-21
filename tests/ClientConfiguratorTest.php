<?php

namespace Silvioq\GoogleCalendar\Tests;

use PHPUnit\Framework\TestCase;
use Silvioq\GoogleCalendar\ClientConfigurator;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;

class ClientConfiguratorTest extends TestCase
{
    const CREDENTIAL_DATA = '{"access_token":"at","token_type":"Bearer","expires_in":3600,"refresh_token":"rt","created":1519068710}';
    public function setUp()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('testDir'));
        file_put_contents(vfsStream::url('testDir/c.json'), self::CREDENTIAL_DATA);
    }

    public function testClientConfigurator()
    {
        $mockClient = $this->getMockBuilder(\Google_Client::class)
            ->disableOriginalConstructor()
            ->getMock();

        $mockClient->expects($this->once())
            ->method('setApplicationName')
            ->with(__CLASS__)
            ;
        $mockClient->expects($this->once())
            ->method('setApplicationName')
            ->with(__CLASS__)
            ;
        $mockClient->expects($this->once())
            ->method('setScopes')
            ;
        $mockClient->expects($this->once())
            ->method('setApprovalPrompt')
            ;
        $mockClient->expects($this->once())
            ->method('setAccessType')
            ;
        $mockClient->expects($this->once())
            ->method('isAccessTokenExpired')
            ->with()
            ->willReturn(false);

        $configurator = new ClientConfigurator(__CLASS__, vfsStream::url('testDir/cs.json'), vfsStream::url('testDir/c.json'), 'at', 'rt');
        $configurator->configure($mockClient);
    }

    public function testClientConfigurationWithRefresh()
    {
        $this->markTestIncomplete();
    }

}

// vim:sw=4 ts=4 sts=4 et
