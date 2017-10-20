<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\Events;

use EightPoints\Bundle\GuzzleBundle\Events\PreTransactionEvent;
use GuzzleHttp\Psr7\Request;
use PHPUnit\Framework\TestCase;

/**
 * @version   4.5
 * @since     2016-01
 */
class PreTransactionEventTest extends TestCase
{
    /**
     * Test Instance
     *
     * @version 4.5
     * @since   2016-01
     *
     * @covers \EightPoints\Bundle\GuzzleBundle\Events\PreTransactionEvent::__construct
     */
    public function testConstruct()
    {
        $serviceName = 'service name';
        $request     = $this->getMockBuilder(Request::class)
                            ->setConstructorArgs(['GET', '/'])
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
     * @covers \EightPoints\Bundle\GuzzleBundle\Events\PreTransactionEvent::setTransaction
     * @covers \EightPoints\Bundle\GuzzleBundle\Events\PreTransactionEvent::getTransaction
     */
    public function testTransaction()
    {
        $method   = 'POST';
        $request  = $this->getMockBuilder(Request::class)
                         ->setConstructorArgs(['GET', '/'])
                         ->getMock();

        $preEvent = new PreTransactionEvent($request, 'main');

        $transMock = $this->getMockBuilder(Request::class)
                          ->setConstructorArgs([$method, '/'])
                          ->getMock();

        $transMock->method('getMethod')->willReturn($method);

        $preEvent->setTransaction($transMock);

        $transaction = $preEvent->getTransaction();

        $this->assertSame($transaction, $transMock);
        $this->assertSame($method, $transaction->getMethod());
    }
}
