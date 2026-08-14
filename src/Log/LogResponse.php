<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

use EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundle;
use Psr\Http\Message\ResponseInterface;

class LogResponse
{
    /** @var int */
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

    public function __construct(ResponseInterface $response, bool $logBody = true)
    {
        $this->logBody = $logBody;
        $this->save($response);
    }

    /**
     * Save data
     */
    public function save(ResponseInterface $response): void
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
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Set HTTP status code
     */
    public function setStatusCode(int $value): void
    {
        $this->statusCode = $value;
    }

    /**
     * Return HTTP status phrase
     */
    public function getStatusPhrase(): string
    {
        return $this->statusPhrase;
    }

    /**
     * Set HTTP status phrase
     */
    public function setStatusPhrase(string $value): void
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
     */
    public function setBody(string $value): void
    {
        $this->body = $value;
    }

    /**
     * Return protocol version
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * Set protocol version
     */
    public function setProtocolVersion(string $value): void
    {
        $this->protocolVersion = $value;
    }

    /**
     * Return response headers
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Set response headers
     */
    public function setHeaders(array $value): void
    {
        $this->headers = $value;
    }
}
