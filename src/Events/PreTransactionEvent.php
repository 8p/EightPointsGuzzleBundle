<?php

namespace EightPoints\Bundle\GuzzleBundle\Events;

use Psr\Http\Message\RequestInterface;
use Symfony\Component\EventDispatcher\Event;

class PreTransactionEvent extends Event
{
    /**
     * @var RequestInterface
     */
    protected $requestTransaction;

    /**
     * @var string
     */
    protected $serviceName;

    /**
     * PreTransactionEvent constructor.
     *
     * @param \Psr\Http\Message\RequestInterface $requestTransaction
     * @param string                             $serviceName
     */
    public function __construct(RequestInterface $requestTransaction, $serviceName)
    {
        $this->requestTransaction = $requestTransaction;
        $this->serviceName = $serviceName;
    }

    /**
     * Access the transaction from the Guzzle HTTP request
     *
     * This returns the actual Request Object from the Guzzle HTTP Request.
     * This object will be modified by the event listener.
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    public function getTransaction()
    {
        return $this->requestTransaction;
    }

    /**
     * Replaces the transaction with the modified one.
     *
     * Guzzles transaction returns a modified request object,
     * so once it has been modified, we need to put it back on the
     * event so it can become part of the transaction.
     *
     * @param \Psr\Http\Message\RequestInterface $requestTransaction
     */
    public function setTransaction(RequestInterface $requestTransaction)
    {
        $this->requestTransaction = $requestTransaction;
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }
}
