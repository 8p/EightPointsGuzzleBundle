<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\Middleware;

use EightPoints\Bundle\GuzzleBundle\Middleware\RequestHeaderMiddleware;

/**
 * Class RequestHeaderMiddlewareTest
 *
 * @package   EightPoints\Bundle\GuzzleBundle\Test\Middleware
 * @author    Florian Preusner
 *
 * @version   4.5
 * @since     2016-01
 */
class RequestHeaderMiddlewareTest extends \PHPUnit_Framework_TestCase {

    /**
     * Test Instance
     *
     * @author  Florian Preusner
     * @version 4.5
     * @since   2016-01
     *
     * @covers  EightPoints\Bundle\GuzzleBundle\Middleware\RequestHeaderMiddleware::__construct
     */
    public function testConstruct() {

        $headers = [
            'Accept' => 'application/json; version=2'
        ];

        $middleware      = new RequestHeaderMiddleware($headers);
        $returnedHeaders = $middleware->getHeaders();

        $this->assertSame($headers, $returnedHeaders);
    } // end: testConstruct()

    /**
     * Test Headers
     *
     * @author  Florian Preusner
     * @version 4.5
     * @since   2016-01
     *
     * @covers  EightPoints\Bundle\GuzzleBundle\Middleware\RequestHeaderMiddleware::getHeaders
     */
    public function testGetHeaders() {

        $headers = [
            'Accept'          => 'application/json; version=2',
            'Accept-Language' => 'de-DE,de;q=0.8,en-US;q=0.6,en;q=0.4',
            'Cache-Control'   => 'max-age=0'
        ];

        $middleware = new RequestHeaderMiddleware($headers);
        $returnedHeader = $middleware->getHeaders();

        $this->assertArrayHasKey('Accept', $returnedHeader);
        $this->assertArrayHasKey('Accept-Language', $returnedHeader);
        $this->assertArrayHasKey('Cache-Control', $returnedHeader);
    } // end: testGetHeaders()

    /**
     * Test Headers
     *
     * @author  Florian Preusner
     * @version 4.5
     * @since   2016-01
     *
     * @covers  EightPoints\Bundle\GuzzleBundle\Middleware\RequestHeaderMiddleware::getHeader
     */
    public function testGetSingleHeader() {

        $headers = [
            'Accept'          => 'application/json; version=2',
            'Accept-Language' => 'de-DE,de;q=0.8,en-US;q=0.6,en;q=0.4',
            'Cache-Control'   => 'max-age=0'
        ];

        $middleware = new RequestHeaderMiddleware($headers);

        $returnedHeaderValue = $middleware->getHeader('Cache-Control');

        $this->assertSame('max-age=0', $returnedHeaderValue);
    } // end: testGetSingleHeader()

    /**
     * Test Headers
     *
     * @author  Florian Preusner
     * @version 4.5
     * @since   2016-01
     *
     * @covers  EightPoints\Bundle\GuzzleBundle\Middleware\RequestHeaderMiddleware::getHeaders
     * @covers  EightPoints\Bundle\GuzzleBundle\Middleware\RequestHeaderMiddleware::addHeader
     */
    public function testAddHeader() {

        $headers = [
            'Accept'          => 'application/json; version=2',
            'Accept-Language' => 'de-DE,de;q=0.8,en-US;q=0.6,en;q=0.4',
            'Cache-Control'   => 'max-age=0'
        ];

        $middleware = new RequestHeaderMiddleware($headers);
        $middleware->addHeader('Connection', 'keep-alive');

        $headers['Connection'] = 'keep-alive';

        $returnedHeader = $middleware->getHeaders();

        $this->assertSame($headers, $returnedHeader);
    } // end: testAddHeader()

    /**
     * Test Headers
     *
     * @author  Florian Preusner
     * @version 4.5
     * @since   2016-01
     *
     * @covers  EightPoints\Bundle\GuzzleBundle\Middleware\RequestHeaderMiddleware::setHeaders
     */
    public function testSetHeaders() {

        $this->markTestSkipped('needs refactoring cause "set" is actually adding headers');

        $headers = [
            'Accept'          => 'application/json; version=2',
            'Accept-Language' => 'de-DE,de;q=0.8,en-US;q=0.6,en;q=0.4',
            'Cache-Control'   => 'max-age=0'
        ];

        $middleware = new RequestHeaderMiddleware($headers);

        $this->assertSame($headers, $middleware->getHeaders());

        $newHeader = ['Connection' => 'keep-alive'];
        $middleware->setHeaders($newHeader);

        $this->assertSame($newHeader, $middleware->getHeaders());
    } // end: testSetHeaders()
} // end: RequestHeaderMiddlewareTest