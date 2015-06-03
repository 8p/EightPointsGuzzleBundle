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
     * @var
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
} // end: HttpDataCollectorTest
