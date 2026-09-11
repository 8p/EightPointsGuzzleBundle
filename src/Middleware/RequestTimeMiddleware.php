<?php

namespace EightPoints\Bundle\GuzzleBundle\Middleware;

use EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector;
use EightPoints\Bundle\GuzzleBundle\Log\Logger;
use EightPoints\Bundle\GuzzleBundle\Log\LoggerInterface;
use GuzzleHttp\TransferStats;
use Psr\Http\Message\RequestInterface;

class RequestTimeMiddleware
{
    protected LoggerInterface $logger;

    private HttpDataCollector $dataCollector;

    public function __construct(LoggerInterface $logger, HttpDataCollector $dataCollector)
    {
        $this->logger = $logger;
        $this->dataCollector = $dataCollector;
    }

    public function __invoke(callable $handler): \Closure
    {
        return function (RequestInterface $request, array $options) use ($handler) {
            $options['on_stats'] = $this->getOnStatsCallback(
                $options['on_stats'] ?? null,
                $options['request_id'] ?? null
            );

            // Continue the handler chain.
            return $handler($request, $options);
        };
    }

    /**
     * Create callback for on_stats options.
     * If request has on_stats option, it will be called inside of this callback.
     */
    protected function getOnStatsCallback(?callable $initialOnStats, ?string $requestId): \Closure
    {
        return function (TransferStats $stats) use ($initialOnStats, $requestId) {
            if (is_callable($initialOnStats)) {
                call_user_func($initialOnStats, $stats);
            }

            $this->dataCollector->addTotalTime((float) $stats->getTransferTime());

            if (($this->logger instanceof Logger) && $requestId) {
                $this->logger->addTransferTimeByRequestId($requestId, (float) $stats->getTransferTime());
            }
        };
    }
}
