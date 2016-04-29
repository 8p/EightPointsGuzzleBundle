<?php

namespace EightPoints\Bundle\GuzzleBundle\Events;

use Psr\Http\Message\ResponseInterface;
use Symfony\Component\EventDispatcher\Event;

class PostTransactionEvent extends Event
{
    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var string
     */
    protected $serviceName;

    /**
     * PostTransactionEvent constructor.
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param string                              $serviceName
     */
    public function __construct(ResponseInterface $response, $serviceName)
    {
        $this->response = $response;
        $this->serviceName = $serviceName;
    }


    /**
     * Get the transaction from the event.
     *
     * This returns the transaction we are working with.
     *
     * @return ResponseInterface
     */
    public function getTransaction()
    {
        return $this->response;
    }

    /**
     * Sets the transaction inline with the event.
     *
     * @param ResponseInterface $response
     */
    public function setTransaction(ResponseInterface $response)
    {
        $this->response = $response;
    }

    /**
     * @return string
     */
    public function getServiceName()
    {
        return $this->serviceName;
    }
}
