<?php

namespace EightPoints\Bundle\GuzzleBundle\Middleware;

use EightPoints\Bundle\GuzzleBundle\Events\PostTransactionEvent;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use EightPoints\Bundle\GuzzleBundle\Events\GuzzleEvents;
use EightPoints\Bundle\GuzzleBundle\Events\PreTransactionEvent;

/**
 * Dispatches an Event using the Symfony Event Dispatcher.
 * Dispatches a PRE_TRANSACTION event, before the transaction is sent
 * Dispatches a POST_TRANMSACTION event, when the remote hosts responds.
 */
class EventDispatchMiddleware
{
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    private $serviceName;

    /**
     * EventDispatchMiddleware constructor.
     *
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(EventDispatcherInterface $eventDispatcher, $serviceName)
    {
        $this->eventDispatcher = $eventDispatcher;
        $this->serviceName = $serviceName;
    }

    /**
     * @return \Closure
     */
    public function dispatchEvent()
    {
        return function (callable $handler) {

            return function (
                RequestInterface $request,
                array $options
            ) use ($handler) {
                // Create the Pre Transaction event.
                $preTransactionEvent = new PreTransactionEvent($request, $this->serviceName);

                // Dispatch it through the symfony Dispatcher.
                $this->eventDispatcher->dispatch(GuzzleEvents::PRE_TRANSACTION, $preTransactionEvent);

                // Continue the handler chain.
                $promise = $handler($preTransactionEvent->getTransaction(), $options);
                // Handle the response form teh server.
                return $promise->then(
                    function (ResponseInterface $response) {
                        // Create hte Post Transaction event.
                        $postTransactionEvent = new PostTransactionEvent($response, $this->serviceName);

                        // Dispatch the event on the symfony event dispatcher.
                        $this->eventDispatcher->dispatch(GuzzleEvents::POST_TRANSACTION, $postTransactionEvent);

                        // Continue down the chain.
                        return $postTransactionEvent->getTransaction();
                    }
                );
            };
        };
    }
}
