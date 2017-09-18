<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

/**
 * @version   2.1
 * @since     2014-11
 */
class LogMessage
{
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
     * @version 2.1
     * @since   2014-11
     *
     * @param   string $message
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Set log level
     *
     * @since  2015-05
     *
     * @param  string $value
     */
    public function setLevel($value)
    {
        $this->level = $value;
    }

    /**
     * Returning log level
     *
     * @version 2.1
     * @since   2014-11
     *
     * @return  string
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Returning log message
     *
     * @version 2.1
     * @since   2014-11
     *
     * @return  string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set Log Request
     *
     * @version 2.1
     * @since   2015-05
     *
     * @param   LogRequest $value
     */
    public function setRequest(LogRequest $value)
    {
        $this->request = $value;
    }

    /**
     * Get Log Request
     *
     * @version 2.1
     * @since   2015-05
     *
     * @return  LogRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set Log Response
     *
     * @version 2.1
     * @since   2015-05
     *
     * @param   LogResponse $value
     */
    public function setResponse(LogResponse $value)
    {
        $this->response = $value;
    }

    /**
     * Get Log Response
     *
     * @version 2.1
     * @since   2015-05
     *
     * @return  LogResponse
     */
    public function getResponse()
    {
        return $this->response;
    }
}
