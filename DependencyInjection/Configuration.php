<?php

namespace EightPoints\Bundle\GuzzleBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @version 1.0
 * @since   2013-10
 */
class Configuration implements ConfigurationInterface
{

    /**
     * @var string $alias
     */
    protected $alias;

    /**
     * @var boolean $debug
     */
    protected $debug;

    /**
     * @version 1.0
     * @since   2013-10
     *
     * @param   string  $alias
     * @param   boolean $debug
     */
    public function __construct($alias, $debug = false)
    {
        $this->alias = $alias;
        $this->debug = (boolean) $debug;
    }

    /**
     * Generates the configuration tree builder
     *
     * @version 1.0
     * @since   2013-10
     *
     * @return  TreeBuilder
     * @throws  \RuntimeException
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $builder->root($this->alias)
                ->children()
                    ->append($this->createClientsNode())
                    ->booleanNode('logging')->defaultValue($this->debug)->end()
                    ->end()
                ->end();

        return $builder;
    }

    /**
     * Create Clients Configuration
     *
     * @since  2015-07
     *
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     * @return \Symfony\Component\Config\Definition\Builder\NodeDefinition
     * @throws \RuntimeException
     */
    private function createClientsNode()
    {
        $builder = new TreeBuilder();
        $node    = $builder->root('clients');

        // Filtering function to cast scalar values to boolean
        $boolFilter = function ($value) {
            return (bool)$value;
        };

        $node->useAttributeAsKey('name')
            ->prototype('array')
                ->children()
                    ->scalarNode('class')->defaultValue('%guzzle.http_client.class%')->end()
                    ->scalarNode('base_url')->defaultValue(null)->end()

                    // @todo @deprecated
                    ->arrayNode('headers')
                        ->normalizeKeys(false)
                        ->prototype('scalar')
                        ->end()
                    ->end()

                    ->arrayNode('options')
                        ->children()
                            ->arrayNode('headers')
                                ->normalizeKeys(false)
                                ->prototype('scalar')
                                ->end()
                            ->end()
                            ->variableNode('auth')
                                ->validate()
                                    ->ifTrue(function ($v) {
                                        return !is_array($v) && !is_string($v);
                                    })
                                    ->thenInvalid('auth can be: string or array')
                                ->end()
                            ->end()
                            ->arrayNode('query')
                                ->prototype('scalar')
                                ->end()
                            ->end()
                            ->variableNode('cert')
                                ->validate()
                                    ->ifTrue(function ($v) {
                                        return !is_string($v) && (!is_array($v) || count($v) != 2);
                                    })
                                    ->thenInvalid('A string or a two entries array required')
                                ->end()
                            ->end()
                            ->scalarNode('connect_timeout')->end()
                            ->booleanNode('debug')
                                ->beforeNormalization()
                                    ->ifString()->then($boolFilter)
                                ->end()
                            ->end()
                            ->booleanNode('decode_content')
                                ->beforeNormalization()
                                    ->ifString()->then($boolFilter)
                                ->end()
                            ->end()
                            ->scalarNode('delay')->end()
                            ->booleanNode('http_errors')
                                ->beforeNormalization()
                                    ->ifString()->then($boolFilter)
                                ->end()
                            ->end()
                            ->scalarNode('expect')->end()
                            ->scalarNode('ssl_key')->end()
                            ->booleanNode('stream')
                                ->beforeNormalization()
                                    ->ifString()->then($boolFilter)
                                ->end()
                            ->end()
                            ->booleanNode('synchronous')
                                ->beforeNormalization()
                                    ->ifString()->then($boolFilter)
                                ->end()
                            ->end()
                            ->scalarNode('timeout')->end()
                            ->booleanNode('verify')
                                ->beforeNormalization()
                                    ->ifString()->then($boolFilter)
                                ->end()
                            ->end()
                            ->booleanNode('cookies')
                                ->beforeNormalization()
                                    ->ifString()->then($boolFilter)
                                ->end()
                            ->end()
                            ->arrayNode('proxy')
                                ->beforeNormalization()
                                ->ifString()
                                    ->then(function($v) { return ['http'=> $v]; })
                                ->end()
                                ->validate()
                                    ->always(function($v) {
                                        if (empty($v['no'])) {
                                            unset($v['no']);
                                        }
                                        return $v;
                                    })
                                ->end()
                                ->children()
                                    ->scalarNode('http')->end()
                                    ->scalarNode('https')->end()
                                    ->arrayNode('no')
                                        ->prototype('scalar')->end()
                                    ->end()
                                ->end()
                            ->end()
                            ->scalarNode('version')->end()
                        ->end()
                    ->end()

                    ->arrayNode('plugin')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->arrayNode('wsse')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('username')->defaultFalse()->end()
                                    ->scalarNode('password')->defaultValue('')->end()
                                    ->scalarNode('created_at')->defaultFalse()->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $node;
    }
}
