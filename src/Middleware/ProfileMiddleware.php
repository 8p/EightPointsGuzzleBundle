<?php

namespace EightPoints\Bundle\GuzzleBundle\Middleware;

use Symfony\Component\Stopwatch\Stopwatch;

class ProfileMiddleware
{
    /**
     * @var \Symfony\Component\Stopwatch\Stopwatch
     */
    private $stopwatch;

    /**
     * @param \Symfony\Component\Stopwatch\Stopwatch $stopwatch
     */
    public function __construct(Stopwatch $stopwatch)
    {
        $this->stopwatch = $stopwatch;
    }

    /**
     * Profiling each Request
     *
     * @return \Closure
     */
    public function profile() : \Closure
    {
        $stopwatch = $this->stopwatch;

        return function (callable $handler) use ($stopwatch) {

            return function ($request, array $options) use ($handler, $stopwatch) {
                $event = $stopwatch->start(
                    sprintf('%s %s', $request->getMethod(), $request->getUri()),
                    'eight_points_guzzle'
                );

                return $handler($request, $options)->then(

                    function ($response) use ($event) {
                        $event->stop();

                        return $response;
                    },

                    function ($reason) use ($event) {
                        $event->stop();

                        return \GuzzleHttp\Promise\rejection_for($reason);
                    }
                );
            };
        };
    }
}
