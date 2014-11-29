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
     * @var array $messages
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

        $message = new LogMessage($level, $message, $context);

        $this->messages[] = $message;
    } // end: log

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
    } // end: getMessages
} // end: Logger
