<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

/**
 * LogMessage
 *
 * @package   EightPoints\Bundle\GuzzleBundle\Log
 * @author    Florian Preusner
 *
 * @version   2.1
 * @since     2014-11
 */
class LogMessage {

    /**
     * @var string $level
     */
    private $level;

    /**
     * @var string $message
     */
    private $message;

    /**
     * @var array $context
     */
    private $context = array();

    /**
     * Constructor
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2014-11
     *
     * @param   string $level
     * @param   string $message
     * @param   array  $context
     */
    public function __construct($level, $message, $context = array()) {

        $this->level   = $level;
        $this->message = $message;
        $this->context = $context;
    } // end: __construct

    /**
     * Returning log level
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2014-11
     *
     * @return  string
     */
    public function getLevel() {

        return $this->level;
    } // end: getLevel

    /**
     * Returning log message
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2014-11
     *
     * @return  string
     */
    public function getMessage() {

        return $this->message;
    } // end: getMessage

    /**
     * Returning context array
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2014-11
     *
     * @return  array
     */
    public function getContext() {

        return $this->context;
    } // end: getContext
} // end: LogMessage
