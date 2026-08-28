<?php

namespace EightPoints\Bundle\GuzzleBundle\Events;

use Psr\Http\Message\ResponseInterface;
use Symfony\Contracts\EventDispatcher\Event;

class PostTransactionEvent extends Event
{
    /** @var ResponseInterface|null */
    protected $response;

    /** @var string */
    protected $serviceName;

    public function __construct(?ResponseInterface $response, string $serviceName)
    {
        $this->response = $response;
        $this->serviceName = $serviceName;
    }

    /**
     * Get the transaction from the event.
     *
     * This returns the transaction we are working with.
     */
    public function getTransaction(): ?ResponseInterface
    {
        return $this->response;
    }

    /**
     * Sets the transaction inline with the event.
     */
    public function setTransaction(?ResponseInterface $response): void
    {
        $this->response = $response;
    }

    public function getServiceName(): string
    {
        return $this->serviceName;
    }
}
