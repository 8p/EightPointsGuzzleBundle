<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\Log;

use EightPoints\Bundle\GuzzleBundle\Log\Logger;
use EightPoints\Bundle\GuzzleBundle\Log\LoggerInterface;
use EightPoints\Bundle\GuzzleBundle\Log\LogMessage;
use EightPoints\Bundle\GuzzleBundle\Log\LogRequest;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

/**
 * Class LoggerTest
 *
 * @package   EightPoints\Bundle\GuzzleBundle\Tests\Log
 *
 * @version   2.1
 * @since     2015-05
 */
class LoggerTest extends TestCase
{
    /**
     * Test Instance
     *
     * @version 2.1
     * @since   2015-05
     */
    public function testConstruct()
    {
        $this->assertInstanceOf(LoggerInterface::class, new Logger());
    }

    /**
     * Test Messages
     *
     * @version 2.1
     * @since   2015-05
     *
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\Logger::hasMessages
     */
    public function testHasMessages()
    {
        $logger = new Logger();
        $this->assertFalse($logger->hasMessages());

        $logger->log(LogLevel::ERROR, 'test message');
        $this->assertTrue($logger->hasMessages());
    }

    /**
     * Test Returning Messages
     *
     * @version 2.1
     * @since   2015-05
     *
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\Logger::getMessages
     */
    public function testGetMessages()
    {
        $logger = new Logger();
        $this->assertCount(0, $logger->getMessages());

        $logger->log(LogLevel::ERROR, 'test message');
        $this->assertCount(1, $logger->getMessages());

        $logger->log(LogLevel::ERROR, 'second test message');
        $this->assertCount(2, $logger->getMessages());

        $messages = $logger->getMessages();

        /** @var LogMessage $message */
        foreach ($messages as $message) {
            $this->assertInstanceOf(LogMessage::class, $message);
            $this->assertSame(LogLevel::ERROR, $message->getLevel());
            $this->assertContains('test message', $message->getMessage());
            $this->assertNull($message->getRequest());
            $this->assertNull($message->getResponse());
        }

        $uriMock = $this->getMockBuilder(Uri::class)
                            ->disableOriginalConstructor()
                            ->getMock();

        $requestMock = $this->getMockBuilder(Request::class)
                            ->disableOriginalConstructor()
                            ->getMock();

        $requestMock->method('getHeaders')->willReturn([]);
        $requestMock->method('getUri')->willReturn($uriMock);

        $logger->log(LogLevel::INFO, 'info message', ['request' => $requestMock]);

        $message = $logger->getMessages()[2];
        $this->assertSame(LogLevel::INFO, $message->getLevel());
        $this->assertSame('info message', $message->getMessage());

        $this->assertInstanceOf(LogRequest::class, $message->getRequest());
    }

    /**
     * Test Clearing Messages
     *
     * @version 2.1
     * @since   2015-05
     *
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\Logger::clear
     */
    public function testClear()
    {
        $logger = new Logger();
        $logger->log(LogLevel::ERROR, 'test message');
        $logger->log(LogLevel::ERROR, 'test message');

        $this->assertCount(2, $logger->getMessages());
        $this->assertTrue($logger->hasMessages());

        $logger->clear();

        $this->assertCount(0, $logger->getMessages());
        $this->assertFalse($logger->hasMessages());
    }
}
