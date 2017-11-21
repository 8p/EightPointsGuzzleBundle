<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

/**
 * @version   2.1
 * @since     2014-11
 */
class LogMessage
{
    /** @var string */
    protected $message;

    /** @var string */
    protected $level;

    /** @var \EightPoints\Bundle\GuzzleBundle\Log\LogRequest */
    protected $request;

    /** @var \EightPoints\Bundle\GuzzleBundle\Log\LogResponse */
    protected $response;

    /**
     * @version 2.1
     * @since   2014-11
     *
     * @param string $message
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Set log level
     *
     * @since 2015-05
     *
     * @param string $level
     *
     * @return void
     */
    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * Returning log level
     *
     * @version 2.1
     * @since   2014-11
     *
     * @return string
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
     * @return string
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
     * @param \EightPoints\Bundle\GuzzleBundle\Log\LogRequest $value
     *
     * @return void
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
     * @return \EightPoints\Bundle\GuzzleBundle\Log\LogRequest
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
     * @param \EightPoints\Bundle\GuzzleBundle\Log\LogResponse $value
     *
     * @return void
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
     * @return \EightPoints\Bundle\GuzzleBundle\Log\LogResponse
     */
    public function getResponse()
    {
        return $this->response;
    }
}
