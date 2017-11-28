<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\Log;

use EightPoints\Bundle\GuzzleBundle\Log\LogGroup;
use EightPoints\Bundle\GuzzleBundle\Log\LogMessage;
use PHPUnit\Framework\TestCase;

class LogGroupTest extends TestCase
{
    /**
     * Test Setting/Returning Request Name
     *
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\LogGroup::setRequestName
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\LogGroup::getRequestName
     */
    public function testRequestName()
    {
        $group = new LogGroup();

        $this->assertNull($group->getRequestName());

        $group->setRequestName('test');

        $this->assertSame('test', $group->getRequestName());
    }

    /**
     * Test Setting/Returning Log Messages
     *
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\LogGroup::setMessages
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\LogGroup::getMessages
     * @covers \EightPoints\Bundle\GuzzleBundle\Log\LogGroup::addMessages
     */
    public function testMessages()
    {
        $group = new LogGroup();

        $this->assertTrue(is_array($group->getMessages()));
        $this->assertEmpty($group->getMessages());

        $message1 = $this->getMockBuilder(LogMessage::class)
                         ->disableOriginalConstructor()
                         ->getMock();

        $message2 = $this->getMockBuilder(LogMessage::class)
                         ->disableOriginalConstructor()
                         ->getMock();

        $message3 = $this->getMockBuilder(LogMessage::class)
                         ->disableOriginalConstructor()
                         ->getMock();

        $messages = [$message1, $message2];

        $group->setMessages($messages);

        $this->assertCount(2, $group->getMessages());

        $group->addMessages([$message3]);

        $this->assertCount(3, $group->getMessages());

        foreach($group->getMessages() as $message) {
            $this->assertInstanceOf(LogMessage::class, $message);
        }

        // reset messages
        $group->setMessages([]);

        $this->assertTrue(is_array($group->getMessages()));
        $this->assertEmpty($group->getMessages());
    }
}
