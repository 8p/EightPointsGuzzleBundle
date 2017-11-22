<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

use Psr\Log\LoggerTrait;

class Logger implements LoggerInterface
{
    use LoggerTrait;

    /** @var \EightPoints\Bundle\GuzzleBundle\Log\LogMessage[] */
    private $messages = [];

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
        $logMessage = new LogMessage($message);
        $logMessage->setLevel($level);

        if (!empty($context)) {
            if (!empty($context['request'])) {
                $logMessage->setRequest(new LogRequest($context['request']));
            }

            if (!empty($context['response'])) {
                $logMessage->setResponse(new LogResponse($context['response']));
            }
        }

        $this->messages[] = $logMessage;
    }

    /**
     * Clear messages list
     *
     * @return void
     */
    public function clear()
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
}
