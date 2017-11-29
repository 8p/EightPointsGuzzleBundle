<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\Middleware;

use EightPoints\Bundle\GuzzleBundle\Log\Logger;
use EightPoints\Bundle\GuzzleBundle\Middleware\LogMiddleware;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Promise\PromiseInterface;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use Psr\Log\LogLevel;

class LogMiddlewareTest extends TestCase
{
    public function testLog()
    {
        $logger = new Logger();
        $request = new Request('POST', 'http://api.domain.tld');
        $handler = new MockHandler([new Response(200)]);

        $logMiddleware = new LogMiddleware($logger, new MessageFormatter());
        $logCallback = $logMiddleware->log();

        $this->assertTrue(is_callable($logCallback));

        $result = $logCallback($handler);
        /** @var \GuzzleHttp\Promise\Promise $promise */
        $promise = $result($request, []);

        $this->assertInstanceOf(PromiseInterface::class, $promise);

        $promise->wait();

        $messages = $logger->getMessages();
        $this->assertCount(1, $messages);
        $this->assertEquals(LogLevel::INFO, $messages[0]->getLevel());
    }

    public function testRejectFromLog()
    {
        $logger = new Logger();
        $request = new Request('POST', 'http://api.domain.tld');
        $exception = new RequestException('message', $request);
        $handler = new MockHandler([$exception]);

        $logMiddleware = new LogMiddleware($logger, new MessageFormatter());
        $logCallback = $logMiddleware->log();

        $this->assertTrue(is_callable($logCallback));

        $result = $logCallback($handler);
        /** @var \GuzzleHttp\Promise\Promise $promise */
        $promise = $result($request, []);

        $this->assertInstanceOf(PromiseInterface::class, $promise);

        $promise->wait(false);

        $messages = $logger->getMessages();
        $this->assertCount(1, $messages);
        $this->assertEquals(LogLevel::NOTICE, $messages[0]->getLevel());
    }
}
