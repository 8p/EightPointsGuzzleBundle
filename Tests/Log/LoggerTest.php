<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\Log;

use EightPoints\Bundle\GuzzleBundle\Log\Logger;
use EightPoints\Bundle\GuzzleBundle\Log\LogMessage;

/**
 * Class LoggerTest
 *
 * @package   EightPoints\Bundle\GuzzleBundle\Tests\Log
 *
 * @version   2.1
 * @since     2015-05
 */
class LoggerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test Instance
     *
     * @version 2.1
     * @since   2015-05
     *
     * covers  EightPoints\Bundle\GuzzleBundle\Log\Logger::__construct
     */
    public function testConstruct()
    {
        $this->assertInstanceOf('EightPoints\Bundle\GuzzleBundle\Log\LoggerInterface', new Logger());
    }

    /**
     * Test Messages
     *
     * @version 2.1
     * @since   2015-05
     *
     * @covers  EightPoints\Bundle\GuzzleBundle\Log\Logger::hasMessages
     */
    public function testHasMessages()
    {
        $logger = new Logger();
        $this->assertFalse($logger->hasMessages());

        $logger->log('test', 'test message');
        $this->assertTrue($logger->hasMessages());
    }

    /**
     * Test Returning Messages
     *
     * @version 2.1
     * @since   2015-05
     *
     * @covers  EightPoints\Bundle\GuzzleBundle\Log\Logger::getMessages
     */
    public function testGetMessages()
    {
        $logger = new Logger();
        $this->assertCount(0, $logger->getMessages());

        $logger->log('test', 'test message');
        $this->assertCount(1, $logger->getMessages());

        $logger->log('test', 'second test message');
        $this->assertCount(2, $logger->getMessages());

        $messages = $logger->getMessages();

        /** @var LogMessage $message */
        foreach ($messages as $message) {

            $this->assertInstanceOf('EightPoints\Bundle\GuzzleBundle\Log\LogMessage', $message);
            $this->assertSame('test', $message->getLevel());
            $this->assertContains('test message', $message->getMessage());
            $this->assertNull($message->getRequest());
            $this->assertNull($message->getResponse());
        }

        $uriMock = $this->getMockBuilder('GuzzleHttp\Psr7\Uri')
                            ->disableOriginalConstructor()
                            ->getMock();

        $requestMock = $this->getMockBuilder('GuzzleHttp\Psr7\Request')
                            ->disableOriginalConstructor()
                            ->getMock();

        $requestMock->method('getHeaders')->willReturn([]);
        $requestMock->method('getUri')->willReturn($uriMock);

        $logger->log('info', 'info message', ['request' => $requestMock]);

        $message = $logger->getMessages()[2];
        $this->assertSame('info', $message->getLevel());
        $this->assertSame('info message', $message->getMessage());

        $this->assertInstanceOf('EightPoints\Bundle\GuzzleBundle\Log\LogRequest', $message->getRequest());
    }

    /**
     * Test Clearing Messages
     *
     * @version 2.1
     * @since   2015-05
     *
     * @covers  EightPoints\Bundle\GuzzleBundle\Log\Logger::clear
     */
    public function testClear()
    {
        $logger = new Logger();
        $logger->log('test', 'test message');
        $logger->log('test', 'test message');

        $this->assertCount(2, $logger->getMessages());
        $this->assertTrue($logger->hasMessages());

        $logger->clear();

        $this->assertCount(0, $logger->getMessages());
        $this->assertFalse($logger->hasMessages());
    }
}
