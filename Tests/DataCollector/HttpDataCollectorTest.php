<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\DataCollector;

use       EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector;

/**
 * Class HttpDataCollectorTest
 *
 * @package   EightPoints\Bundle\GuzzleBundle\Tests\DataCollector
 * @author    Florian Preusner
 *
 * @version   2.1
 * @since     2015-05
 */
class HttpDataCollectorTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var \EightPoints\Bundle\GuzzleBundle\Log\Logger
     */
    protected $logger;

    /**
     * SetUp: before executing each test function
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-06
     */
    public function setUp() {

        $this->logger = $this->getMockBuilder('EightPoints\Bundle\GuzzleBundle\Log\Logger')
                             ->getMock();
    } // end: setUp()

    /**
     * Test Constructor
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-06
     *
     * @covers  EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector::__construct
     */
    public function testConstruct() {

        $collector = new HttpDataCollector($this->logger);
        $data      = unserialize($collector->serialize());
        $expected  = array(
            'logs'      => array(),
            'callCount' => 0,
        );

        $this->assertSame($expected, $data);
    } // end: testConstruct()

    /**
     * Test Collecting Data
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-06
     *
     * @covers  EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector::collect
     * @covers  EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector::getLogs
     * @covers  EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector::getLogGroup
     */
    public function testCollect() {

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
        foreach($logs as $log) {

            $this->assertInstanceOf('EightPoints\Bundle\GuzzleBundle\Log\LogGroup', $log);

            $this->assertSame(array('test message'), $log->getMessages());
            $this->assertSame('id', $log->getRequestName());
        }
    } // end: testCollect()

    /**
     * Test Collector Name
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-06
     *
     * @covers  EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector::getName
     */
    public function testName() {

        $collector = new HttpDataCollector($this->logger);

        $this->assertSame('guzzle', $collector->getName());
    } // end: testName()

    /**
     * Test Log Messages
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-06
     *
     * @covers  EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector::getMessages
     */
    public function testMessages() {

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
        foreach($messages as $i => $message) {

            $text = sprintf('test message #%d', ($i + 1));
            $this->assertSame($text, $message);
        }
    } // end: testMessages()

    /**
     * Test Call Count
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-06
     *
     * @covers  EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector::getCallCount
     */
    public function testCallCount() {

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
    } // end: testCallCount()

    /**
     * Test Error Count
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-06
     *
     * @covers  EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector::getErrorCount
     */
    public function testErrorCount() {

        // implement me
    } // end: testErrorCount()

    /**
     * Test Total Time
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-06
     *
     * @covers  EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector::getTotalTime
     */
    public function testTotalTime() {

        // implement me
    } // end: testTotalTime()
} // end: HttpDataCollectorTest
