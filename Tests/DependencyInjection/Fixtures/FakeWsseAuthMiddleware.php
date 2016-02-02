<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\DependencyInjection\Fixtures;


/**
 * Class FakeWsseAuthMiddleware
 *
 * @package EightPoints\Bundle\GuzzleBundle\Tests\DependencyInjection\Fixtures
 * @author Sebastian Blum
 *
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