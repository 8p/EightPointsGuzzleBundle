<?php

namespace EightPoints\Bundle\GuzzleBundle\Middleware;

use Psr\Http\Message\RequestInterface;

/**
 * Adds headers to request
 *
 * @version   3.0
 * @since     2015-06
 */
class RequestHeaderMiddleware
{
    /**
     * @var array $headers
     */
    private $headers = [];

    /**
     * Constructor
     *
     * @version 1.0
     * @since   2013-10
     *
     * @param   array $headers
     */
    public function __construct(array $headers)
    {
        $this->setHeaders($headers);
    }

    /**
     * Retrieve headers that have been set
     *
     * @version 1.0
     * @since   2013-10
     *
     * @return  array $headers
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Set headers
     *
     * @version 1.0
     * @since   2013-10
     *
     * @param   array $headers
     * @return  void
     */
    public function setHeaders(array $headers)
    {
        foreach ($headers as $name => $value) {

            $this->addHeader($name, $value);
        }
    }

    /**
     * @version 1.0
     * @since   2013-10
     *
     * @param   string $key
     * @param   string $value
     *
     * @return  void
     */
    public function addHeader($key, $value)
    {
        $this->headers[$key] = $value;
    }

    /**
     * Get specified header
     *
     * @version 1.0
     * @since   2013-10
     *
     * @param   string $key
     * @return  string
     */
    public function getHeader($key)
    {
        if (array_key_exists($key, $this->headers)) {

            return $this->headers[$key];
        }

        return null;
    }

    /**
     * Add given headers to request
     *
     * @version 3.0
     * @since   2015-06
     *
     * @return  callable
     */
    public function attach()
    {
        return function (callable $handler) {

            return function (RequestInterface $request, array $options) use ($handler) {

                foreach ($this->getHeaders() as $key => $value) {

                    $request = $request->withHeader($key, $value);
                }

                return $handler($request, $options);
            };
        };
    }
}
