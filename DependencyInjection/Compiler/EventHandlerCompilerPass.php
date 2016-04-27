<?php

namespace EightPoints\Bundle\GuzzleBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class EventHandlerCompilerPass implements CompilerPassInterface
{
    /**
     * We tag handlers with specific services to listen too.
     *
     * We get all event tagged services from the container.
     * We then go through each event, and look for the value guzzle_bundle.
     * For each one we find, we check if the service key is set, and then
     * call setServiceName on each EventListener.
     *
     * @api
     *
     * @param  \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @return void
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function process(ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds('kernel.event_listener');

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                if (array_key_exists('service', $attributes)
                    && false !== strstr($attributes['event'], 'guzzle_bundle')) {

                    $container->getDefinition($id)->addMethodCall(
                        'setServiceName',
                        [$attributes['service']]
                    );
                }
            }
        }
    }
}
