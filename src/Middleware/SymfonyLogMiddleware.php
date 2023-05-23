<?php

namespace EightPoints\Bundle\GuzzleBundle\Middleware;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\MessageFormatter;
use Psr\Log\LoggerInterface;

class SymfonyLogMiddleware
{
    /** @var \GuzzleHttp\MessageFormatter */
    protected $formatter;

    /** @var \Psr\Log\LoggerInterface */
    protected $logger;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param \GuzzleHttp\MessageFormatter $formatter
     */
    public function __construct(LoggerInterface $logger, MessageFormatter $formatter)
    {
        $this->logger    = $logger;
        $this->formatter = $formatter;
    }

    /**
     * @param callable $handler
     *
     * @return \Closure
     */
    public function __invoke(callable $handler) : \Closure
    {
        $logger    = $this->logger;
        $formatter = $this->formatter;

        return function ($request, array $options) use ($handler, $logger, $formatter) {

            return $handler($request, $options)->then(

                function ($response) use ($logger, $request, $formatter) {
                    $message = $formatter->format($request, $response);

                    $logger->info($message);

                    return $response;
                },

                function ($reason) use ($logger, $request, $formatter) {
                    $response = $reason instanceof RequestException ? $reason->getResponse() : null;
                    $message  = $formatter->format($request, $response, $reason);

                    $logger->notice($message);

                    return \GuzzleHttp\Promise\Create::rejectionFor($reason);
                }
            );
        };
    }
}
