<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\Log;

use       EightPoints\Bundle\GuzzleBundle\Log\LogResponse;

/**
 * Class LogResponseTest
 *
 * @package   EightPoints\Bundle\GuzzleBundle\Test\Log
 * @author    Florian Preusner
 *
 * @version   2.1
 * @since     2015-05
 */
class LogResponseTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var \GuzzleHttp\Message\Response
     */
    protected $response;

    /**
     * SetUp: before executing each test function
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-06
     */
    public function setUp() {

        $this->response = $this->getMockBuilder('GuzzleHttp\Message\Response')
                               ->disableOriginalConstructor()
                               ->getMock();

        $this->response->method('getStatusCode')->willReturn(200);
        $this->response->method('getHeaders')->willReturn(['Content-Length' => 2435]);
        $this->response->method('getBody')->willReturn('test body');
        $this->response->method('getProtocolVersion')->willReturn('1.1');
    } // end: setUp()

    /**
     * Test Status Code
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-06
     *
     * @covers  EightPoints\Bundle\GuzzleBundle\Log\LogResponse::save
     * @covers  EightPoints\Bundle\GuzzleBundle\Log\LogResponse::getStatusCode
     * @covers  EightPoints\Bundle\GuzzleBundle\Log\LogResponse::setStatusCode
     */
    public function testStatusCode() {

        $response = new LogResponse($this->response);

        $this->assertSame(200, $response->getStatusCode());
    } // end: testStatusCode()

    /**
     * Test Body
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-06
     *
     * @covers  EightPoints\Bundle\GuzzleBundle\Log\LogResponse::save
     * @covers  EightPoints\Bundle\GuzzleBundle\Log\LogResponse::getBody
     * @covers  EightPoints\Bundle\GuzzleBundle\Log\LogResponse::setBody
     */
    public function testBody() {

        $response = new LogResponse($this->response);

        $this->assertSame('test body', $response->getBody());
    } // end: testBody()

    /**
     * Test Protocol Version
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-06
     *
     * @covers  EightPoints\Bundle\GuzzleBundle\Log\LogResponse::save
     * @covers  EightPoints\Bundle\GuzzleBundle\Log\LogResponse::getProtocolVersion
     * @covers  EightPoints\Bundle\GuzzleBundle\Log\LogResponse::setProtocolVersion
     */
    public function testProtocolVersion() {

        $response = new LogResponse($this->response);

        $this->assertSame('1.1', $response->getProtocolVersion());
    } // end: testProtocolVersion()

    /**
     * Test Headers
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-06
     *
     * @covers  EightPoints\Bundle\GuzzleBundle\Log\LogResponse::save
     * @covers  EightPoints\Bundle\GuzzleBundle\Log\LogResponse::getHeaders
     * @covers  EightPoints\Bundle\GuzzleBundle\Log\LogResponse::setHeaders
     */
    public function testHeaders() {

        $response = new LogResponse($this->response);
        $headers  = ['Content-Length' => 2435];

        $this->assertSame($headers, $response->getHeaders());
    } // end: testProtocolVersion()
} // end: LogResponseTest
