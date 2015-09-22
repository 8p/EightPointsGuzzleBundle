<?php

namespace EightPoints\Bundle\GuzzleBundle\DependencyInjection\Compiler;
use \Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class EventHandlerCompilerPass
 *
 * @package EightPoints\Bundle\GuzzleBundle\DependencyInjection\Compiler
 */
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
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        $taggedServices = $container->findTaggedServiceIds('kernel.event_listener');

        foreach($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                if(strstr($attributes['event'], 'guzzle_bundle') !== false) {
                    if(isset($attributes['service'])) {
                        $container->getDefinition($id)->addMethodCall(
                            'setServiceName', [$attributes['service']]
                        );
                    }
                }
            }
        }
    }

}
