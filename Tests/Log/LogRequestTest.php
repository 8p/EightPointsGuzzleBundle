<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\Log;

use EightPoints\Bundle\GuzzleBundle\Log\LogRequest;
use GuzzleHttp\Psr7\Stream;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\UriInterface;

/**
 * @version   2.1
 * @since     2015-05
 */
class LogRequestTest extends \PHPUnit_Framework_TestCase
{
    /** @var RequestInterface */
    protected $request;

    /**
     * @var array
     */
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
        $body = $this->getMockBuilder(Stream::class)
            ->disableOriginalConstructor()
            ->getMock();

        $body->method('__toString')->willReturn('test body');
        $body->method('isSeekable')->willReturn(false);

        $uri = $this->getMockBuilder(UriInterface::class)->getMock();
        $uri->method('getHost')->willReturn('localhost');
        $uri->method('getPort')->willReturn(80);
        $uri->method('getPath')->willReturn('/');
        $uri->method('getScheme')->willReturn('http');
        $uri->method('__toString')->willReturn('localhost');

        $this->request = $this->getMockBuilder(RequestInterface::class)->getMock();
        $this->request->method('getUri')->willReturn($uri);
        $this->request->method('getHeaders')->willReturn($this->headers);
        $this->request->method('getProtocolVersion')->willReturn('1.1');
        $this->request->method('getMethod')->willReturn('GET');
        $this->request->method('getBody')->willReturn($body);
    }

    public function testGetHost()
    {
        $logRequest = new LogRequest($this->request);
        $this->assertEquals('localhost', $logRequest->getHost());
    }

    public function testGetUri()
    {
        $logRequest = new LogRequest($this->request);
        $this->assertEquals('localhost', $logRequest->getUrl());
    }

    public function testGetPort()
    {
        $logRequest = new LogRequest($this->request);
        $this->assertEquals(80, $logRequest->getPort());
    }

    public function testGetPath()
    {
        $logRequest = new LogRequest($this->request);
        $this->assertEquals('/', $logRequest->getPath());
    }

    public function testGetScheme()
    {
        $logRequest = new LogRequest($this->request);
        $this->assertEquals('http', $logRequest->getScheme());
    }

    public function testGetHeaders()
    {
        $logRequest = new LogRequest($this->request);
        $this->assertSame($this->headers, $logRequest->getHeaders());
    }

    public function testGetProtocolVersion()
    {
        $logRequest = new LogRequest($this->request);
        $this->assertEquals('1.1', $logRequest->getProtocolVersion());
    }

    public function testGetMethod()
    {
        $logRequest = new LogRequest($this->request);
        $this->assertEquals('GET', $logRequest->getMethod());
    }

    public function testGetBody()
    {
        $logRequest = new LogRequest($this->request);
        $this->assertEquals('test body', $logRequest->getBody());
    }
}
