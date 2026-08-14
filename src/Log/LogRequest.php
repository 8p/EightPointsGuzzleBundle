<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

use Psr\Http\Message\RequestInterface;

class LogRequest
{
    /** @var string */
    protected $host;

    /** @var int|null */
    protected $port;

    /** @var string */
    protected $url;

    /** @var string */
    protected $path;

    /** @var string */
    protected $scheme;

    /** @var string[][] */
    protected $headers = [];

    /** @var string */
    protected $protocolVersion;

    /** @var string */
    protected $method;

    /** @var string|null */
    protected $body;

    public function __construct(RequestInterface $request)
    {
        $this->save($request);
    }

    /**
     * Save data
     */
    protected function save(RequestInterface $request): void
    {
        $uri = $request->getUri();

        $this->setHost($uri->getHost());
        $this->setPort($uri->getPort());
        $this->setUrl((string) $uri);
        $this->setPath($uri->getPath());
        $this->setScheme($uri->getScheme());
        $this->setHeaders($request->getHeaders());
        $this->setProtocolVersion($request->getProtocolVersion());
        $this->setMethod($request->getMethod());

        // rewind to previous position after logging request
        $readPosition = null;
        if ($request->getBody() && $request->getBody()->isSeekable()) {
            $readPosition = $request->getBody()->tell();
        }

        $this->setBody($request->getBody() ? $request->getBody()->__toString() : null);

        if ($readPosition !== null) {
            $request->getBody()->seek($readPosition);
        }
    }

    /**
     * Return host
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * Set request host
     */
    public function setHost(string $value): void
    {
        $this->host = $value;
    }

    /**
     * Return port
     */
    public function getPort(): ?int
    {
        return $this->port;
    }

    /**
     * Set port
     */
    public function setPort(?int $value): void
    {
        $this->port = $value;
    }

    /**
     * Return url
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * Set url
     */
    public function setUrl(string $value): void
    {
        $this->url = $value;
    }

    /**
     * Return path
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Set path
     */
    public function setPath(string $value): void
    {
        $this->path = $value;
    }

    /**
     * Return scheme
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * Set scheme
     */
    public function setScheme(string $value): void
    {
        $this->scheme = $value;
    }

    /**
     * Return headers
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Set headers
     */
    public function setHeaders(array $value): void
    {
        $this->headers = $value;
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
     * Return method
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Set method
     */
    public function setMethod(string $value): void
    {
        $this->method = $value;
    }

    /**
     * Return body
     */
    public function getBody(): ?string
    {
        return $this->body;
    }

    /**
     * Set body
     */
    public function setBody(?string $value): void
    {
        $this->body = $value;
    }
}
