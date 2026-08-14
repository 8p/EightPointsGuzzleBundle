<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\Events;

use EightPoints\Bundle\GuzzleBundle\Events\PostTransactionEvent;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\TestCase;

class PostTransactionEventTest extends TestCase
{
    /**
     * @covers \EightPoints\Bundle\GuzzleBundle\Events\PostTransactionEvent::__construct
     */
    public function testConstruct(): void
    {
        $serviceName = 'service name';
        $response = $this->createMock(Response::class);
        $postEvent = new PostTransactionEvent($response, $serviceName);

        $this->assertSame($serviceName, $postEvent->getServiceName());
    }

    /**
     * @covers \EightPoints\Bundle\GuzzleBundle\Events\PostTransactionEvent::setTransaction
     * @covers \EightPoints\Bundle\GuzzleBundle\Events\PostTransactionEvent::getTransaction
     */
    public function testTransaction(): void
    {
        $statusCode = 204;
        $response = $this->createMock(Response::class);
        $postEvent = new PostTransactionEvent($response, 'main');

        $transMock = $this->getMockBuilder(Response::class)->getMock();
        $transMock->method('getStatusCode')->willReturn($statusCode);

        $postEvent->setTransaction($transMock);

        $transaction = $postEvent->getTransaction();

        $this->assertSame($transaction, $transMock);
        $this->assertSame($statusCode, $transaction->getStatusCode());
    }
}
