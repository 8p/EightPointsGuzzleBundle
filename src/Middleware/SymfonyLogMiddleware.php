<?php

namespace EightPoints\Bundle\GuzzleBundle\Middleware;

use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Promise\Create;
use Psr\Log\LoggerInterface;

class SymfonyLogMiddleware
{
    protected MessageFormatter $formatter;

    protected LoggerInterface $logger;

    public function __construct(LoggerInterface $logger, MessageFormatter $formatter)
    {
        $this->logger = $logger;
        $this->formatter = $formatter;
    }

    public function __invoke(callable $handler): \Closure
    {
        $logger = $this->logger;
        $formatter = $this->formatter;

        return function ($request, array $options) use ($handler, $logger, $formatter) {
            return $handler($request, $options)->then(
                function ($response) use ($logger, $request, $formatter) {
                    $message = $formatter->format($request, $response);

                    $logger->info($message);

                    return $response;
                },
                function ($reason) use ($logger, $request, $formatter) {
                    $response = null;
                    if (\is_object($reason) && \method_exists($reason, 'getResponse')) {
                        $response = $reason->getResponse();
                    }
                    $message = $formatter->format($request, $response, $reason);

                    $logger->notice($message);

                    return Create::rejectionFor($reason);
                }
            );
        };
    }
}
