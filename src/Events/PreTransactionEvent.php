<?php

namespace EightPoints\Bundle\GuzzleBundle\Events;

use Psr\Http\Message\RequestInterface;

class PreTransactionEvent extends Event
{
    /** @var RequestInterface */
    protected $requestTransaction;

    /** @var string */
    protected $serviceName;

    public function __construct(RequestInterface $requestTransaction, string $serviceName)
    {
        $this->requestTransaction = $requestTransaction;
        $this->serviceName = $serviceName;
    }

    /**
     * Access the transaction from the Guzzle HTTP request
     *
     * This returns the actual Request Object from the Guzzle HTTP Request.
     * This object will be modified by the event listener.
     */
    public function getTransaction(): RequestInterface
    {
        return $this->requestTransaction;
    }

    /**
     * Replaces the transaction with the modified one.
     *
     * Guzzles transaction returns a modified request object,
     * so once it has been modified, we need to put it back on the
     * event so it can become part of the transaction.
     */
    public function setTransaction(RequestInterface $requestTransaction): void
    {
        $this->requestTransaction = $requestTransaction;
    }

    public function getServiceName(): string
    {
        return $this->serviceName;
    }
}
