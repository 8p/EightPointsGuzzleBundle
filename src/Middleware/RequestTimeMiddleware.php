<?php

namespace EightPoints\Bundle\GuzzleBundle\Middleware;

use EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector;
use Psr\Http\Message\RequestInterface;
use GuzzleHttp\TransferStats;

class RequestTimeMiddleware
{
    /** @var HttpDataCollector */
    private $dataCollector;

    /**
     * @param HttpDataCollector $dataCollector
     */
    public function __construct(HttpDataCollector $dataCollector)
    {
        $this->dataCollector = $dataCollector;
    }

    /**
     * @param callable $handler
     *
     * @return \Closure
     */
    public function __invoke(callable $handler) : \Closure
    {
        return function (
            RequestInterface $request,
            array $options
        ) use ($handler) {
            $options['on_stats'] = $this->getOnStatsCallback($options['on_stats']);

            // Continue the handler chain.
            return $handler($request, $options);
        };
    }

    /**
     * Create callback for on_stats options.
     * If request has on_stats option, it will be called inside of this callback.
     *
     * @param null|\Closure $initialOnStats
     *
     * @return \Closure
     */
    protected function getOnStatsCallback(\Closure $initialOnStats = null) : \Closure
    {
        return  function (TransferStats $stats) use ($initialOnStats) {
            if (is_callable($initialOnStats)) {
                call_user_func($initialOnStats);
            }

            $this->dataCollector->addTotalTime((float)$stats->getTransferTime());
        };
    }
}
