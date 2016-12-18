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
                    ->scalarNode('base_url')->defaultValue(null)->end()

                    // @todo @deprecated
                    ->arrayNode('headers')
                        ->prototype('scalar')
                        ->end()
                    ->end()

                    ->arrayNode('options')
                        ->children()
                            ->arrayNode('headers')
                                ->prototype('scalar')
                                ->end()
                            ->end()
                            ->arrayNode('auth')
                                ->prototype('scalar')
                                ->end()
                            ->end()
                            ->arrayNode('query')
                                ->prototype('scalar')
                                ->end()
                            ->end()
                            ->scalarNode('cert')->end()
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
