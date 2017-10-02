<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\DependencyInjection\Fixtures;

/**
 * @since 2016-01
 */
class FakeWsseAuthMiddleware
{
    public function attach()
    {
        return function (callable $handler) {

        };
    }
}
