<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

use Namshi\Cuzzle\Formatter\CurlFormatter;
use Psr\Log\LoggerTrait;
use Symfony\Contracts\Service\ResetInterface;

class Logger implements LoggerInterface, ResetInterface
{
    use LoggerTrait;

    public const LOG_MODE_NONE = 0;
    public const LOG_MODE_REQUEST = 1;
    public const LOG_MODE_REQUEST_AND_RESPONSE_HEADERS = 2;
    public const LOG_MODE_REQUEST_AND_RESPONSE = 3;

    /** @var LogMessage[] */
    private array $messages = [];

    private int $logMode;

    public function __construct(int $logMode = self::LOG_MODE_REQUEST_AND_RESPONSE)
    {
        $this->logMode = $logMode;
    }

    /**
     * Log message
     *
     * @param string $level
     * @param string $message
     */
    public function log($level, $message, array $context = []): void
    {
        $requestId = $context['requestId'] ?? uniqid('eight_points_guzzle_');

        if (array_key_exists($requestId, $this->messages)) {
            $logMessage = $this->messages[$requestId];
        } else {
            $logMessage = new LogMessage($message);
        }

        $logMessage->setLevel($level);

        if (!empty($context)) {
            if (!empty($context['request']) && $this->logMode > self::LOG_MODE_NONE) {
                $logMessage->setRequest(new LogRequest($context['request']));

                // namshi/cuzzle is an optional suggested package; not installed in the test environment.
                // @codeCoverageIgnoreStart
                if (class_exists(CurlFormatter::class)) {
                    $logMessage->setCurlCommand((new CurlFormatter())->format($context['request']));
                }
                // @codeCoverageIgnoreEnd
            }

            if (!empty($context['response']) && $this->logMode > self::LOG_MODE_REQUEST) {
                $logMessage->setResponse(new LogResponse(
                    $context['response'],
                    $this->logMode > self::LOG_MODE_REQUEST_AND_RESPONSE_HEADERS
                ));
            }
        }

        $this->messages[$requestId] = $logMessage;
    }

    /**
     * Clear messages list
     */
    public function clear(): void
    {
        $this->messages = [];
    }

    /**
     * {@inheritdoc}
     *
     * Clears buffered messages so state does not leak between requests
     * in long-running workers (e.g. FrankenPHP worker mode).
     */
    public function reset(): void
    {
        $this->clear();
    }

    /**
     * Return if messages exist or not
     */
    public function hasMessages(): bool
    {
        return (bool) $this->getMessages();
    }

    /**
     * Return log messages
     *
     * @return LogMessage[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    public function addTransferTimeByRequestId(?string $requestId, float $transferTime): void
    {
        if (array_key_exists($requestId, $this->messages)) {
            $this->messages[$requestId]->setTransferTime($transferTime);
        }
    }
}
