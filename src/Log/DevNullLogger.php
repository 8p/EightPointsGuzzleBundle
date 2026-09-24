<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

use Psr\Log\LoggerTrait;
use Symfony\Contracts\Service\ResetInterface;

class DevNullLogger implements LoggerInterface, ResetInterface
{
    use LoggerTrait;

    /**
     * @param string $level
     * @param string $message
     * @param mixed[] $context
     */
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
     *
     * @return LogMessage[]
     */
    public function getMessages(): array
    {
        return [];
    }
}
