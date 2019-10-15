<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

use Psr\Http\Message\RequestInterface;

class LogRequest
{
    /** @var string */
    protected $host;

    /** @var integer|null */
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

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     */
    public function __construct(RequestInterface $request)
    {
        $this->save($request);
    }

    /**
     * Save data
     *
     * @param \Psr\Http\Message\RequestInterface $request
     *
     * @return void
     */
    protected function save(RequestInterface $request) : void
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
     *
     * @return string
     */
    public function getHost() : string
    {
        return $this->host;
    }

    /**
     * Set request host
     *
     * @param string $value
     *
     * @return void
     */
    public function setHost(string $value) : void
    {
        $this->host = $value;
    }

    /**
     * Return port
     *
     * @return integer|null
     */
    public function getPort() : ?int
    {
        return $this->port;
    }

    /**
     * Set port
     *
     * @param integer|null $value
     *
     * @return void
     */
    public function setPort(?int $value): void
    {
        $this->port = $value;
    }

    /**
     * Return url
     *
     * @return string
     */
    public function getUrl() : string
    {
        return $this->url;
    }

    /**
     * Set url
     *
     * @param string $value
     *
     * @return void
     */
    public function setUrl(string $value) : void
    {
        $this->url = $value;
    }

    /**
     * Return path
     *
     * @return string
     */
    public function getPath() : string
    {
        return $this->path;
    }

    /**
     * Set path
     *
     * @param string $value
     *
     * @return void
     */
    public function setPath(string $value) : void
    {
        $this->path = $value;
    }

    /**
     * Return scheme
     *
     * @return string
     */
    public function getScheme() : string
    {
        return $this->scheme;
    }

    /**
     * Set scheme
     *
     * @param string $value
     *
     * @return void
     */
    public function setScheme(string $value) : void
    {
        $this->scheme = $value;
    }

    /**
     * Return headers
     *
     * @return array
     */
    public function getHeaders() : array
    {
        return $this->headers;
    }

    /**
     * Set headers
     *
     * @param array $value
     *
     * @return void
     */
    public function setHeaders(array $value) : void
    {
        $this->headers = $value;
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
     * Return method
     *
     * @return string
     */
    public function getMethod() : string
    {
        return $this->method;
    }

    /**
     * Set method
     *
     * @param string $value
     *
     * @return void
     */
    public function setMethod(string $value) : void
    {
        $this->method = $value;
    }

    /**
     * Return body
     *
     * @return string|null
     */
    public function getBody() : ?string
    {
        return $this->body;
    }

    /**
     * Set body
     *
     * @param string|null $value
     *
     * @return void
     */
    public function setBody(?string $value) : void
    {
        $this->body = $value;
    }
}
