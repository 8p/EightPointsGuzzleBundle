<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\Twig\Extension;

use EightPoints\Bundle\GuzzleBundle\Twig\Extension\DebugExtension;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Extension\ExtensionInterface;
use Twig\TwigFunction;
use function class_exists;
use function interface_exists;

class DebugExtensionTest extends TestCase
{
    public function testConstructor()
    {
        $extensionClass = interface_exists(ExtensionInterface::class) ? ExtensionInterface::class : \Twig_ExtensionInterface::class;

        $extension = new DebugExtension();
        $this->assertInstanceOf($extensionClass, $extension);
    }

    public function testDumpFunction()
    {
        $functionClass = class_exists(TwigFunction::class) ? TwigFunction::class : \Twig_Function::class;
        $extension = new DebugExtension();
        $functions = $extension->getFunctions();

        $this->assertCount(1, $functions);

        /** @var \Twig_Function $dumpFunction */
        $dumpFunction = $functions[0];
        $this->assertInstanceOf($functionClass, $dumpFunction);
        $this->assertEquals('eight_points_guzzle_dump', $dumpFunction->getName());
        $this->assertTrue($dumpFunction->needsEnvironment());

        $callable = $dumpFunction->getCallable();
        $this->assertInstanceOf(DebugExtension::class, $callable[0]);
        $this->assertEquals('dump', $callable[1]);
    }

    public function testDump()
    {
        $environmentClass = class_exists(Environment::class) ? Environment::class : \Twig_Environment::class;
        $environment = $this->createMock($environmentClass);

        $extension = new DebugExtension();
        $result = $extension->dump($environment, 'randomTestValue');

        $this->assertInternalType('string', $result);
        $this->assertContains('randomTestValue', $result);
    }

    public function testGetName()
    {
        $this->assertEquals((new DebugExtension())->getName(), DebugExtension::class);
    }
}
