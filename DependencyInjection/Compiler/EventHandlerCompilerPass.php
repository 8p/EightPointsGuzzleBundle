<?php

namespace EightPoints\Bundle\GuzzleBundle\DependencyInjection\Compiler;
use \Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Created by IntelliJ IDEA.
 * User: chris
 * Date: 9/16/15
 * Time: 2:30 PM
 */

class EventHandlerCompilerPass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
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
