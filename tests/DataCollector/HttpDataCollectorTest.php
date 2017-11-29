<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\DataCollector;

use EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector;
use EightPoints\Bundle\GuzzleBundle\Log\Logger;
use EightPoints\Bundle\GuzzleBundle\Log\LogGroup;
use EightPoints\Bundle\GuzzleBundle\Log\LogMessage;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class HttpDataCollectorTest extends TestCase
{
    /**
     * @var \EightPoints\Bundle\GuzzleBundle\Log\Logger|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $logger;

    /**
     * SetUp: before executing each test function
     */
    public function setUp()
    {
        $this->logger = $this->getMockBuilder(Logger::class)->getMock();
    }

    /**
     * Test Constructor
     *
     * @covers \EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector::__construct
     */
    public function testConstruct()
    {
        $collector = new HttpDataCollector($this->logger);
        $data      = unserialize($collector->serialize());
        $expected  = [
            'logs'      => [],
            'callCount' => 0,
            'totalTime' => 0,
        ];

        $this->assertSame($expected, $data);
    }

    /**
     * Test Collecting Data
     *
     * @covers \EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector::collect
     * @covers \EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector::getLogs
     * @covers \EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector::getLogGroup
     */
    public function testCollect()
    {
        $this->logger->expects($this->once())
                     ->method('getMessages')
                     ->willReturn(['test message']);

        $collector = new HttpDataCollector($this->logger);
        $response  = $this->getMockBuilder(Response::class)
                          ->getMock();

        $request = $this->getMockBuilder(Request::class)
                          ->getMock();

        $request->expects($this->once())
                ->method('getUri')
                ->willReturn('someRandomUrlId');

        $request->expects($this->once())
                ->method('getPathInfo')
                ->willReturn('id');

        $collector->collect($request, $response);

        $logs = $collector->getLogs();

        /** @var LogGroup $log */
        foreach ($logs as $log) {
            $this->assertInstanceOf(LogGroup::class, $log);

            $this->assertSame(['test message'], $log->getMessages());
            $this->assertSame('id', $log->getRequestName());
        }
    }

    /**
     * Test Collector Name
     *
     * @covers \EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector::getName
     */
    public function testName()
    {
        $collector = new HttpDataCollector($this->logger);

        $this->assertSame('eight_points_guzzle', $collector->getName());
    }

    /**
     * Test Log Messages
     *
     * @covers \EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector::getMessages
     */
    public function testMessages()
    {
        $this->logger->expects($this->once())
                     ->method('getMessages')
                     ->willReturn(['test message #1', 'test message #2']);

        $collector = new HttpDataCollector($this->logger);
        $response  = $this->getMockBuilder(Response::class)
                          ->getMock();
        $request   = $this->getMockBuilder(Request::class)
                          ->getMock();

        $request->expects($this->once())
                ->method('getUri')
                ->willReturn('someRandomUrlId');

        $request->expects($this->once())
                ->method('getPathInfo')
                ->willReturn('id');

        $collector->collect($request, $response);

        $messages = $collector->getMessages();

        /** @var \EightPoints\Bundle\GuzzleBundle\Log\LogMessage $message */
        foreach ($messages as $i => $message) {

            $text = sprintf('test message #%d', ($i + 1));
            $this->assertSame($text, $message);
        }
    }

    /**
     * Test Call Count
     *
     * @covers \EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector::getCallCount
     */
    public function testCallCount()
    {
        $this->logger->expects($this->once())
                     ->method('getMessages')
                     ->willReturn(['test message #1', 'test message #2']);

        $collector = new HttpDataCollector($this->logger);
        $response  = $this->getMockBuilder(Response::class)
                          ->getMock();
        $request   = $this->getMockBuilder(Request::class)
                          ->getMock();

        $request->expects($this->once())
                ->method('getUri')
                ->willReturn('someRandomUrlId');

        $request->expects($this->once())
                ->method('getPathInfo')
                ->willReturn('id');

        $collector->collect($request, $response);

        $this->assertSame(2, $collector->getCallCount());
    }

    /**
     * Test reset method
     *
     * @covers \EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector::reset
     */
    public function testReset()
    {
        $this->logger->expects($this->once())
            ->method('getMessages')
            ->willReturn(['test message #1', 'test message #2']);

        $collector = new HttpDataCollector($this->logger);

        $response = $this->getMockBuilder(Response::class)
            ->getMock();

        $request = $this->getMockBuilder(Request::class)
            ->getMock();

        $request->expects($this->once())
            ->method('getUri')
            ->willReturn('someRandomUrlId');

        $request->expects($this->once())
            ->method('getPathInfo')
            ->willReturn('id');

        $collector->collect($request, $response);

        $collector->addTotalTime(3.14);

        $this->assertEquals(2, $collector->getCallCount());
        $this->assertEquals(3.14, $collector->getTotalTime());

        $collector->reset();

        $this->assertSame(0, $collector->getCallCount());
        $this->assertCount(0, $collector->getLogs());
        $this->assertEquals(0, $collector->getTotalTime());
    }

    /**
     * Test Error Count
     *
     * @covers \EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector::getErrorCount
     */
    public function testErrorCount()
    {
        $errorMessage = new LogMessage('error log message');
        $errorMessage->setLevel(LogLevel::ERROR);

        $infoMessage = new LogMessage('info log message');
        $infoMessage->setLevel(LogLevel::INFO);

        $this->logger->expects($this->once())
            ->method('getMessages')
            ->willReturn([$errorMessage, $infoMessage]);

        $collector = new HttpDataCollector($this->logger);

        $response = $this->getMockBuilder(Response::class)
            ->getMock();

        $request = $this->getMockBuilder(Request::class)
            ->getMock();

        $request->expects($this->once())
            ->method('getUri')
            ->willReturn('someRandomUrlId');

        $request->expects($this->once())
            ->method('getPathInfo')
            ->willReturn('id');

        $collector->collect($request, $response);

        $this->assertEquals(1, $collector->getErrorCount());
    }

    /**
     * Test Total Time
     *
     * @covers \EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector::getTotalTime
     * @covers \EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector::addTotalTime
     */
    public function testTotalTime()
    {
        $collector = new HttpDataCollector($this->logger);
        $this->assertEquals(0, $collector->getTotalTime());

        $collector->addTotalTime(3.14);
        $this->assertEquals(3.14, $collector->getTotalTime());

        $collector->addTotalTime(2.17);
        $this->assertEquals(5.31, $collector->getTotalTime());
    }

    /**
     * @covers \EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector::getIconColor
     */
    public function testGetIconColor()
    {
        $collector = new HttpDataCollector($this->logger);
        $color = $collector->getIconColor();

        $this->assertRegExp('/(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/', $color);
    }
}
