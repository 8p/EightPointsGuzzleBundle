<?php

namespace EightPoints\Bundle\GuzzleBundle\DependencyInjection;

use       Symfony\Component\Config\Definition\Builder\TreeBuilder,
          Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Configuration
 *
 * @package   EightPoints\Bundle\GuzzleBundle\DependencyInjection
 *
 * @copyright 8points IT
 * @author    Florian Preusner
 *
 * @version   1.0
 * @since     2013-10
 */
class Configuration implements ConfigurationInterface {

    /**
     * @var boolean $debug
     */
    private $debug;

    /**
     * Constructor
     *
     * @author  Florian Preusner
     * @version 1.0
     * @since   2013-10
     *
     * @param   boolean $debug
     */
    public function __construct($debug = false) {

        $this->debug = (boolean) $debug;
    } // end: __construct

    /**
     * Generates the configuration tree builder
     *
     * @author  Florian Preusner
     * @version 1.0
     * @since   2013-10
     *
     * @return  TreeBuilder
     */
    public function getConfigTreeBuilder() {

        $builder = new TreeBuilder();
        $builder->root('guzzle')
                    ->children()
                        ->scalarNode('base_url')->defaultValue(null)->end()

                        ->arrayNode('headers')
                            ->prototype('scalar')
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
                                    ->end()
                                ->end()
                            ->end()
                        ->end()

                        ->booleanNode('logging')->defaultValue($this->debug)->end()
                    ->end()
                ->end();

        return $builder;
    } // end: getConfigTreeBuilder
} // end: Configuration