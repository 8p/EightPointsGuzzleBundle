<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\Middleware;

use EightPoints\Bundle\GuzzleBundle\Middleware\LogMiddleware;
use EightPoints\Bundle\GuzzleBundle\Log\LoggerInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Promise\RejectedPromise;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class LogMiddlewareTest extends TestCase
{
    /** @var \EightPoints\Bundle\GuzzleBundle\Log\LoggerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $logger;

    /** @var \GuzzleHttp\MessageFormatter|\PHPUnit_Framework_MockObject_MockObject */
    protected $formatter;

    /** @var callable|\PHPUnit_Framework_MockObject_MockObject */
    protected $handler;

    /** @var \EightPoints\Bundle\GuzzleBundle\Tests\Middleware\FlexiblePromise */
    protected $promise;

    /** @var \Psr\Http\Message\RequestInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $request;

    /** @var \Psr\Http\Message\ResponseInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $response;

    /** @var \GuzzleHttp\Exception\RequestException|\PHPUnit_Framework_MockObject_MockObject */
    protected $requestException;

    public function setUp()
    {
        $this->logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $this->formatter = $this->getMockBuilder(MessageFormatter::class)->getMock();
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

    public function testLog()
    {
        $logMiddleware = new LogMiddleware($this->logger, $this->formatter);
        $result = $logMiddleware->log();
        $result = $result($this->handler);
        $result($this->request, []);

        $this->assertTrue(is_callable($this->promise->onFulfilled));
        $this->assertTrue(is_callable($this->promise->onRejected));

        $promiseResponse = call_user_func($this->promise->onFulfilled, $this->response);
        $this->assertInstanceOf(ResponseInterface::class, $promiseResponse);

        $rejectionPromise = call_user_func($this->promise->onRejected, $this->requestException);
        $this->assertInstanceOf(RejectedPromise::class, $rejectionPromise);
    }
}
