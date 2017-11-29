<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\DependencyInjection\Compiler;

use EightPoints\Bundle\GuzzleBundle\DependencyInjection\Compiler\EventHandlerCompilerPass;
use EightPoints\Bundle\GuzzleBundle\Events\GuzzleEvents;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use PHPUnit\Framework\TestCase;

class EventHandlerCompilerPassTest extends TestCase
{
    /**
     * Test the case when compiler pass added method call to proper service.
     *
     * @covers \EightPoints\Bundle\GuzzleBundle\DependencyInjection\Compiler\EventHandlerCompilerPass::process
     */
    public function testProcessAddedMethodCall()
    {
        $container = new ContainerBuilder();
        $container->register('foo_listener')
            ->addTag(
                'kernel.event_listener',
                ['event' => GuzzleEvents::PRE_TRANSACTION, 'method' => 'onPreTransaction', 'service' => 'servicename']
            );

        $eventHandlerCompilerPass = new EventHandlerCompilerPass();
        $eventHandlerCompilerPass->process($container);

        $fooListenerDefinition = $container->getDefinition('foo_listener');
        $this->assertCount(1, $fooListenerDefinition->getMethodCalls());
        $this->assertSame(
            ['setServiceName', ['servicename']],
            $fooListenerDefinition->getMethodCalls()[0]
        );
    }

    /**
     * Test the case when foreign service is defined but with similar tag structure.
     *
     * @covers \EightPoints\Bundle\GuzzleBundle\DependencyInjection\Compiler\EventHandlerCompilerPass::process
     */
    public function testProcessDoesNotAddedMethodCall()
    {
        $container = new ContainerBuilder();
        $container->register('foo_listener')
            ->addTag(
                'kernel.event_listener',
                ['event' => 'some.foreign.event', 'method' => 'onPreTransaction', 'service' => 'servicename']
            );

        $eventHandlerCompilerPass = new EventHandlerCompilerPass();
        $eventHandlerCompilerPass->process($container);

        $fooListenerDefinition = $container->getDefinition('foo_listener');
        $this->assertCount(0, $fooListenerDefinition->getMethodCalls());
    }
}
