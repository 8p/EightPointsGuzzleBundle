<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\Log;

use EightPoints\Bundle\GuzzleBundle\Log\DevNullLogger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

class DevNullLoggerTest extends TestCase
{
    /**
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\DevNullLogger::log
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\DevNullLogger::hasMessages
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\DevNullLogger::getMessages
     */
    public function testLog()
    {
        $logger = new DevNullLogger();
        $logger->log(LogLevel::INFO, 'message');

        $this->assertFalse($logger->hasMessages());
        $this->assertCount(0, $logger->getMessages());
    }

    /**
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\DevNullLogger::clear
     */
    public function testClear()
    {
        $logger = new DevNullLogger();
        $logger->clear();

        $this->assertFalse($logger->hasMessages());
        $this->assertCount(0, $logger->getMessages());
    }
}
