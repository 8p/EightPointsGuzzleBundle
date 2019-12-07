<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\Middleware;

use EightPoints\Bundle\GuzzleBundle\Events\PostTransactionEvent;
use EightPoints\Bundle\GuzzleBundle\Middleware\EventDispatchMiddleware;
use EightPoints\Bundle\GuzzleBundle\Events\PreTransactionEvent;
use EightPoints\Bundle\GuzzleBundle\Events\GuzzleEvents;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class EventDispatchMiddlewareTest extends TestCase
{
    /**
     * Test that listeners for 'eight_points_guzzle.pre_transaction' and
     * 'eight_points_guzzle.post_transaction' are called.
     */
    public function testDispatchEvent()
    {
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(GuzzleEvents::PRE_TRANSACTION, $this->createPreTransactionEventListener());
        $eventDispatcher->addListener(GuzzleEvents::POST_TRANSACTION, $this->createPostTransactionEventListener());

        $request = new Request('POST', 'http://api.domain.tld');
        $handler = new MockHandler([new Response(200)]);

        $promise = $this->dispatchEvents($eventDispatcher, $handler, $request);

        $this->assertInstanceOf(PromiseInterface::class, $promise);

        $promise->wait();
    }

    /**
     * Test the case when Pre Transaction listener modify request (add header)
     *
     * @see https://github.com/8p/EightPointsGuzzleBundle/pull/119
     */
    public function testCaseWhenPreTransactionListenerChangesRequest()
    {
        $preTransactionListener = static function(PreTransactionEvent $event) {
            $request = $event->getTransaction();

            $event->setTransaction($request->withHeader('some-test-header', 'some-test-value'));
        };

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(GuzzleEvents::PRE_TRANSACTION, $preTransactionListener);

        $request = new Request('POST', 'http://api.domain.tld');
        $handler = new MockHandler([new Response(200)]);

        $promise = $this->dispatchEvents($eventDispatcher, $handler, $request);

        $this->assertInstanceOf(PromiseInterface::class, $promise);

        $promise->wait();

        $lastRequest = $handler->getLastRequest();
        $this->assertInstanceOf(Request::class, $lastRequest);
        $this->assertTrue($lastRequest->hasHeader('some-test-header'));
        $this->assertEquals('some-test-value', $lastRequest->getHeaderLine('some-test-header'));
    }

    /**
     * Test the case when Post Transaction listener modify response (add header)
     *
     * @see https://github.com/8p/EightPointsGuzzleBundle/pull/132
     */
    public function testCaseWhenPostTransactionListenerChangesResponse()
    {
        $postTransactionListener = static function(PostTransactionEvent $event) {
            $response = $event->getTransaction();

            $event->setTransaction($response->withHeader('some-test-header', 'some-test-value'));
        };

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(GuzzleEvents::POST_TRANSACTION, $postTransactionListener);

        $request = new Request('POST', 'http://api.domain.tld');
        $handler = new MockHandler([new Response(200)]);

        $promise = $this->dispatchEvents($eventDispatcher, $handler, $request);

        $this->assertInstanceOf(PromiseInterface::class, $promise);

        /** @var ResponseInterface $response */
        $response = $promise->wait();

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertTrue($response->hasHeader('some-test-header'));
        $this->assertEquals('some-test-value', $response->getHeaderLine('some-test-header'));
    }

    /**
     * Post Transaction listener should be called even is request failed
     */
    public function testDispatchEventShouldCallPostTransactionListener()
    {
        $postTransactionListener = $this->createPostTransactionEventListener();

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(GuzzleEvents::POST_TRANSACTION, $postTransactionListener);

        $request = new Request('POST', 'http://api.domain.tld');
        $exception = new RequestException('message', $request);
        $handler = new MockHandler([$exception]);

        $promise = $this->dispatchEvents($eventDispatcher, $handler, $request);

        $this->assertInstanceOf(PromiseInterface::class, $promise);

        $promise->wait(false);
    }

    /**
     * Test the case when request failed and exception doesn't have response object
     *
     * @see https://github.com/8p/EightPointsGuzzleBundle/pull/154
     */
    public function testCaseWhenPostTransactionListenerReceivesNullFromException()
    {
        $postTransactionListener = $this->createPostTransactionEventListener();

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(GuzzleEvents::POST_TRANSACTION, $postTransactionListener);

        $request = new Request('POST', 'http://api.domain.tld');
        $exception = new RequestException('message', $request);
        $handler = new MockHandler([$exception]);

        $promise = $this->dispatchEvents($eventDispatcher, $handler, $request);

        $this->assertInstanceOf(PromiseInterface::class, $promise);

        $promise->wait(false);
    }

    /**
     * Test the case when request failed and exception has response object
     */
    public function testCaseWhenPostTransactionListenerReceivesResponseFromException()
    {
        /** @var Callable|MockObject $postTransactionEvent */
        $postTransactionEvent = $this->createPartialMock(\stdClass::class, ['__invoke']);
        $postTransactionEvent->expects($this->once())
            ->method('__invoke')
            ->with($this->callback(static function(PostTransactionEvent $event){
                $response = $event->getTransaction();

                return $event->getServiceName() === 'main' &&
                    is_object($response) &&
                    get_class($response) === Response::class &&
                    $response->getHeaderLine('some-test-header') === 'some-test-value';
            }));

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(GuzzleEvents::POST_TRANSACTION, $postTransactionEvent);

        $request = new Request('POST', 'http://api.domain.tld');
        $response = new Response(200, ['some-test-header' => 'some-test-value']);
        $exception = new RequestException('message', $request, $response);
        $handler = new MockHandler([$exception]);

        $promise = $this->dispatchEvents($eventDispatcher, $handler, $request);

        $this->assertInstanceOf(PromiseInterface::class, $promise);

        $promise->wait(false);
    }

    private function createPreTransactionEventListener(): callable
    {
        /** @var callable|MockObject $listener */
        $listener = $this->createPartialMock(\stdClass::class, ['__invoke']);
        $listener->expects($this->once())
            ->method('__invoke')
            ->with($this->callback(static function(PreTransactionEvent $event) {
                return $event->getServiceName() === 'main';
            }));

        return $listener;
    }

    private function createPostTransactionEventListener(): callable
    {
        /** @var callable|MockObject $listener */
        $listener = $this->createPartialMock(\stdClass::class, ['__invoke']);
        $listener->expects($this->once())
            ->method('__invoke')
            ->with($this->callback(static function(PostTransactionEvent $event) {
                return $event->getServiceName() === 'main';
            }));

        return $listener;
    }

    public function dispatchEvents(EventDispatcher $eventDispatcher, MockHandler $handler, Request $request): Promise
    {
        $eventDispatchMiddleware = new EventDispatchMiddleware($eventDispatcher, 'main');
        $eventDispatcherResult = $eventDispatchMiddleware->dispatchEvent();
        $result = $eventDispatcherResult($handler);
        /** @var Promise $promise */
        $promise = $result($request, []);

        return $promise;
    }
}
