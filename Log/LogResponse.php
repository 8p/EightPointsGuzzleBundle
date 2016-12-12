<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

use Psr\Http\Message\ResponseInterface;

/**
 * @version 2.1
 * @since   2015-05
 */
class LogResponse
{
    /**
     * @var integer
     */
    protected $statusCode;

    /**
     * @var string
     */
    protected $statusPhrase;

    /**
     * @var string
     */
    protected $body;

    /**
     * @var array
     */
    protected $headers = array();

    /**
     * @var string
     */
    protected $protocolVersion;

    /**
     * Construct
     *
     * @version 2.1
     * @since   2015-05
     *
     * @param   ResponseInterface $response
     */
    public function __construct(ResponseInterface $response)
    {
        $this->save($response);
    }

    /**
     * Save data
     *
     * @version 2.1
     * @since   2015-05
     *
     * @param   ResponseInterface $response
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
     * @version 2.1
     * @since   2015-05
     *
     * @return  integer
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Set HTTP status code
     *
     * @version 2.1
     * @since   2015-05
     *
     * @param   integer $value
     */
    public function setStatusCode($value)
    {
        $this->statusCode = $value;
    }

    /**
     * Return HTTP status phrase
     *
     * @version 4.0
     * @since   2015-07
     *
     * @return  string
     */
    public function getStatusPhrase()
    {
        return $this->statusPhrase;
    }

    /**
     * Set HTTP status phrase
     *
     * @version 4.0
     * @since   2015-07
     *
     * @param   string $value
     */
    public function setStatusPhrase($value)
    {
        $this->statusPhrase = $value;
    }

    /**
     * Return response body
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
     * Set response body
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
     * Return response headers
     *
     * @version 2.1
     * @since   2015-05
     *
     * @return  array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Set response headers
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
}
