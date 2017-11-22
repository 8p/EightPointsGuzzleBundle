<?php

namespace EightPoints\Bundle\GuzzleBundle\Middleware;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\MessageFormatter;
use EightPoints\Bundle\GuzzleBundle\Log\LoggerInterface;

class LogMiddleware
{
    /** @var \GuzzleHttp\MessageFormatter */
    protected $formatter;

    /** @var \EightPoints\Bundle\GuzzleBundle\Log\LoggerInterface */
    protected $logger;

    /**
     * @param \EightPoints\Bundle\GuzzleBundle\Log\LoggerInterface $logger
     * @param \GuzzleHttp\MessageFormatter $formatter
     */
    public function __construct(LoggerInterface $logger, MessageFormatter $formatter)
    {
        $this->logger    = $logger;
        $this->formatter = $formatter;
    }

    /**
     * Logging each Request
     *
     * @return \Closure
     */
    public function log() : \Closure
    {
        $logger    = $this->logger;
        $formatter = $this->formatter;

        return function (callable $handler) use ($logger, $formatter) {

            return function ($request, array $options) use ($handler, $logger, $formatter) {

                return $handler($request, $options)->then(

                    function ($response) use ($logger, $request, $formatter) {

                        $message = $formatter->format($request, $response);
                        $context = compact('request', 'response');

                        $logger->info($message, $context);

                        return $response;
                    },

                    function ($reason) use ($logger, $request, $formatter) {

                        $response = $reason instanceof RequestException ? $reason->getResponse() : null;
                        $message  = $formatter->format($request, $response, $reason);
                        $context  = compact('request', 'response');

                        $logger->notice($message, $context);

                        return \GuzzleHttp\Promise\rejection_for($reason);
                    }
                );
            };
        };
    }
}
