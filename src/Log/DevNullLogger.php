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

    public function log($level, $message, array $context = []): void
    {
        // do nothing!!
    }

    /**
     * Clear messages list
     */
    public function clear(): void
    {
        // do nothing!!
    }

    public function reset(): void
    {
        $this->clear();
    }

    /**
     * Return if messages exist or not
     */
    public function hasMessages(): bool
    {
        return false;
    }

    /**
     * Return log messages
     */
    public function getMessages(): array
    {
        return [];
    }
}
