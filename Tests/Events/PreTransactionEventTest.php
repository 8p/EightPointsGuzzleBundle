<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\Events;

use EightPoints\Bundle\GuzzleBundle\Events\PreTransactionEvent;

/**
 * @version   4.5
 * @since     2016-01
 */
class PreTransactionEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test Instance
     *
     * @version 4.5
     * @since   2016-01
     *
     * @covers  EightPoints\Bundle\GuzzleBundle\Events\PreTransactionEvent::__construct
     */
    public function testConstruct()
    {
        $serviceName = 'service name';
        $request     = $this->getMockBuilder('GuzzleHttp\Psr7\Request')
                            ->setConstructorArgs(array('GET', '/'))
                            ->getMock();

        $preEvent = new PreTransactionEvent($request, $serviceName);

        $this->assertSame($serviceName, $preEvent->getServiceName());
    }

    /**
     * Test Transaction
     *
     * @version 4.5
     * @since   2016-01
     *
     * @covers  EightPoints\Bundle\GuzzleBundle\Events\PreTransactionEvent::setTransaction
     * @covers  EightPoints\Bundle\GuzzleBundle\Events\PreTransactionEvent::getTransaction
     */
    public function testTranscation()
    {
        $method   = 'POST';
        $request  = $this->getMockBuilder('GuzzleHttp\Psr7\Request')
                         ->setConstructorArgs(array('GET', '/'))
                         ->getMock();

        $preEvent = new PreTransactionEvent($request, null);

        $transMock = $this->getMockBuilder('GuzzleHttp\Psr7\Request')
                          ->setConstructorArgs(array($method, '/'))
                          ->getMock();

        $transMock->method('getMethod')->willReturn($method);

        $preEvent->setTransaction($transMock);

        $transaction = $preEvent->getTransaction();

        $this->assertSame($transaction, $transMock);
        $this->assertSame($method, $transaction->getMethod());
    }
}
