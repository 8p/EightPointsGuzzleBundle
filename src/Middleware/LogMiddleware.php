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
                // generate id that will be used to supplement the log with information
                $requestId = uniqid('eight_points_guzzle_');

                // initial registration of log
                $logger->info('', compact('request', 'requestId'));

                // this id will be used by RequestTimeMiddleware
                $options['request_id'] = $requestId;

                return $handler($request, $options)->then(

                    function ($response) use ($logger, $request, $formatter, $requestId) {

                        $message = $formatter->format($request, $response);
                        $context = compact('request', 'response', 'requestId');

                        $logger->info($message, $context);

                        return $response;
                    },

                    function ($reason) use ($logger, $request, $formatter, $requestId) {

                        $response = $reason instanceof RequestException ? $reason->getResponse() : null;
                        $message  = $formatter->format($request, $response, $reason);
                        $context  = compact('request', 'response', 'requestId');

                        $logger->notice($message, $context);

                        return \GuzzleHttp\Promise\rejection_for($reason);
                    }
                );
            };
        };
    }
}
