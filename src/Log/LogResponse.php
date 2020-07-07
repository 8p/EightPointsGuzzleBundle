<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

use EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundle;
use Psr\Http\Message\ResponseInterface;

class LogResponse
{
    /** @var integer */
    protected $statusCode;

    /** @var string */
    protected $statusPhrase;

    /** @var string */
    protected $body;

    /** @var string[][] */
    protected $headers = [];

    /** @var string */
    protected $protocolVersion;

    /** @var bool */
    private $logBody;

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     * @param bool $logBody
     */
    public function __construct(ResponseInterface $response, bool $logBody = true)
    {
        $this->logBody = $logBody;
        $this->save($response);
    }

    /**
     * Save data
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return void
     */
    public function save(ResponseInterface $response) : void
    {
        $this->setStatusCode($response->getStatusCode());
        $this->setStatusPhrase($response->getReasonPhrase());

        $this->setHeaders($response->getHeaders());
        $this->setProtocolVersion($response->getProtocolVersion());

        if ($this->logBody) {
            $this->setBody($response->getBody()->getContents());

            // rewind to previous position after reading response body
            if ($response->getBody()->isSeekable()) {
                $response->getBody()->rewind();
            }
        } else {
            $this->setBody(EightPointsGuzzleBundle::class . ': [response body log disabled]');
        }
    }

    /**
     * Return HTTP status code
     *
     * @return integer
     */
    public function getStatusCode() : int
    {
        return $this->statusCode;
    }

    /**
     * Set HTTP status code
     *
     * @param integer $value
     *
     * @return void
     */
    public function setStatusCode(int $value) : void
    {
        $this->statusCode = $value;
    }

    /**
     * Return HTTP status phrase
     *
     * @return string
     */
    public function getStatusPhrase() : string
    {
        return $this->statusPhrase;
    }

    /**
     * Set HTTP status phrase
     *
     * @param string $value
     *
     * @return void
     */
    public function setStatusPhrase(string $value) : void
    {
        $this->statusPhrase = $value;
    }

    /**
     * Return response body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set response body
     *
     * @param string $value
     *
     * @return void
     */
    public function setBody(string $value) : void
    {
        $this->body = $value;
    }

    /**
     * Return protocol version
     *
     * @return string
     */
    public function getProtocolVersion() : string
    {
        return $this->protocolVersion;
    }

    /**
     * Set protocol version
     *
     * @param string $value
     *
     * @return void
     */
    public function setProtocolVersion(string $value) : void
    {
        $this->protocolVersion = $value;
    }

    /**
     * Return response headers
     *
     * @return array
     */
    public function getHeaders() : array
    {
        return $this->headers;
    }

    /**
     * Set response headers
     *
     * @param array $value
     *
     * @return void
     */
    public function setHeaders(array $value) : void
    {
        $this->headers = $value;
    }
}
