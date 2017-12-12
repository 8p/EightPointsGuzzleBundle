<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\DataCollector;

use EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector;

/**
 * @version   2.1
 * @since     2015-05
 */
class HttpDataCollectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \EightPoints\Bundle\GuzzleBundle\Log\Logger|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $logger;

    /**
     * SetUp: before executing each test function
     *
     * @version 2.1
     * @since   2015-06
     */
    public function setUp()
    {
        $this->logger = $this->getMockBuilder('EightPoints\Bundle\GuzzleBundle\Log\Logger')
                             ->getMock();
    }

    /**
     * Test Constructor
     *
     * @version 2.1
     * @since   2015-06
     *
     * @covers  EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector::__construct
     */
    public function testConstruct()
    {
        $collector = new HttpDataCollector($this->logger);
        $data      = unserialize($collector->serialize());
        $expected  = array(
            'logs'      => array(),
            'callCount' => 0,
        );

        $this->assertSame($expected, $data);
    }

    /**
     * Test Collecting Data
     *
     * @version 2.1
     * @since   2015-06
     *
     * @covers  EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector::collect
     * @covers  EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector::getLogs
     * @covers  EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector::getLogGroup
     */
    public function testCollect()
    {
        $this->logger->expects($this->once())
                     ->method('getMessages')
                     ->willReturn(array('test message'));

        $collector = new HttpDataCollector($this->logger);
        $response  = $this->getMockBuilder('Symfony\Component\HttpFoundation\Response')
                          ->getMock();
        $request   = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
                          ->getMock();

        $request->expects($this->once())
                ->method('getPathInfo')
                ->willReturn('id');

        $collector->collect($request, $response);

        $logs = $collector->getLogs();

        /** @var \EightPoints\Bundle\GuzzleBundle\Log\LogGroup $log */
        foreach ($logs as $log) {

            $this->assertInstanceOf('EightPoints\Bundle\GuzzleBundle\Log\LogGroup', $log);

            $this->assertSame(array('test message'), $log->getMessages());
            $this->assertSame('id', $log->getRequestName());
        }
    }

    /**
     * Test Collector Name
     *
     * @version 2.1
     * @since   2015-06
     *
     * @covers  EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector::getName
     */
    public function testName()
    {
        $collector = new HttpDataCollector($this->logger);

        $this->assertSame('guzzle', $collector->getName());
    }

    /**
     * Test Log Messages
     *
     * @version 2.1
     * @since   2015-06
     *
     * @covers  EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector::getMessages
     */
    public function testMessages()
    {
        $this->logger->expects($this->once())
                     ->method('getMessages')
                     ->willReturn(['test message #1', 'test message #2']);

        $collector = new HttpDataCollector($this->logger);
        $response  = $this->getMockBuilder('Symfony\Component\HttpFoundation\Response')
                          ->getMock();
        $request   = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
                          ->getMock();

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
     * @version 2.1
     * @since   2015-06
     *
     * @covers  EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector::getCallCount
     */
    public function testCallCount()
    {
        $this->logger->expects($this->once())
                     ->method('getMessages')
                     ->willReturn(['test message #1', 'test message #2']);

        $collector = new HttpDataCollector($this->logger);
        $response  = $this->getMockBuilder('Symfony\Component\HttpFoundation\Response')
                          ->getMock();
        $request   = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
                          ->getMock();

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

        $response = $this->getMockBuilder('Symfony\Component\HttpFoundation\Response')
            ->getMock();

        $request = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->getMock();

        $request->expects($this->once())
            ->method('getUri')
            ->willReturn('someRandomUrlId');

        $request->expects($this->once())
            ->method('getPathInfo')
            ->willReturn('id');

        $collector->collect($request, $response);

        $this->assertSame(2, $collector->getCallCount());

        $collector->reset();

        $this->assertSame(0, $collector->getCallCount());
        $this->assertCount(0, $collector->getLogs());
    }

    /**
     * Test Error Count
     *
     * @version 2.1
     * @since   2015-06
     *
     * @covers  EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector::getErrorCount
     */
    public function testErrorCount()
    {
        // implement me
    }

    /**
     * Test Total Time
     *
     * @version 2.1
     * @since   2015-06
     *
     * @covers  EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector::getTotalTime
     */
    public function testTotalTime()
    {
        // implement me
    }
}
