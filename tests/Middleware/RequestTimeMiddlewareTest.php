<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\Middleware;

use EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector;
use EightPoints\Bundle\GuzzleBundle\Log\Logger;
use EightPoints\Bundle\GuzzleBundle\Middleware\RequestTimeMiddleware;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\TransferStats;
use PHPUnit\Framework\TestCase;

class RequestTimeMiddlewareTest extends TestCase
{
    /** @var \EightPoints\Bundle\GuzzleBundle\Log\LoggerInterface|\PHPUnit\Framework\MockObject\MockObject */
    protected $logger;

    public function setUp()
    {
        $this->logger = $this->getMockBuilder(Logger::class)
            ->getMock();
    }

    public function testInvoke()
    {
        $httpDataCollector = new HttpDataCollector([$this->logger], 0);

        $request = new Request('GET', 'http://test.com');
        $handler = new MockHandler([new Response(200)]);

        $requestTimeMiddleware = new RequestTimeMiddleware($this->logger, $httpDataCollector);
        $invokeResult = $requestTimeMiddleware($handler);
        $invokeResult($request, ['request_id' => uniqid()]);

        $lastOptions = $handler->getLastOptions();
        $this->assertArrayHasKey('on_stats', $lastOptions);
        $this->assertNotNull($lastOptions['request_id']);
        $this->assertInstanceOf(\Closure::class, $lastOptions['on_stats']);

        $transferStats = new TransferStats($request, null, 3.14);
        call_user_func($lastOptions['on_stats'], $transferStats);

        $this->assertEquals(3.14, $httpDataCollector->getTotalTime());
    }

    public function testInvokeWithInitialOnStats()
    {
        $httpDataCollector = new HttpDataCollector([$this->logger], 0);

        $request = new Request('GET', 'http://test.com');
        $handler = new MockHandler([new Response(200)]);

        $onStatsCallable = $this->createPartialMock(\stdClass::class, ['__invoke']);
        $onStatsCallable->expects(self::once())
            ->method('__invoke');

        $requestTimeMiddleware = new RequestTimeMiddleware($this->logger, $httpDataCollector);
        $invokeResult = $requestTimeMiddleware($handler);
        $invokeResult($request, ['on_stats' => $onStatsCallable]);

        $lastOptions = $handler->getLastOptions();
        $this->assertArrayHasKey('on_stats', $lastOptions);
        $this->assertInstanceOf(\Closure::class, $lastOptions['on_stats']);

        $transferStats = new TransferStats($request, null, 3.14);
        call_user_func($lastOptions['on_stats'], $transferStats);

        $this->assertEquals(3.14, $httpDataCollector->getTotalTime());
    }
}
