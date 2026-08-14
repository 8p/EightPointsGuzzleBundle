<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests;

use EightPoints\Bundle\GuzzleBundle\DependencyInjection\EightPointsGuzzleExtension;
use EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundle;
use EightPoints\Bundle\GuzzleBundle\PluginInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EightPointsGuzzleBundleTest extends TestCase
{
    public function testInstance(): void
    {
        $this->assertInstanceOf(Bundle::class, new EightPointsGuzzleBundle());
    }

    public function testInitWithPlugin(): void
    {
        $plugin = $this->getMockBuilder(PluginInterface::class)->getMock();

        new EightPointsGuzzleBundle([$plugin]);

        // assert that it doesn't fail
        $this->addToAssertionCount(1);
    }

    public function testInitWithPluginsNameDuplication(): void
    {
        $this->expectException(InvalidConfigurationException::class);

        $firstPlugin = $this->getMockBuilder(PluginInterface::class)->getMock();
        $firstPlugin->expects($this->once())
            ->method('getPluginName')
            ->willReturn('wsse');

        $secondPlugin = $this->getMockBuilder(PluginInterface::class)->getMock();
        $secondPlugin->expects($this->exactly(2))
            ->method('getPluginName')
            ->willReturn('wsse');

        new EightPointsGuzzleBundle([$firstPlugin, $secondPlugin]);
    }

    public function testBoot(): void
    {
        $plugin = $this->getMockBuilder(PluginInterface::class)->getMock();
        $plugin->expects($this->once())->method('boot');

        $bundle = new EightPointsGuzzleBundle([$plugin]);
        $bundle->boot();
    }

    public function testBuild(): void
    {
        $container = new ContainerBuilder();

        $plugin = $this->getMockBuilder(PluginInterface::class)->getMock();
        $plugin->expects($this->once())->method('build');

        $bundle = new EightPointsGuzzleBundle([$plugin]);
        $bundle->build($container);
    }

    public function testGetContainerExtension(): void
    {
        $bundle = new EightPointsGuzzleBundle();

        $extension = $bundle->getContainerExtension();
        $this->assertInstanceOf(EightPointsGuzzleExtension::class, $extension);

        // assert that on each call new extension is not created
        $this->assertSame($extension, $bundle->getContainerExtension());
    }
}
