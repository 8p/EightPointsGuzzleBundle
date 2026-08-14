<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\Log;

use EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundle;
use EightPoints\Bundle\GuzzleBundle\Log\LogResponse;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Stream;
use PHPUnit\Framework\TestCase;

class LogResponseTest extends TestCase
{
    /** @var Response */
    protected $response;

    /** @var array */
    protected $headers = [
        'Date' => ['Sun, 07 Jun 2015 16:32:50 GMT'],
        'Expires' => ['-1'],
        'Cache-Control' => ['private, max-age=0'],
        'Content-Type' => ['text/html; charset=ISO-8859-1'],
    ];

    public function setUp(): void
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
        $this->response->method('getReasonPhrase')->willReturn('OK message');
    }

    /**
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\LogResponse::__construct
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\LogResponse::save
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\LogResponse::getStatusCode
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\LogResponse::setStatusCode
     */
    public function testStatusCode(): void
    {
        $response = new LogResponse($this->response);

        $this->assertSame(200, $response->getStatusCode());
    }

    public function testBodyLoggingDisabled(): void
    {
        $response = new LogResponse($this->response, false);

        $this->assertSame(EightPointsGuzzleBundle::class . ': [response body log disabled]', $response->getBody());
    }

    /**
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\LogResponse::__construct
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\LogResponse::save
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\LogResponse::getBody
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\LogResponse::setBody
     */
    public function testBody(): void
    {
        $response = new LogResponse($this->response);

        $this->assertSame('test body', $response->getBody());
    }

    /**
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\LogResponse::__construct
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\LogResponse::save
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\LogResponse::getProtocolVersion
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\LogResponse::setProtocolVersion
     */
    public function testProtocolVersion(): void
    {
        $response = new LogResponse($this->response);

        $this->assertSame('1.1', $response->getProtocolVersion());
    }

    /**
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\LogResponse::__construct
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\LogResponse::save
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\LogResponse::getHeaders
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\LogResponse::setHeaders
     */
    public function testHeaders(): void
    {
        $response = new LogResponse($this->response);

        $this->assertSame($this->headers, $response->getHeaders());
    }

    /**
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\LogResponse::__construct
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\LogResponse::setStatusPhrase
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\LogResponse::getStatusPhrase
     */
    public function testGetStatusPhrase(): void
    {
        $logResponse = new LogResponse($this->response);

        $this->assertEquals('OK message', $logResponse->getStatusPhrase());
    }
}
