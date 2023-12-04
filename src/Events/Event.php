<?php

namespace EightPoints\Bundle\GuzzleBundle\Events;

use Symfony\Contracts\EventDispatcher\Event as BaseEvent;

/**
 * @see: https://github.com/8p/EightPointsGuzzleBundle/pull/261
 * @see: https://github.com/8p/EightPointsGuzzleBundle/pull/265
 *
 * SF 4.3 introduced Contracts and deprecated interfaces used before.
 * This file is a layer to support different versions and don't cause deprecation messages.
 *
 * @TODO remove that file in the next major release
 */
class Event extends BaseEvent
{
}
