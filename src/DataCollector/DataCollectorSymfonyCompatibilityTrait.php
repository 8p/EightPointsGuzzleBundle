<?php

namespace EightPoints\Bundle\GuzzleBundle\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

trait DataCollectorSymfonyCompatibilityTrait
{
    abstract protected function doCollect(Request $request, Response $response, ?\Throwable $exception = null);

    public function collect(Request $request, Response $response, ?\Throwable $exception = null): void
    {
        $this->doCollect($request, $response, $exception);
    }
}
