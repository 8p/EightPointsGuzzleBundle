<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\Twig\Extension;

use EightPoints\Bundle\GuzzleBundle\Twig\Extension\DebugExtension;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Extension\ExtensionInterface;
use Twig\TwigFunction;

class DebugExtensionTest extends TestCase
{
    public function testConstructor()
    {
        $extension = new DebugExtension();
        $this->assertInstanceOf(ExtensionInterface::class, $extension);
    }

    public function testDumpFunction()
    {
        $extension = new DebugExtension();
        $functions = $extension->getFunctions();

        $this->assertCount(1, $functions);

        /** @var TwigFunction $dumpFunction */
        $dumpFunction = $functions[0];
        $this->assertInstanceOf(TwigFunction::class, $dumpFunction);
        $this->assertEquals('eight_points_guzzle_dump', $dumpFunction->getName());
        $this->assertTrue($dumpFunction->needsEnvironment());

        $callable = $dumpFunction->getCallable();
        $this->assertInstanceOf(DebugExtension::class, $callable[0]);
        $this->assertEquals('dump', $callable[1]);
    }

    public function testDump()
    {
        /** @var Environment $environment */
        $environment = $this->createMock(Environment::class);

        $extension = new DebugExtension();
        $result = $extension->dump($environment, 'randomTestValue');

        $this->assertIsString('string', $result);
        $this->assertContains('randomTestValue', $result);
    }

    public function testGetName()
    {
        $this->assertEquals((new DebugExtension())->getName(), DebugExtension::class);
    }
}
