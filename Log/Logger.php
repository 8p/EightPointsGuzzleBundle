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

		$request  = new LogRequest($context['request']);
		$response = new LogResponse($context['response']);

        $message = new LogMessage($message);
		$message->setLevel($level);
		$message->setRequest($request);
		$message->setResponse($response);

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

		return ($this->getMessages());
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
