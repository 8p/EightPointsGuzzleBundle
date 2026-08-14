<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\Log;

use EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundle;
use EightPoints\Bundle\GuzzleBundle\Log\Logger;
use EightPoints\Bundle\GuzzleBundle\Log\LoggerInterface;
use EightPoints\Bundle\GuzzleBundle\Log\LogMessage;
use EightPoints\Bundle\GuzzleBundle\Log\LogRequest;
use EightPoints\Bundle\GuzzleBundle\Log\LogResponse;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LogLevel;
use Symfony\Contracts\Service\ResetInterface;

class LoggerTest extends TestCase
{
    public function testConstruct(): void
    {
        $logger = new Logger();
        $this->assertInstanceOf(LoggerInterface::class, $logger);
        $this->assertInstanceOf(ResetInterface::class, $logger);
    }

    /**
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\Logger::hasMessages
     */
    public function testHasMessages(): void
    {
        $logger = new Logger();
        $this->assertFalse($logger->hasMessages());

        $logger->log(LogLevel::ERROR, 'test message');
        $this->assertTrue($logger->hasMessages());
    }

    /**
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\Logger::getMessages
     */
    public function testGetMessages(): void
    {
        $logger = new Logger();
        $this->assertCount(0, $logger->getMessages());

        $logger->log(LogLevel::ERROR, 'test message');
        $this->assertCount(1, $logger->getMessages());

        $logger->log(LogLevel::ERROR, 'second test message');
        $this->assertCount(2, $logger->getMessages());

        $messages = $logger->getMessages();

        foreach ($messages as $message) {
            $this->assertInstanceOf(LogMessage::class, $message);
            $this->assertSame(LogLevel::ERROR, $message->getLevel());
            $this->assertStringContainsString('test message', $message->getMessage());
            $this->assertNull($message->getRequest());
            $this->assertNull($message->getResponse());
        }

        $requestMock = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $uriMock = $this->getMockBuilder(Uri::class)
            ->disableOriginalConstructor()
            ->getMock();

        $uriMock->method('getHost')->willReturn('example.com');
        $uriMock->method('getPath')->willReturn('/');
        $uriMock->method('getScheme')->willReturn('https');

        $bodyMock = $this->getMockBuilder(StreamInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $requestMock->method('getHeaders')->willReturn([]);
        $requestMock->method('getUri')->willReturn($uriMock);
        $requestMock->method('getBody')->willReturn($bodyMock);
        $requestMock->method('getProtocolVersion')->willReturn('1.1');
        $requestMock->method('getMethod')->willReturn('GET');

        $logger->log(LogLevel::INFO, 'info message', ['request' => $requestMock]);

        $messages = array_slice($logger->getMessages(), 2, 1);
        $message = reset($messages);
        $this->assertSame(LogLevel::INFO, $message->getLevel());
        $this->assertSame('info message', $message->getMessage());

        $this->assertInstanceOf(LogRequest::class, $message->getRequest());
    }

    /**
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\Logger::clear
     */
    public function testClear(): void
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
     * Test Reset (Symfony ResetInterface / FrankenPHP worker)
     *
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\Logger::reset
     */
    public function testReset(): void
    {
        $logger = new Logger();
        $logger->log(LogLevel::ERROR, 'test message');
        $this->assertTrue($logger->hasMessages());

        $logger->reset();
        $this->assertFalse($logger->hasMessages());
    }

    public function getLoggerRequestModes(): array
    {
        return [
            [Logger::LOG_MODE_NONE, false],
            [Logger::LOG_MODE_REQUEST, true],
            [Logger::LOG_MODE_REQUEST_AND_RESPONSE, true],
            [Logger::LOG_MODE_REQUEST_AND_RESPONSE_HEADERS, true],
        ];
    }

    /**
     * @dataProvider getLoggerRequestModes
     *
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\Logger::log
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\LogMessage::setRequest
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\LogMessage::getRequest
     */
    public function testLogWithRequest(int $logMode, bool $hasRequest): void
    {
        $request = new Request('GET', 'http://api.domain.tld');

        $logger = new Logger($logMode);
        $logger->log(LogLevel::INFO, 'message', ['request' => $request]);

        $this->assertCount(1, $logger->getMessages());

        /** @var LogMessage $message */
        $message = array_values($logger->getMessages())[0];
        $logRequest = $message->getRequest();
        if ($hasRequest) {
            $this->assertInstanceOf(LogRequest::class, $logRequest);
            $this->assertEquals('GET', $logRequest->getMethod());
            $this->assertEquals('http://api.domain.tld', $logRequest->getUrl());
        } else {
            $this->assertNull($logRequest);
        }
    }

    public function getLoggerResponseModes(): array
    {
        return [
            [Logger::LOG_MODE_NONE, false, false],
            [Logger::LOG_MODE_REQUEST, false, false],
            [Logger::LOG_MODE_REQUEST_AND_RESPONSE, true, true],
            [Logger::LOG_MODE_REQUEST_AND_RESPONSE_HEADERS, true, false],
        ];
    }

    /**
     * @dataProvider getLoggerResponseModes
     *
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\Logger::log
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\LogMessage::setResponse
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\LogMessage::getResponse
     */
    public function testLogWithResponse(int $logMode, bool $hasResponseHeaders, bool $hasResponseBody): void
    {
        $response = new Response(
            201,
            ['some-test-header' => 'some-test-value'],
            'body'
        );

        $logger = new Logger($logMode);
        $logger->log(LogLevel::INFO, 'message', ['response' => $response]);

        $this->assertCount(1, $logger->getMessages());

        /** @var LogMessage $message */
        $message = array_values($logger->getMessages())[0];
        $logResponse = $message->getResponse();

        if (!$hasResponseHeaders && !$hasResponseBody) {
            $this->assertNull($logResponse);

            return;
        }

        $this->assertInstanceOf(LogResponse::class, $logResponse);

        if ($hasResponseHeaders) {
            $this->assertEquals(201, $logResponse->getStatusCode());
            $this->assertEquals(
                ['some-test-header' => ['some-test-value']],
                $logResponse->getHeaders()
            );
        }

        if ($hasResponseBody) {
            $this->assertEquals('body', $logResponse->getBody());
        } else {
            $this->assertEquals(EightPointsGuzzleBundle::class . ': [response body log disabled]', $logResponse->getBody());
        }
    }

    /**
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\Logger::addTransferTimeByRequestId
     */
    public function testTransferTimeByRequestId(): void
    {
        $logger = new Logger();
        $logger->log(LogLevel::INFO, 'message');

        $requestId = array_keys($logger->getMessages())[0];
        $logger->addTransferTimeByRequestId($requestId, time());
        $message = $logger->getMessages()[$requestId];

        $this->assertNotNull($message->getTransferTime());
    }
}
