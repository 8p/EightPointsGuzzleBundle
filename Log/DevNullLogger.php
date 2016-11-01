<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

use Psr\Log\LoggerTrait;
use Psr\Log\LoggerInterface;

/**
 * @version   2.1
 * @since     2016-11
 */
class DevNullLogger implements LoggerInterface
{
    use LoggerTrait;

    /**
     * @inheritDoc
     */
    public function log($level, $message, array $context = [])
    {
        // do nothing!!
    }
}
