<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

use Psr\Log\LoggerTrait;
use Symfony\Contracts\Service\ResetInterface;

/**
 * @author SuRiKmAn <surikman@surikman.sk>
 */
class DevNullLogger implements LoggerInterface, ResetInterface
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
     * {@inheritdoc}
     */
    public function reset() : void
    {
        $this->clear();
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
