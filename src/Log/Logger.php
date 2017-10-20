<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

use Psr\Log\LoggerTrait;

/**
 * @version   2.1
 * @since     2014-11
 */
class Logger implements LoggerInterface
{
    use LoggerTrait;

    /** @var array */
    private $messages = [];

    /**
     * Log message
     *
     * @version 2.1
     * @since   2014-11
     *
     * @param   string $level
     * @param   string $message
     * @param   array  $context
     *
     * @return  void
     */
    public function log($level, $message, array $context = [])
    {
        $logMessage = new LogMessage($message);
        $logMessage->setLevel($level);

        if ($context) {
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
     * @version 2.1
     * @since   2015-05
     */
    public function clear()
    {
        $this->messages = [];
    }

    /**
     * Return if messages exist or not
     *
     * @version 2.1
     * @since   2015-05
     *
     * @return  boolean
     */
    public function hasMessages() : bool
    {
        return $this->getMessages() ? true : false;
    }

    /**
     * Return log messages
     *
     * @version 2.1
     * @since   2014-11
     *
     * @return  array
     */
    public function getMessages() : array
    {
        return $this->messages;
    }
}
