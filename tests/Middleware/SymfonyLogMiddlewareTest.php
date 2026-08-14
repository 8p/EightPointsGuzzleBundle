<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\Middleware;

use EightPoints\Bundle\GuzzleBundle\Middleware\SymfonyLogMiddleware;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

class SymfonyLogMiddlewareTest extends TestCase
{
    public function testLog(): void
    {
        $logger = new Logger();
        $request = new Request('POST', 'http://api.domain.tld');
        $handler = new MockHandler([new Response(200)]);

        $logMiddleware = new SymfonyLogMiddleware($logger, new MessageFormatter());

        $this->assertTrue(is_callable($logMiddleware));

        $result = $logMiddleware($handler);
        /** @var Promise $promise */
        $promise = $result($request, []);

        $this->assertInstanceOf(PromiseInterface::class, $promise);

        $promise->wait();

        $messages = $logger->getLogs(LogLevel::INFO);
        $this->assertCount(1, $messages);
    }

    public function testRejectFromLog(): void
    {
        $logger = new Logger();
        $request = new Request('POST', 'http://api.domain.tld');
        $exception = new RequestException('message', $request);
        $handler = new MockHandler([$exception]);

        $logMiddleware = new SymfonyLogMiddleware($logger, new MessageFormatter());

        $this->assertTrue(is_callable($logMiddleware));

        $result = $logMiddleware($handler);
        /** @var Promise $promise */
        $promise = $result($request, []);

        $this->assertInstanceOf(PromiseInterface::class, $promise);

        $promise->wait(false);

        $messages = $logger->getLogs(LogLevel::NOTICE);
        $this->assertCount(1, $messages);
    }
}
