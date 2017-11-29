<?php

namespace EightPoints\Bundle\GuzzleBundle\DependencyInjection\Compiler;

use EightPoints\Bundle\GuzzleBundle\Events\GuzzleEvents;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EventHandlerCompilerPass implements CompilerPassInterface
{
    /**
     * We tag handlers with specific services to listen too.
     *
     * We get all event tagged services from the container.
     * We then go through each event, and look for the value eight_points_guzzle_bundle.
     * For each one we find, we check if the service key is set, and then
     * call setServiceName on each EventListener.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     *
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds('kernel.event_listener');

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                if (isset($attributes['service']) && in_array($attributes['event'], GuzzleEvents::EVENTS, true)) {
                    $container->getDefinition($id)->addMethodCall(
                        'setServiceName',
                        [$attributes['service']]
                    );
                }
            }
        }
    }
}
