<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

use       Psr\Log\LoggerTrait,
          Psr\Log\LoggerInterface;

/**
 * Logger
 *
 * @package   EightPoints\Bundle\GuzzleBundle\Log
 * @author    Florian Preusner
 *
 * @version   2.1
 * @since     2014-11
 */
class Logger implements LoggerInterface {

    use LoggerTrait;

    /**
     * @var array
     */
    private $messages = array();

    /**
     * Log message
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2014-11
     *
     * @param   string $level
     * @param   string $message
     * @param   array  $context
     *
     * @return  void
     */
    public function log($level, $message, array $context = array()) {

        $message = new LogMessage($message);
        $message->setLevel($level);

        if($context) {

            if(isset($context['request'])) {

                $message->setRequest(new LogRequest($context['request']));
            }

            if(isset($context['response'])) {

                $message->setResponse(new LogResponse($context['response']));
            }
        }

        $this->messages[] = $message;
    } // end: log()

    /**
     * Clear messages list
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     */
    public function clear() {

        $this->messages = array();
    } // end: clear()

    /**
     * Return if messages exist or not
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @return  boolean
     */
    public function hasMessages() {

        return $this->getMessages() ? true : false;
    } // end: hasMessages()

    /**
     * Return log messages
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2014-11
     *
     * @return  array
     */
    public function getMessages() {

        return $this->messages;
    } // end: getMessages()
} // end: Logger
