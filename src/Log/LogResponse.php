<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

use Psr\Http\Message\ResponseInterface;

class LogResponse
{
    /** @var integer */
    protected $statusCode;

    /** @var string */
    protected $statusPhrase;

    /** @var string */
    protected $body;

    /** @var array */
    protected $headers = [];

    /** @var string */
    protected $protocolVersion;

    /**
     * @param \Psr\Http\Message\ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->save($response);
    }

    /**
     * Save data
     *
     * @param \Psr\Http\Message\ResponseInterface $response
     *
     * @return void
     */
    public function save(ResponseInterface $response)
    {
        $this->setStatusCode($response->getStatusCode());
        $this->setStatusPhrase($response->getReasonPhrase());
        $this->setBody($response->getBody()->getContents());

        // rewind to previous position after reading response body
        if ($response->getBody()->isSeekable()) {
            $response->getBody()->rewind();
        }

        $this->setHeaders($response->getHeaders());
        $this->setProtocolVersion($response->getProtocolVersion());
    }

    /**
     * Return HTTP status code
     *
     * @return integer
     */
    public function getStatusCode()
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
    public function setStatusCode($value)
    {
        $this->statusCode = $value;
    }

    /**
     * Return HTTP status phrase
     *
     * @return string
     */
    public function getStatusPhrase()
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
    public function setStatusPhrase($value)
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
    public function setBody($value)
    {
        $this->body = $value;
    }

    /**
     * Return protocol version
     *
     * @return string
     */
    public function getProtocolVersion()
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
    public function setProtocolVersion($value)
    {
        $this->protocolVersion = $value;
    }

    /**
     * Return response headers
     *
     * @return array
     */
    public function getHeaders()
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
    public function setHeaders(array $value)
    {
        $this->headers = $value;
    }
}
