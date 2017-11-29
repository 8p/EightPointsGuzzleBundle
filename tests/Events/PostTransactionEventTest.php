<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\Events;

use EightPoints\Bundle\GuzzleBundle\Events\PostTransactionEvent;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\Response;

class PostTransactionEventTest extends TestCase
{
    /**
     * Test Instance
     *
     * @covers \EightPoints\Bundle\GuzzleBundle\Events\PostTransactionEvent::__construct
     */
    public function testConstruct()
    {
        $serviceName = 'service name';
        $response    = $this->createMock(Response::class);
        $postEvent   = new PostTransactionEvent($response, $serviceName);

        $this->assertSame($serviceName, $postEvent->getServiceName());
    }

    /**
     * Test Transaction
     *
     * @covers \EightPoints\Bundle\GuzzleBundle\Events\PostTransactionEvent::setTransaction
     * @covers \EightPoints\Bundle\GuzzleBundle\Events\PostTransactionEvent::getTransaction
     */
    public function testTransaction()
    {
        $statusCode = 204;
        $response   = $this->createMock(Response::class);
        $postEvent  = new PostTransactionEvent($response, 'main');

        $transMock = $this->getMockBuilder(Response::class)
                          ->getMock();

        $transMock->method('getStatusCode')->willReturn($statusCode);

        $postEvent->setTransaction($transMock);

        $transaction = $postEvent->getTransaction();

        $this->assertSame($transaction, $transMock);
        $this->assertSame($statusCode, $transaction->getStatusCode());
    }
}
