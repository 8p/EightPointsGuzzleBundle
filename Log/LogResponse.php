<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

use       Psr\Http\Message\ResponseInterface;

/**
 * Class LogResponse
 *
 * @package EightPoints\Bundle\GuzzleBundle\Log
 * @author  Florian Preusner
 *
 * @version 2.1
 * @since   2015-05
 */
class LogResponse {

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
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @param   ResponseInterface $response
     */
    public function __construct(ResponseInterface $response) {

        $this->save($response);
    } // end: __construct()

    /**
     * Save data
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @param   ResponseInterface $response
     */
    public function save(ResponseInterface $response) {

        $this->setStatusCode($response->getStatusCode());
        $this->setStatusPhrase($response->getReasonPhrase());

        if($response->getBody()->isSeekable()) {
            $readPosition = $response->getBody()->tell();
        }
        $this->setBody($response->getBody()->__toString());
        if($response->getBody()->isSeekable()) {
            $response->getBody()->seek($readPosition);
        }

        $this->setHeaders($response->getHeaders());
        $this->setProtocolVersion($response->getProtocolVersion());
    } // end: save()

    /**
     * Return HTTP status code
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @return  integer
     */
    public function getStatusCode() {

        return $this->statusCode;
    } // end: getStatusCode()

    /**
     * Set HTTP status code
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @param   integer $value
     */
    public function setStatusCode($value) {

        $this->statusCode = $value;
    } // end: setStatusCode()

    /**
     * Return HTTP status phrase
     *
     * @author  Florian Preusner
     * @version 4.0
     * @since   2015-07
     *
     * @return  string
     */
    public function getStatusPhrase() {

        return $this->statusPhrase;
    } // end: getStatusPhrase()

    /**
     * Set HTTP status phrase
     *
     * @author  Florian Preusner
     * @version 4.0
     * @since   2015-07
     *
     * @param   string $value
     */
    public function setStatusPhrase($value) {

        $this->statusPhrase = $value;
    } // end: setStatusPhrase()

    /**
     * Return response body
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
     * Set response body
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
     * Return response headers
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @return  array
     */
    public function getHeaders() {

        return $this->headers;
    } // end: getHeaders()

    /**
     * Set response headers
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
} // end: LogResponse
