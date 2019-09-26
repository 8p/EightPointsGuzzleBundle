<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

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

    /** @var null|float */
    protected $transferTime;

    /** @var null|string */
    protected $curlCommand;

    /**
     * @param string $message
     */
    public function __construct($message)
    {
        $this->message = $message;
    }

    /**
     * Set log level
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
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Returning log message
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
     * @return \EightPoints\Bundle\GuzzleBundle\Log\LogRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set Log Response
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
     * @return \EightPoints\Bundle\GuzzleBundle\Log\LogResponse
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return float|null
     */
    public function getTransferTime()
    {
        return $this->transferTime;
    }

    /**
     * @param float|null $transferTime
     */
    public function setTransferTime($transferTime)
    {
        $this->transferTime = $transferTime;
    }

    /**
     * @return null|string
     */
    public function getCurlCommand()
    {
        return $this->curlCommand;
    }

    /**
     * @param string $curlCommand
     */
    public function setCurlCommand($curlCommand)
    {
        $this->curlCommand = $curlCommand;
    }
}
