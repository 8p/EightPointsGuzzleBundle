<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\Middleware;

use EightPoints\Bundle\GuzzleBundle\Middleware\EventDispatchMiddleware;
use EightPoints\Bundle\GuzzleBundle\Events\PreTransactionEvent;
use EightPoints\Bundle\GuzzleBundle\Events\GuzzleEvents;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

class EventDispatchMiddlewareTest extends TestCase
{
    /**
     * Test that listeners for 'eight_points_guzzle.pre_transaction' and
     * 'eight_points_guzzle.post_transaction' are called.
     */
    public function testDispatchEvent()
    {
        /** @var Callable|\PHPUnit_Framework_MockObject_MockObject $preTransactionEvent */
        $preTransactionEvent = $this->createPartialMock(\stdClass::class, ['__invoke']);
        $preTransactionEvent->expects($this->once())
            ->method('__invoke');

        /** @var Callable|\PHPUnit_Framework_MockObject_MockObject $postTransactionEvent */
        $postTransactionEvent = $this->createPartialMock(\stdClass::class, ['__invoke']);
        $postTransactionEvent->expects($this->once())
            ->method('__invoke');

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(GuzzleEvents::PRE_TRANSACTION, $preTransactionEvent);
        $eventDispatcher->addListener(GuzzleEvents::POST_TRANSACTION, $postTransactionEvent);

        $request = new Request('POST', 'http://api.domain.tld');
        $handler = new MockHandler([new Response(200)]);

        $eventDispatchMiddleware = new EventDispatchMiddleware($eventDispatcher, 'main');
        $eventDispatcherResult = $eventDispatchMiddleware->dispatchEvent();
        $result = $eventDispatcherResult($handler);
        /** @var \GuzzleHttp\Promise\Promise $promise */
        $promise = $result($request, []);

        $this->assertInstanceOf(PromiseInterface::class, $promise);

        $promise->wait();
    }
}
