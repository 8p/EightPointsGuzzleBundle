<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\Middleware;

use EightPoints\Bundle\GuzzleBundle\Middleware\EventDispatchMiddleware;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\RejectedPromise;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class EventDispatchMiddlewareTest extends \PHPUnit_Framework_TestCase
{
    /** @var EventDispatcher */
    private $eventDispatcher;

    /** @var callable */
    private $handler;

    /** @var RequestInterface */
    private $request;

    /** @var FlexiblePromise */
    private $promise;

    /** @var ResponseInterface */
    private $response;

    /** @var RequestException */
    private $requestException;

    public function setUp()
    {
        $this->eventDispatcher = $this->getMockBuilder(EventDispatcher::class)->getMock();
        $this->request = $this->getMockBuilder(RequestInterface::class)->getMock();
        $this->response = $this->getMockBuilder(ResponseInterface::class)->getMock();
        $this->requestException = $this->getMockBuilder(RequestException::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->requestException->method('getResponse')->willReturn($this->response);

        $this->promise = new FlexiblePromise();

        $this->handler = $this->getMockBuilder(\StdClass::class)->setMethods(['__invoke'])->getMock();
        $this->handler->method('__invoke')->willReturn($this->promise);
    }

    public function testDispatchEvent()
    {
        $eventDispatchMiddleware = new EventDispatchMiddleware($this->eventDispatcher, 'main');
        $eventDispatcherResult = $eventDispatchMiddleware->dispatchEvent();
        $result = $eventDispatcherResult($this->handler);
        $result($this->request, []);

        $this->assertTrue(is_callable($this->promise->onFulfilled));
        $this->assertTrue(is_callable($this->promise->onRejected));

        $promiseResponse = call_user_func($this->promise->onFulfilled, $this->response);
        $this->assertInstanceOf(ResponseInterface::class, $promiseResponse);

        $rejectionPromise = call_user_func($this->promise->onRejected, $this->requestException);
        $this->assertInstanceOf(RejectedPromise::class, $rejectionPromise);
    }
}
