<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests;

use EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundle;
use EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundlePlugin;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EightPointsGuzzleBundleTest extends TestCase
{
    public function testInstance()
    {
        $this->assertInstanceOf(Bundle::class, new EightPointsGuzzleBundle());
    }

    public function testInitWithPlugin()
    {
        $plugin = $this->getMockBuilder(EightPointsGuzzleBundlePlugin::class)->getMock();

        new EightPointsGuzzleBundle([$plugin]);

        // assert that it doesn't fail
        $this->addToAssertionCount(1);
    }

    public function testInitWithPluginsNameDuplication()
    {
        $this->expectException(InvalidConfigurationException::class);

        $firstPlugin = $this->getMockBuilder(EightPointsGuzzleBundlePlugin::class)->getMock();
        $firstPlugin->expects($this->once())
            ->method('getPluginName')
            ->willReturn('wsse');

        $secondPlugin = $this->getMockBuilder(EightPointsGuzzleBundlePlugin::class)->getMock();
        $secondPlugin->expects($this->exactly(2))
            ->method('getPluginName')
            ->willReturn('wsse');

        new EightPointsGuzzleBundle([$firstPlugin, $secondPlugin]);
    }
}
