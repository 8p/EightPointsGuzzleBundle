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
    const SERVICE_NAME = 'main';

    /**
     * Test that listeners for 'eight_points_guzzle.pre_transaction' and
     * 'eight_points_guzzle.post_transaction' are called.
     */
    public function testDispatchEvent()
    {
        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(GuzzleEvents::PRE_TRANSACTION, $this->createPreTransactionEventListener());
        $eventDispatcher->addListener(GuzzleEvents::preTransactionFor(self::SERVICE_NAME), $this->createPreTransactionEventListener());
        $eventDispatcher->addListener(GuzzleEvents::POST_TRANSACTION, $this->createPostTransactionEventListener());

        $request = new Request('POST', 'http://api.domain.tld');
        $handler = new MockHandler([new Response(200)]);

        $promise = $this->dispatchEvents($eventDispatcher, $handler, $request);

        $this->assertInstanceOf(PromiseInterface::class, $promise);

        $promise->wait();
    }

    /**
     * Test the case where a client specific listener is configured and an event is dispatched for another client
     */
    public function testCaseWhenClientSpecificPreTransactionListenerIsNotPassedEventsForOtherClients()
    {
        /** @var callable|MockObject $nonCalledListener */
        $nonCalledListener = $this->createPartialMock(\stdClass::class, ['__invoke']);
        $nonCalledListener->expects($this->never())->method('__invoke');

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(GuzzleEvents::preTransactionFor(self::SERVICE_NAME), $this->createPreTransactionEventListener());
        $eventDispatcher->addListener(GuzzleEvents::preTransactionFor('other-service-name'), $nonCalledListener);

        $request = new Request('POST', 'http://api.domain.tld');
        $handler = new MockHandler([new Response(200)]);

        $promise = $this->dispatchEvents($eventDispatcher, $handler, $request);

        $this->assertInstanceOf(PromiseInterface::class, $promise);

        $promise->wait();
    }

    /**
     * Test the case where a client specific listener is configured and an event is dispatched for another client
     */
    public function testCaseWhenClientSpecificPostTransactionListenerIsNotPassedEventsForOtherClients()
    {
        /** @var callable|MockObject $nonCalledListener */
        $nonCalledListener = $this->createPartialMock(\stdClass::class, ['__invoke']);
        $nonCalledListener->expects($this->never())->method('__invoke');

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(GuzzleEvents::postTransactionFor(self::SERVICE_NAME), $this->createPostTransactionEventListener());
        $eventDispatcher->addListener(GuzzleEvents::postTransactionFor('other-service-name'), $nonCalledListener);

        $request = new Request('POST', 'http://api.domain.tld');
        $handler = new MockHandler([new Response(200)]);

        $promise = $this->dispatchEvents($eventDispatcher, $handler, $request);

        $this->assertInstanceOf(PromiseInterface::class, $promise);

        $promise->wait();
    }

    /**
     * Test the case when generic Pre Transaction listener modify request (add header)
     *
     * @see https://github.com/8p/EightPointsGuzzleBundle/pull/119
     */
    public function testCaseWhenGenericPreTransactionListenerChangesRequest()
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
     * Test the case when client specific Pre Transaction listener modify request (add header)
     *
     * @see https://github.com/8p/EightPointsGuzzleBundle/pull/119
     */
    public function testCaseWhenClientSpecificPreTransactionListenerChangesRequest()
    {
        $preTransactionListener = static function(PreTransactionEvent $event) {
            $request = $event->getTransaction();

            $event->setTransaction($request->withHeader('some-test-header', 'some-test-value'));
        };

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(GuzzleEvents::preTransactionFor(self::SERVICE_NAME), $preTransactionListener);

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
     * Test the case when both client specific and generic Pre Transaction listener modify request (add header)
     *
     * @see https://github.com/8p/EightPointsGuzzleBundle/pull/119
     */
    public function testCaseWhenBothGenericAndClientSpecificPreTransactionListenerChangesRequest()
    {
        $genericPreTransactionListener = static function(PreTransactionEvent $event) {
            $request = $event->getTransaction();

            $event->setTransaction($request->withHeader('some-test-header', 'some-generic-value')->withHeader('some-generic-header', 'some-generic-value'));
        };

        $clientSpecificPreTransactionListener = static function(PreTransactionEvent $event) {
            $request = $event->getTransaction();

            $event->setTransaction($request->withHeader('some-test-header', 'some-client-specific-value')->withHeader('some-client-specific-header', 'some-client-specific-value'));
        };

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(GuzzleEvents::PRE_TRANSACTION, $genericPreTransactionListener);
        $eventDispatcher->addListener(GuzzleEvents::preTransactionFor(self::SERVICE_NAME), $clientSpecificPreTransactionListener);

        $request = new Request('POST', 'http://api.domain.tld');
        $handler = new MockHandler([new Response(200)]);

        $promise = $this->dispatchEvents($eventDispatcher, $handler, $request);

        $this->assertInstanceOf(PromiseInterface::class, $promise);

        $promise->wait();

        $lastRequest = $handler->getLastRequest();
        $this->assertInstanceOf(Request::class, $lastRequest);

        // generic headers are added to the request
        $this->assertTrue($lastRequest->hasHeader('some-generic-header'));
        $this->assertEquals('some-generic-value', $lastRequest->getHeaderLine('some-generic-header'));

        // client specific headers are added to the request
        $this->assertTrue($lastRequest->hasHeader('some-client-specific-header'));
        $this->assertEquals('some-client-specific-value', $lastRequest->getHeaderLine('some-client-specific-header'));

        // client specific headers override generic headers
        $this->assertTrue($lastRequest->hasHeader('some-test-header'));
        $this->assertEquals('some-client-specific-value', $lastRequest->getHeaderLine('some-test-header'));
    }

    /**
     * Test the case when generic Post Transaction listener modify response (add header)
     *
     * @see https://github.com/8p/EightPointsGuzzleBundle/pull/132
     */
    public function testCaseWhenGenericPostTransactionListenerChangesResponse()
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
     * Test the case when client specific Post Transaction listener modify response (add header)
     *
     * @see https://github.com/8p/EightPointsGuzzleBundle/pull/132
     */
    public function testCaseWhenClientSpecificPostTransactionListenerChangesResponse()
    {
        $postTransactionListener = static function(PostTransactionEvent $event) {
            $response = $event->getTransaction();

            $event->setTransaction($response->withHeader('some-test-header', 'some-test-value'));
        };

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(GuzzleEvents::postTransactionFor(self::SERVICE_NAME), $postTransactionListener);

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
     * Test the case when both generic and client specific Post Transaction listener modify response (add header)
     *
     * @see https://github.com/8p/EightPointsGuzzleBundle/pull/132
     */
    public function testCaseWhenBothGenericAndClientSpecificPostTransactionListenerChangeResponse()
    {
        $genericPostTransactionListener = static function(PostTransactionEvent $event) {
            $response = $event->getTransaction();

            $event->setTransaction($response->withHeader('some-test-header', 'some-generic-value')->withHeader('some-generic-header', 'some-generic-value'));
        };

        $clientSpecificPostTransactionListener = static function(PostTransactionEvent $event) {
            $response = $event->getTransaction();

            $event->setTransaction($response->withHeader('some-test-header', 'some-client-specific-value')->withHeader('some-client-specific-header', 'some-client-specific-value'));
        };

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(GuzzleEvents::POST_TRANSACTION, $genericPostTransactionListener);
        $eventDispatcher->addListener(GuzzleEvents::postTransactionFor(self::SERVICE_NAME), $clientSpecificPostTransactionListener);

        $request = new Request('POST', 'http://api.domain.tld');
        $handler = new MockHandler([new Response(200)]);

        $promise = $this->dispatchEvents($eventDispatcher, $handler, $request);

        $this->assertInstanceOf(PromiseInterface::class, $promise);

        /** @var ResponseInterface $response */
        $response = $promise->wait();

        $this->assertInstanceOf(ResponseInterface::class, $response);

        // generic headers are added to the response
        $this->assertTrue($response->hasHeader('some-generic-header'));
        $this->assertEquals('some-generic-value', $response->getHeaderLine('some-generic-header'));

        // client specific headers are added to the response
        $this->assertTrue($response->hasHeader('some-client-specific-header'));
        $this->assertEquals('some-client-specific-value', $response->getHeaderLine('some-client-specific-header'));

        // client specific headers override generic headers
        $this->assertTrue($response->hasHeader('some-test-header'));
        $this->assertEquals('some-client-specific-value', $response->getHeaderLine('some-test-header'));
    }

    /**
     * Post Transaction listener should be called even is request failed
     */
    public function testDispatchEventsShouldCallPostTransactionListener()
    {
        $genericPostTransactionListener = $this->createPostTransactionEventListener();
        $clientSpecificPostTransactionListener = $this->createPostTransactionEventListener();

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(GuzzleEvents::POST_TRANSACTION, $genericPostTransactionListener);
        $eventDispatcher->addListener(GuzzleEvents::postTransactionFor(self::SERVICE_NAME), $clientSpecificPostTransactionListener);

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
        $genericPostTransactionListener = $this->createPostTransactionEventListener();
        $clientSpecificTransactionListener = $this->createPostTransactionEventListener();

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(GuzzleEvents::POST_TRANSACTION, $genericPostTransactionListener);
        $eventDispatcher->addListener(GuzzleEvents::postTransactionFor(self::SERVICE_NAME), $clientSpecificTransactionListener);

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
        $callback = $this->callback(static function (PostTransactionEvent $event) {
            $response = $event->getTransaction();

            return $event->getServiceName() === 'main' &&
                is_object($response) &&
                get_class($response) === Response::class &&
                $response->getHeaderLine('some-test-header') === 'some-test-value';
        });

        /** @var Callable|MockObject $genericPostTransactionListener */
        $genericPostTransactionListener = $this->createPartialMock(\stdClass::class, ['__invoke']);
        $genericPostTransactionListener->expects($this->once())
            ->method('__invoke')
            ->with($callback);

        /** @var Callable|MockObject $genericPostTransactionListener */
        $clientSpecificPostTransactionListener = $this->createPartialMock(\stdClass::class, ['__invoke']);
        $clientSpecificPostTransactionListener->expects($this->once())
            ->method('__invoke')
            ->with($callback);

        $eventDispatcher = new EventDispatcher();
        $eventDispatcher->addListener(GuzzleEvents::POST_TRANSACTION, $genericPostTransactionListener);
        $eventDispatcher->addListener(GuzzleEvents::postTransactionFor(self::SERVICE_NAME), $clientSpecificPostTransactionListener);

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
                return $event->getServiceName() === self::SERVICE_NAME;
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
                return $event->getServiceName() === self::SERVICE_NAME;
            }));

        return $listener;
    }

    public function dispatchEvents(EventDispatcher $eventDispatcher, MockHandler $handler, Request $request): Promise
    {
        $eventDispatchMiddleware = new EventDispatchMiddleware($eventDispatcher, self::SERVICE_NAME);
        $eventDispatcherResult = $eventDispatchMiddleware->dispatchEvent();
        $result = $eventDispatcherResult($handler);
        /** @var Promise $promise */
        $promise = $result($request, []);

        return $promise;
    }
}
