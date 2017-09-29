<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\Log;

use EightPoints\Bundle\GuzzleBundle\Log\LogResponse;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;

/**
 * @version   2.1
 * @since     2015-05
 */
class LogResponseTest extends TestCase
{
    /** @var Response */
    protected $response;

    /** @var array */
    protected $headers = [
        'Date'          => ['Sun, 07 Jun 2015 16:32:50 GMT'],
        'Expires'       => ['-1'],
        'Cache-Control' => ['private, max-age=0'],
        'Content-Type'  => ['text/html; charset=ISO-8859-1']
    ];

    /**
     * SetUp: before executing each test function
     *
     * @version 2.1
     * @since   2015-06
     */
    public function setUp()
    {
        $this->response = $this->getMockBuilder(Response::class)
                               ->disableOriginalConstructor()
                               ->getMock();

        $bodyMock = $this->getMockBuilder(Stream::class)
                         ->disableOriginalConstructor()
                         ->getMock();

        $bodyMock->method('getContents')->willReturn('test body');

        $this->response->method('getStatusCode')->willReturn(200);
        $this->response->method('getHeaders')->willReturn($this->headers);
        $this->response->method('getBody')->willReturn($bodyMock);
        $this->response->method('getProtocolVersion')->willReturn('1.1');
    }

    /**
     * Test Status Code
     *
     * @version 2.1
     * @since   2015-06
     *
     * @covers LogResponse::__construct
     * @covers LogResponse::save
     * @covers LogResponse::getStatusCode
     * @covers LogResponse::setStatusCode
     */
    public function testStatusCode()
    {
        $response = new LogResponse($this->response);

        $this->assertSame(200, $response->getStatusCode());
    }

    /**
     * Test Body
     *
     * @version 2.1
     * @since   2015-06
     *
     * @covers LogResponse::__construct
     * @covers LogResponse::save
     * @covers LogResponse::getBody
     * @covers LogResponse::setBody
     */
    public function testBody()
    {
        $response = new LogResponse($this->response);

        $this->assertSame('test body', $response->getBody());
    }

    /**
     * Test Protocol Version
     *
     * @version 2.1
     * @since   2015-06
     *
     * @covers LogResponse::__construct
     * @covers LogResponse::save
     * @covers LogResponse::getProtocolVersion
     * @covers LogResponse::setProtocolVersion
     */
    public function testProtocolVersion()
    {
        $response = new LogResponse($this->response);

        $this->assertSame('1.1', $response->getProtocolVersion());
    }

    /**
     * Test Headers
     *
     * @version 2.1
     * @since   2015-06
     *
     * @covers LogResponse::__construct
     * @covers LogResponse::save
     * @covers LogResponse::getHeaders
     * @covers LogResponse::setHeaders
     */
    public function testHeaders()
    {
        $response = new LogResponse($this->response);

        $this->assertSame($this->headers, $response->getHeaders());
    }
}
