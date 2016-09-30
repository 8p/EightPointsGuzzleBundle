<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

use       Psr\Http\Message\RequestInterface;

/**
 * Class LogRequest
 *
 * @package EightPoints\Bundle\GuzzleBundle\Log
 * @author  Florian Preusner
 *
 * @version 2.1
 * @since   2015-05
 */
class LogRequest {

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
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @param   RequestInterface $request
     */
    public function __construct(RequestInterface $request) {

        $this->save($request);
    } // end: __construct()

    /**
     * Save data
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @param   RequestInterface $request
     */
    protected function save(RequestInterface $request) {

        $uri = $request->getUri();

        $this->setHost($uri->getHost());
        $this->setPort($uri->getPort());
        $this->setUrl((string) $uri);
        $this->setPath($uri->getPath());
        $this->setScheme($uri->getScheme());
        $this->setHeaders($request->getHeaders());
        $this->setProtocolVersion($request->getProtocolVersion());
        $this->setMethod($request->getMethod());

        $readPosition = null;
        if($request->getBody() && $request->getBody()->isSeekable()) {
            $readPosition = $request->getBody()->tell();
        }

        $this->setBody($request->getBody() ? $request->getBody()->__toString() : null);

        if($readPosition) {
            $request->getBody()->seek($readPosition);
        }

    } // end: save()

    /**
     * Return host
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @return  string
     */
    public function getHost() {

        return $this->host;
    } // end: getHost()

    /**
     * Set request host
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @param   string $value
     */
    public function setHost($value) {

        $this->host = $value;
    } // end: setHost()

    /**
     * Return port
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @return  integer
     */
    public function getPort() {

        return $this->port;
    } // end: getPort()

    /**
     * Set port
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @param   integer $value
     */
    public function setPort($value) {

        $this->port = $value;
    } // end: setPort()

    /**
     * Return url
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @return  string
     */
    public function getUrl() {

        return $this->url;
    } // end: getUrl()

    /**
     * Set url
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @param   string $value
     */
    public function setUrl($value) {

        $this->url = $value;
    } // end: setUrl()

    /**
     * Return path
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @return  string
     */
    public function getPath() {

        return $this->path;
    } // end: getPath()

    /**
     * Set path
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @param   string $value
     */
    public function setPath($value) {

        $this->path = $value;
    } // end: setPath()

    /**
     * Return scheme
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @return  string
     */
    public function getScheme() {

        return $this->scheme;
    } // end: getScheme()

    /**
     * Set scheme
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @param   string $value
     */
    public function setScheme($value) {

        $this->scheme = $value;
    } // end: setScheme()

    /**
     * Return headers
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @return array
     */
    public function getHeaders() {

        return $this->headers;
    } // end: getHeaders()

    /**
     * Set headers
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @param   array $value
     */
    public function setHeaders(array $value) {

        $this->headers = $value;
    } // end: setHeaders()

    /**
     * Return protocol version
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @return  string
     */
    public function getProtocolVersion() {

        return $this->protocolVersion;
    } // end: getProtocolVersion()

    /**
     * Set protocol version
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @param   string $value
     */
    public function setProtocolVersion($value) {

        $this->protocolVersion = $value;
    } // end: setProtocolVersion()

    /**
     * Return method
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @return  string
     */
    public function getMethod() {

        return $this->method;
    } // end: getMethod()

    /**
     * Set method
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @param   string $value
     */
    public function setMethod($value) {

        $this->method = $value;
    } // end: setMethod()

    /**
     * Return body
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @return  string
     */
    public function getBody() {

        return $this->body;
    } // end: getBody()

    /**
     * Set body
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @param   string $value
     */
    public function setBody($value) {

        $this->body = $value;
    } // end: setBody()

    /**
     * Return resource
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @return  string
     */
    public function getResource() {

        return $this->resource;
    } // end: getResource()

    /**
     * Set resource
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @param   string $value
     */
    public function setResource($value) {

        $this->resource = $value;
    } // end: setResource()
} // end: LogRequest
