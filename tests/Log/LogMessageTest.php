<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\Log;

use EightPoints\Bundle\GuzzleBundle\Log\LogMessage;
use PHPUnit\Framework\TestCase;

class LogMessageTest extends TestCase
{
    public function testConstruct()
    {
        $message = 'message';
        $logMessage = new LogMessage($message);
        $this->assertEquals($logMessage->getMessage(), $message);
    }
}
