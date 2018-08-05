<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\Log;

use EightPoints\Bundle\GuzzleBundle\Log\Logger;
use EightPoints\Bundle\GuzzleBundle\Log\LoggerInterface;
use EightPoints\Bundle\GuzzleBundle\Log\LogMessage;
use EightPoints\Bundle\GuzzleBundle\Log\LogRequest;
use EightPoints\Bundle\GuzzleBundle\Log\LogResponse;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

class LoggerTest extends TestCase
{
    /**
     * Test Instance
     */
    public function testConstruct()
    {
        $this->assertInstanceOf(LoggerInterface::class, new Logger());
    }

    /**
     * Test Messages
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

        $messages = array_slice($logger->getMessages(), 2, 1);
        $message = reset($messages);
        $this->assertSame(LogLevel::INFO, $message->getLevel());
        $this->assertSame('info message', $message->getMessage());

        $this->assertInstanceOf(LogRequest::class, $message->getRequest());
    }

    /**
     * Test Clearing Messages
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

    /**
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\Logger::log
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\LogMessage::setRequest
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\LogMessage::getRequest
     */
    public function testLogWithRequest()
    {
        $request = new Request('GET', 'http://api.domain.tld');

        $logger = new Logger();
        $logger->log(LogLevel::INFO, 'message', ['request' => $request]);

        $this->assertCount(1, $logger->getMessages());

        /** @var LogMessage $message */
        $message = array_values($logger->getMessages())[0];
        $logRequest = $message->getRequest();
        $this->assertInstanceOf(LogRequest::class, $logRequest);
        $this->assertEquals('GET', $logRequest->getMethod());
        $this->assertEquals('http://api.domain.tld', $logRequest->getUrl());
    }

    /**
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\Logger::log
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\LogMessage::setResponse
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\LogMessage::getResponse
     */
    public function testLogWithResponse()
    {
        $response = new Response(
            201,
            ['some-test-header' => 'some-test-value'],
            'body'
        );

        $logger = new Logger();
        $logger->log(LogLevel::INFO, 'message', ['response' => $response]);

        $this->assertCount(1, $logger->getMessages());

        /** @var LogMessage $message */
        $message = array_values($logger->getMessages())[0];
        $logResponse = $message->getResponse();
        $this->assertInstanceOf(LogResponse::class, $logResponse);
        $this->assertEquals(201, $logResponse->getStatusCode());
        $this->assertEquals(
            ['some-test-header' => ['some-test-value']],
            $logResponse->getHeaders()
        );
        $this->assertEquals('body', $logResponse->getBody());
    }
}
