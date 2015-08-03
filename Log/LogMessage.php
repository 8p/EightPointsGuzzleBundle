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
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $level;

    /**
     * @var LogRequest
     */
    protected $request;

    /**
     * @var LogResponse
     */
    protected $response;

    /**
     * Constructor
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2014-11
     *
     * @param   string $message
     */
    public function __construct($message) {

        $this->message = $message;
    } // end: __construct()

    /**
     * Set log level
     *
     * @author Florian Preusner
     * @since  2015-05
     *
     * @param  string $value
     */
    public function setLevel($value) {

        $this->level = $value;
    } // end: setLevel()

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
    } // end: getLevel()

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
    } // end: getMessage()

    /**
     * Set Log Request
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @param   LogRequest $value
     */
    public function setRequest(LogRequest $value) {

        $this->request = $value;
    } // end: setRequest()

    /**
     * Get Log Request
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @return  LogRequest
     */
    public function getRequest() {

        return $this->request;
    } // end: getRequest()

    /**
     * Set Log Response
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @param   LogResponse $value
     */
    public function setResponse(LogResponse $value) {

        $this->response = $value;
    } // end: setResponse()

    /**
     * Get Log Response
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @return  LogResponse
     */
    public function getResponse() {

        return $this->response;
    } // end: getResponse()
} // end: LogMessage
