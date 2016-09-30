<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

use Psr\Http\Message\RequestInterface;

/**
 * @version 2.1
 * @since   2015-05
 */
class LogRequest
{
    /**
     * @var string
     */
    protected $host;

    /**
     * @var integer
     */
    protected $port;

    /**
     * @var string
     */
    protected $url;

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $scheme;

    /**
     * @var array
     */
    protected $headers = array();

    /**
     * @var string
     */
    protected $protocolVersion;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var string
     */
    protected $resource;

    /**
     * Construct
     *
     * @version 2.1
     * @since   2015-05
     *
     * @param   RequestInterface $request
     */
    public function __construct(RequestInterface $request)
    {
        $this->save($request);
    }

    /**
     * Save data
     *
     * @version 2.1
     * @since   2015-05
     *
     * @param   RequestInterface $request
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
        if($request->getBody() && $request->getBody()->isSeekable()) {
            $readPosition = $request->getBody()->tell();
        }

        $this->setBody($request->getBody() ? $request->getBody()->__toString() : null);

        if($readPosition) {
            $request->getBody()->seek($readPosition);
        }
    }

    /**
     * Return host
     *
     * @version 2.1
     * @since   2015-05
     *
     * @return  string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * Set request host
     *
     * @version 2.1
     * @since   2015-05
     *
     * @param   string $value
     */
    public function setHost($value)
    {
        $this->host = $value;
    }

    /**
     * Return port
     *
     * @version 2.1
     * @since   2015-05
     *
     * @return  integer
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Set port
     *
     * @version 2.1
     * @since   2015-05
     *
     * @param   integer $value
     */
    public function setPort($value)
    {
        $this->port = $value;
    }

    /**
     * Return url
     *
     * @version 2.1
     * @since   2015-05
     *
     * @return  string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set url
     *
     * @version 2.1
     * @since   2015-05
     *
     * @param   string $value
     */
    public function setUrl($value)
    {
        $this->url = $value;
    }

    /**
     * Return path
     *
     * @version 2.1
     * @since   2015-05
     *
     * @return  string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set path
     *
     * @version 2.1
     * @since   2015-05
     *
     * @param   string $value
     */
    public function setPath($value)
    {
        $this->path = $value;
    }

    /**
     * Return scheme
     *
     * @version 2.1
     * @since   2015-05
     *
     * @return  string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Set scheme
     *
     * @version 2.1
     * @since   2015-05
     *
     * @param   string $value
     */
    public function setScheme($value)
    {
        $this->scheme = $value;
    }

    /**
     * Return headers
     *
     * @version 2.1
     * @since   2015-05
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
     * @version 2.1
     * @since   2015-05
     *
     * @param   array $value
     */
    public function setHeaders(array $value)
    {
        $this->headers = $value;
    }

    /**
     * Return protocol version
     *
     * @version 2.1
     * @since   2015-05
     *
     * @return  string
     */
    public function getProtocolVersion()
    {
        return $this->protocolVersion;
    }

    /**
     * Set protocol version
     *
     * @version 2.1
     * @since   2015-05
     *
     * @param   string $value
     */
    public function setProtocolVersion($value)
    {
        $this->protocolVersion = $value;
    }

    /**
     * Return method
     *
     * @version 2.1
     * @since   2015-05
     *
     * @return  string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Set method
     *
     * @version 2.1
     * @since   2015-05
     *
     * @param   string $value
     */
    public function setMethod($value)
    {
        $this->method = $value;
    }

    /**
     * Return body
     *
     * @version 2.1
     * @since   2015-05
     *
     * @return  string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * Set body
     *
     * @version 2.1
     * @since   2015-05
     *
     * @param   string $value
     */
    public function setBody($value)
    {
        $this->body = $value;
    }

    /**
     * Return resource
     *
     * @version 2.1
     * @since   2015-05
     *
     * @return  string
     */
    public function getResource()
    {

        return $this->resource;
    }

    /**
     * Set resource
     *
     * @version 2.1
     * @since   2015-05
     *
     * @param   string $value
     */
    public function setResource($value)
    {
        $this->resource = $value;
    }
}
