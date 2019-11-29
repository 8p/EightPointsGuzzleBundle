<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\Middleware;

use Psr\Log\AbstractLogger;

class Logger extends AbstractLogger
{
    /** @var array */
    protected $logs = [];

    /**
     * @param mixed $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = []): void
    {
        $this->logs[$level][] = $message;
    }

    /**
     * @param bool $level
     *
     * @return array
     */
    public function getLogs($level = false): array
    {
        return false === $level ? $this->logs : $this->logs[$level];
    }
}
