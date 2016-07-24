<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\Events;

use EightPoints\Bundle\GuzzleBundle\Events\PostTransactionEvent;

/**
 * @version   4.5
 * @since     2016-01
 */
class PostTransactionEventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test Instance
     *
     * @version 4.5
     * @since   2016-01
     *
     * @covers  EightPoints\Bundle\GuzzleBundle\Events\PostTransactionEvent::__construct
     */
    public function testConstruct()
    {
        $serviceName = 'service name';
        $response    = $this->createMock('GuzzleHttp\Psr7\Response');
        $postEvent   = new PostTransactionEvent($response, $serviceName);

        $this->assertSame($serviceName, $postEvent->getServiceName());
    }

    /**
     * Test Transaction
     *
     * @version 4.5
     * @since   2016-01
     *
     * @covers  EightPoints\Bundle\GuzzleBundle\Events\PostTransactionEvent::setTransaction
     * @covers  EightPoints\Bundle\GuzzleBundle\Events\PostTransactionEvent::getTransaction
     */
    public function testTranscation()
    {
        $statusCode = 204;
        $response   = $this->createMock('GuzzleHttp\Psr7\Response');
        $postEvent  = new PostTransactionEvent($response, null);

        $transMock = $this->getMockBuilder('GuzzleHttp\Psr7\Response')
                          ->getMock();

        $transMock->method('getStatusCode')->willReturn($statusCode);

        $postEvent->setTransaction($transMock);

        $transaction = $postEvent->getTransaction();

        $this->assertSame($transaction, $transMock);
        $this->assertSame($statusCode, $transaction->getStatusCode());
    }
}
