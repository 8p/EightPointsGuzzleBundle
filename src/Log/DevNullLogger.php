<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

use Psr\Log\LoggerTrait;

/**
 * @author SuRiKmAn <surikman@surikman.sk>
 */
class DevNullLogger implements LoggerInterface
{
    use LoggerTrait;

    /**
     * {@inheritdoc}
     */
    public function log($level, $message, array $context = []): void
    {
        // do nothing!!
    }

    /**
     * Clear messages list
     *
     * @return void
     */
    public function clear() : void
    {
        // do nothing!!
    }

    /**
     * Return if messages exist or not
     *
     * @return boolean
     */
    public function hasMessages() : bool
    {
        return false;
    }

    /**
     * Return log messages
     *
     * @return array
     */
    public function getMessages() : array
    {
        return [];
    }
}
