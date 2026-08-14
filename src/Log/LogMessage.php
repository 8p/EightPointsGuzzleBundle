<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

class LogMessage
{
    /** @var string */
    protected $message;

    /** @var string */
    protected $level;

    /** @var LogRequest */
    protected $request;

    /** @var LogResponse */
    protected $response;

    /** @var float|null */
    protected $transferTime;

    /** @var string|null */
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
     */
    public function setLevel($level): void
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
     */
    public function setRequest(LogRequest $value): void
    {
        $this->request = $value;
    }

    /**
     * Get Log Request
     *
     * @return LogRequest
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set Log Response
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
     * @return LogResponse
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
    public function setTransferTime($transferTime): void
    {
        $this->transferTime = $transferTime;
    }

    /**
     * @return string|null
     */
    public function getCurlCommand()
    {
        return $this->curlCommand;
    }

    /**
     * @param string $curlCommand
     */
    public function setCurlCommand($curlCommand): void
    {
        $this->curlCommand = $curlCommand;
    }
}
