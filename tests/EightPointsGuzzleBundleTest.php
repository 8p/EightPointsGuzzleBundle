<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests;

use EightPoints\Bundle\GuzzleBundle\DependencyInjection\Compiler\EventHandlerCompilerPass;
use EightPoints\Bundle\GuzzleBundle\DependencyInjection\EightPointsGuzzleExtension;
use EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundle;
use EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundlePlugin;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
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

    public function testBoot()
    {
        $plugin = $this->getMockBuilder(EightPointsGuzzleBundlePlugin::class)->getMock();
        $plugin->expects($this->once())->method('boot');

        $bundle = new EightPointsGuzzleBundle([$plugin]);
        $bundle->boot();
    }

    public function testBuild()
    {
        $container = new ContainerBuilder();

        $plugin = $this->getMockBuilder(EightPointsGuzzleBundlePlugin::class)->getMock();
        $plugin->expects($this->once())->method('build');

        $bundle = new EightPointsGuzzleBundle([$plugin]);
        $bundle->build($container);

        // assert that compiler pass was registered
        $this->assertCount(1, array_filter(
            $container->getCompiler()->getPassConfig()->getPasses(),
            function (CompilerPassInterface $pass) {
                return $pass instanceof EventHandlerCompilerPass;
            }
        ));
    }

    public function testGetContainerExtension()
    {
        $bundle = new EightPointsGuzzleBundle();

        $extension = $bundle->getContainerExtension();
        $this->assertInstanceOf(EightPointsGuzzleExtension::class, $extension);

        // assert that on each call new extension is not created
        $this->assertSame($extension, $bundle->getContainerExtension());
    }
}
