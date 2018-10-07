<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\Middleware;

use EightPoints\Bundle\GuzzleBundle\Log\Logger;
use EightPoints\Bundle\GuzzleBundle\Middleware\LogMiddleware;
use EightPoints\Bundle\GuzzleBundle\Middleware\ProfileMiddleware;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Promise\PromiseInterface;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use Psr\Log\LogLevel;
use Symfony\Component\Stopwatch\Stopwatch;

class ProfileMiddlewareTest extends TestCase
{
    public function testLog()
    {
        $stopwatch = new Stopwatch();
        $request = new Request('POST', 'http://api.domain.tld');
        $handler = new MockHandler([new Response(200)]);

        $profileMiddleware = new ProfileMiddleware($stopwatch);
        $profileCallback = $profileMiddleware->profile();

        $this->assertTrue(is_callable($profileCallback));

        $result = $profileCallback($handler);
        /** @var \GuzzleHttp\Promise\Promise $promise */
        $promise = $result($request, []);

        $this->assertInstanceOf(PromiseInterface::class, $promise);

        $promise->wait();

        $event = $stopwatch->getEvent('POST http://api.domain.tld');
        $this->assertNotNull($event);
    }

    public function testRejectFromLog()
    {
        $stopwatch = new Stopwatch();
        $request = new Request('POST', 'http://api.domain.tld');
        $exception = new RequestException('message', $request);
        $handler = new MockHandler([$exception]);

        $profileMiddleware = new ProfileMiddleware($stopwatch);
        $profileCallback = $profileMiddleware->profile();

        $this->assertTrue(is_callable($profileCallback));

        $result = $profileCallback($handler);
        /** @var \GuzzleHttp\Promise\Promise $promise */
        $promise = $result($request, []);

        $this->assertInstanceOf(PromiseInterface::class, $promise);

        $promise->wait(false);

        $event = $stopwatch->getEvent('POST http://api.domain.tld');
        $this->assertNotNull($event);
    }
}
