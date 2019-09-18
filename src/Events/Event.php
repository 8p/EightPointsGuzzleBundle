<?php

namespace EightPoints\Bundle\GuzzleBundle\Events;

use Symfony\Component\EventDispatcher\Event as BaseEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Contracts\EventDispatcher\Event as ContractsBaseEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @see: https://github.com/8p/EightPointsGuzzleBundle/pull/261
 * @see: https://github.com/8p/EightPointsGuzzleBundle/pull/265
 *
 * SF 4.3 introduced Contracts and deprecated interfaces used before.
 * This file is a layer to support different versions and don't cause deprecation messages.
 */
if (is_subclass_of(EventDispatcher::class, EventDispatcherInterface::class)) {
    class Event extends ContractsBaseEvent
    {
    }
} else {
    class Event extends BaseEvent
    {
    }
}
