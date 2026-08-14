<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\Middleware;

use EightPoints\Bundle\GuzzleBundle\Middleware\ProfileMiddleware;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Stopwatch\Stopwatch;

class ProfileMiddlewareTest extends TestCase
{
    public function testLog(): void
    {
        $stopwatch = new Stopwatch();
        $request = new Request('POST', 'http://api.domain.tld');
        $handler = new MockHandler([new Response(200)]);

        $profileMiddleware = new ProfileMiddleware($stopwatch);
        $profileCallback = $profileMiddleware->profile();

        $this->assertTrue(is_callable($profileCallback));

        $result = $profileCallback($handler);
        /** @var Promise $promise */
        $promise = $result($request, []);

        $this->assertInstanceOf(PromiseInterface::class, $promise);

        $promise->wait();

        $event = $stopwatch->getEvent('POST http://api.domain.tld');
        $this->assertNotNull($event);
    }

    public function testRejectFromLog(): void
    {
        $stopwatch = new Stopwatch();
        $request = new Request('POST', 'http://api.domain.tld');
        $exception = new RequestException('message', $request);
        $handler = new MockHandler([$exception]);

        $profileMiddleware = new ProfileMiddleware($stopwatch);
        $profileCallback = $profileMiddleware->profile();

        $this->assertTrue(is_callable($profileCallback));

        $result = $profileCallback($handler);
        /** @var Promise $promise */
        $promise = $result($request, []);

        $this->assertInstanceOf(PromiseInterface::class, $promise);

        $promise->wait(false);

        $event = $stopwatch->getEvent('POST http://api.domain.tld');
        $this->assertNotNull($event);
    }
}
