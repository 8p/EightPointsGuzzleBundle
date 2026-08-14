<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

use Psr\Log\LoggerInterface as PsrLoggerInterface;

interface LoggerInterface extends PsrLoggerInterface
{
    /**
     * Clear messages list
     */
    public function clear(): void;

    /**
     * Return if messages exist or not
     */
    public function hasMessages(): bool;

    /**
     * Return log messages
     */
    public function getMessages(): array;
}
