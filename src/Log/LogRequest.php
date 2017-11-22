<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

use Psr\Http\Message\RequestInterface;

class LogRequest
{
    /** @var string */
    protected $host;

    /** @var integer */
    protected $port;

    /** @var string */
    protected $url;

    /** @var string */
    protected $path;

    /** @var string */
    protected $scheme;

    /** @var array */
    protected $headers = [];

    /** @var string */
    protected $protocolVersion;

    /** @var string */
    protected $method;

    /** @var string */
    protected $body;

    /** @var string */
    protected $resource;

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
    protected function save(RequestInterface $request)
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
    public function getHost()
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
    public function setHost($value)
    {
        $this->host = $value;
    }

    /**
     * Return port
     *
     * @return integer
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Set port
     *
     * @param integer $value
     *
     * @return void
     */
    public function setPort($value)
    {
        $this->port = $value;
    }

    /**
     * Return url
     *
     * @return string
     */
    public function getUrl()
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
    public function setUrl($value)
    {
        $this->url = $value;
    }

    /**
     * Return path
     *
     * @return string
     */
    public function getPath()
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
    public function setPath($value)
    {
        $this->path = $value;
    }

    /**
     * Return scheme
     *
     * @return string
     */
    public function getScheme()
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
    public function setScheme($value)
    {
        $this->scheme = $value;
    }

    /**
     * Return headers
     *
     * @return array
     */
    public function getHeaders()
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
    public function setHeaders(array $value)
    {
        $this->headers = $value;
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
     * Return method
     *
     * @return string
     */
    public function getMethod()
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
    public function setMethod($value)
    {
        $this->method = $value;
    }

    /**
     * Return body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set body
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
     * Return resource
     *
     * @return string
     */
    public function getResource()
    {
        return $this->resource;
    }

    /**
     * Set resource
     *
     * @param string $value
     *
     * @return void
     */
    public function setResource($value)
    {
        $this->resource = $value;
    }
}
