<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

class LogMessage
{
    /** @var string */
    protected $message;

    /** @var string */
    protected $level;

    protected ?LogRequest $request = null;

    protected ?LogResponse $response = null;

    protected ?float $transferTime = null;

    protected ?string $curlCommand = null;

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

    public function setRequest(LogRequest $value): void
    {
        $this->request = $value;
    }

    public function getRequest(): ?LogRequest
    {
        return $this->request;
    }

    public function setResponse(LogResponse $value): void
    {
        $this->response = $value;
    }

    public function getResponse(): ?LogResponse
    {
        return $this->response;
    }

    public function getTransferTime(): ?float
    {
        return $this->transferTime;
    }

    public function setTransferTime(?float $transferTime): void
    {
        $this->transferTime = $transferTime;
    }

    public function getCurlCommand(): ?string
    {
        return $this->curlCommand;
    }

    public function setCurlCommand(string $curlCommand): void
    {
        $this->curlCommand = $curlCommand;
    }
}
