<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

use Namshi\Cuzzle\Formatter\CurlFormatter;
use Psr\Log\LoggerTrait;

class Logger implements LoggerInterface
{
    const LOG_MODE_NONE = 0;
    const LOG_MODE_REQUEST = 1;
    const LOG_MODE_REQUEST_AND_RESPONSE_HEADERS = 2;
    const LOG_MODE_REQUEST_AND_RESPONSE = 3;

    use LoggerTrait;

    /** @var \EightPoints\Bundle\GuzzleBundle\Log\LogMessage[] */
    private $messages = [];

    /** @var int */
    private $logMode;

    public function __construct(int $logMode = self::LOG_MODE_REQUEST_AND_RESPONSE)
    {
        $this->logMode = $logMode;
    }

    /**
     * Log message
     *
     * @param string $level
     * @param string $message
     * @param array  $context
     *
     * @return void
     */
    public function log($level, $message, array $context = [])
    {
        $requestId = isset($context['requestId']) ? $context['requestId'] : uniqid('eight_points_guzzle_');

        if (array_key_exists($requestId, $this->messages)) {
            $logMessage = $this->messages[$requestId];
        } else {
            $logMessage = new LogMessage($message);
        }

        $logMessage->setLevel($level);

        if (!empty($context)) {
            if (!empty($context['request']) && $this->logMode > self::LOG_MODE_NONE) {
                $logMessage->setRequest(new LogRequest($context['request']));

                if (class_exists(CurlFormatter::class)) {
                    $logMessage->setCurlCommand((new CurlFormatter())->format($context['request']));
                }
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
     *
     * @return void
     */
    public function clear() : void
    {
        $this->messages = [];
    }

    /**
     * Return if messages exist or not
     *
     * @return boolean
     */
    public function hasMessages() : bool
    {
        return $this->getMessages() ? true : false;
    }

    /**
     * Return log messages
     *
     * @return \EightPoints\Bundle\GuzzleBundle\Log\LogMessage[]
     */
    public function getMessages() : array
    {
        return $this->messages;
    }

    /**
     * @param string|null $requestId
     * @param float $transferTime
     *
     * @return void
     */
    public function addTransferTimeByRequestId(?string $requestId, float $transferTime) : void
    {
        if (array_key_exists($requestId, $this->messages)) {
            $this->messages[$requestId]->setTransferTime($transferTime);
        }
    }
}
