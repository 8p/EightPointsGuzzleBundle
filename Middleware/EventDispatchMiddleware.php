<?php
/**
 * Created by IntelliJ IDEA.
 * User: chris
 * Date: 9/16/15
 * Time: 11:19 AM
 */

namespace EightPoints\Bundle\GuzzleBundle\Middleware;


use EightPoints\Bundle\GuzzleBundle\Events\PostTransactionEvent;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use EightPoints\Bundle\GuzzleBundle\Events\GuzzleEvents;
use EightPoints\Bundle\GuzzleBundle\Events\PreTransactionEvent;

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

    public function dispatchEvent()
    {
        return function (callable $handler) {
            return function (
                RequestInterface $request,
                array $options
            ) use ( $handler ) {
                $preTransactionEvent = new PreTransactionEvent($request, $this->serviceName);

                $this->eventDispatcher->dispatch(GuzzleEvents::PRE_TRANSACTION, $preTransactionEvent);
                $promise = $handler($request, $options);
                return $promise->then(
                    function (ResponseInterface $response) {
                        $postTransactionEvent = new PostTransactionEvent($response, $this->serviceName);
                        $this->eventDispatcher->dispatch(GuzzleEvents::POST_TRANSACTION, $postTransactionEvent);
                        return $response;
                    }
                );
            };
        };
    }
}
