<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\Middleware;

use GuzzleHttp\Promise\PromiseInterface;

class FlexiblePromise implements PromiseInterface
{
    /** @var callable|null */
    public $onFulfilled;

    /** @var callable|null */
    public $onRejected;

    public function then(
        callable $onFulfilled = null,
        callable $onRejected = null
    ){
        $this->onFulfilled = $onFulfilled;
        $this->onRejected = $onRejected;
    }

    public function otherwise(callable $onRejected) {}

    public function getState(){}

    public function resolve($value) {}

    public function reject($reason) {}

    public function cancel(){}

    public function wait($unwrap = true) {}
}
