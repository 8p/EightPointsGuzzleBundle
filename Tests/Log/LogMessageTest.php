<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\Log;

use EightPoints\Bundle\GuzzleBundle\Log\LogMessage;

/**
 * @version   2.1
 * @since     2015-05
 */
class LogMessageTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $message = 'message';
        $logMessage = new LogMessage($message);
        $this->assertEquals($logMessage->getMessage(), $message);
    }
}
