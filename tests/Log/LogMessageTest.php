<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\Log;

use EightPoints\Bundle\GuzzleBundle\Log\LogMessage;
use PHPUnit\Framework\TestCase;

class LogMessageTest extends TestCase
{
    public function testConstruct(): void
    {
        $message = 'message';
        $logMessage = new LogMessage($message);
        $logMessage->setTransferTime(time());
        $this->assertEquals($logMessage->getMessage(), $message);
        $this->assertNotNull($logMessage->getTransferTime());
    }

    public function testCurlCommandIsNullWhenNotSet(): void
    {
        // The profiler template reads this getter on every message, and the curl
        // command is only set when namshi/cuzzle (a suggested package) is installed.
        $logMessage = new LogMessage('message');

        $this->assertNull($logMessage->getCurlCommand());
    }

    public function testSetCurlCommand(): void
    {
        $logMessage = new LogMessage('message');
        $logMessage->setCurlCommand("curl 'http://example.com'");

        $this->assertSame("curl 'http://example.com'", $logMessage->getCurlCommand());
    }
}
