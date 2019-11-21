<?php

namespace EightPoints\Bundle\GuzzleBundle\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;

/**
 * @TODO remove this trait with dropping of support Symfony < 4.4
 */

if (Kernel::VERSION_ID >= 40308) {
    trait DataCollectorSymfonyCompatibilityTrait
    {
        abstract protected function doCollect(Request $request, Response $response, \Throwable $exception = null);

        /**
         * @param Request $request
         * @param Response $response
         * @param \Throwable|null $exception
         */
        public function collect(Request $request, Response $response, \Throwable $exception = null)
        {
            $this->doCollect($request, $response, $exception);
        }
    }
} else {
    trait DataCollectorSymfonyCompatibilityTrait
    {
        abstract protected function doCollect(Request $request, Response $response, \Throwable $exception = null);

        /**
         * @param Request $request
         * @param Response $response
         * @param \Exception|null $exception
         */
        public function collect(Request $request, Response $response, \Exception $exception = null)
        {
            $this->doCollect($request, $response, $exception);
        }
    }
}
