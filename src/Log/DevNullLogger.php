<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

use Psr\Log\LoggerTrait;

/**
 * @author    SuRiKmAn <surikman@surikman.sk>
 * @version   5.0
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

    /**
     * Clear messages list
     *
     * @return void
     */
    public function clear()
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
