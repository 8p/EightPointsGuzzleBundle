<?php

namespace EightPoints\Bundle\GuzzleBundle\DependencyInjection;

use function method_exists;
use EightPoints\Bundle\GuzzleBundle\Log\Logger;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class Configuration implements ConfigurationInterface
{
    /**
     * @var string
     */
    protected $alias;

    /**
     * @var boolean
     */
    protected $debug;

    /**
     * @var \EightPoints\Bundle\GuzzleBundle\PluginInterface[]
     */
    protected $plugins;

    /**
     * @param string $alias
     * @param boolean $debug
     * @param array $plugins
     */
    public function __construct(string $alias, bool $debug = false, array $plugins = [])
    {
        $this->alias = $alias;
        $this->debug = $debug;
        $this->plugins = $plugins;
    }

    /**
     * Generates the configuration tree builder
     *
     * @throws \RuntimeException
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder
     */
    public function getConfigTreeBuilder() : TreeBuilder
    {
        $builder = new TreeBuilder($this->alias);

        if (method_exists($builder, 'getRootNode')) {
            $root = $builder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $root = $builder->root($this->alias);
        }

        $root
            ->children()
                ->append($this->createClientsNode())
                ->booleanNode('logging')->defaultValue($this->debug)->end()
                ->booleanNode('profiling')->defaultValue($this->debug)->end()
                ->integerNode('slow_response_time')->defaultValue(0)->end()
                ->end()
            ->end();

        return $builder;
    }

    /**
     * Create Clients Configuration
     *
     * @throws \RuntimeException
     *
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition
     */
    private function createClientsNode() : ArrayNodeDefinition
    {
        $builder = new TreeBuilder('clients');

        /** @var \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition $node */
        if (method_exists($builder, 'getRootNode')) {
            $node = $builder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $node = $builder->root('clients');
        }

        /** @var \Symfony\Component\Config\Definition\Builder\NodeBuilder $nodeChildren */
        $nodeChildren = $node->useAttributeAsKey('name')
            ->prototype('array')
                ->children();

        $nodeChildren->scalarNode('class')->defaultValue('%eight_points_guzzle.http_client.class%')->end()
                    ->scalarNode('base_url')
                        ->defaultValue(null)
                        ->validate()
                            ->ifTrue(function ($v) {
                                return !is_string($v);
                            })
                            ->thenInvalid('base_url can be: string')
                        ->end()
                    ->end()
                    ->booleanNode('lazy')->defaultValue(false)->end()
                    ->integerNode('logging')
                        ->defaultValue(null)
                        ->beforeNormalization()
                            ->always(function ($value): int {
                                if ($value === 1 || $value === true) {
                                    return Logger::LOG_MODE_REQUEST_AND_RESPONSE;
                                } elseif ($value === 0 || $value === false) {
                                    return Logger::LOG_MODE_NONE;
                                } else {
                                    return constant(Logger::class .'::LOG_MODE_' . strtoupper($value));
                                }
                            })
                        ->end()
                    ->end()
                    ->scalarNode('handler')
                        ->defaultValue(null)
                        ->validate()
                            ->ifTrue(function ($v) {
                                return $v !== null && (!is_string($v) || !class_exists($v));
                            })
                            ->thenInvalid('handler must be a valid FQCN for a loaded class')
                        ->end()
                    ->end()
                    ->arrayNode('options')
                        ->validate()
                            ->ifTrue(function ($options) {
                                return count($options['form_params']) && count($options['multipart']);
                            })
                            ->thenInvalid('You cannot use form_params and multipart at the same time.')
                        ->end()
                        ->children()
                            ->arrayNode('headers')
                                ->useAttributeAsKey('name')
                                ->normalizeKeys(false)
                                ->prototype('scalar')->end()
                            ->end()
                            ->variableNode('allow_redirects')
                                ->validate()
                                    ->ifTrue(function ($v) {
                                        return !is_array($v) && !is_bool($v);
                                    })
                                    ->thenInvalid('allow_redirects can be: bool or array')
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
                            ->variableNode('query')
                                ->validate()
                                    ->ifTrue(function ($v) {
                                        return !is_string($v) && !is_array($v);
                                    })
                                    ->thenInvalid('query can be: string or array')
                                ->end()
                            ->end()
                            ->arrayNode('curl')
                                ->beforeNormalization()
                                    ->ifArray()
                                        ->then(function (array $curlOptions) {
                                            $result = [];

                                            foreach ($curlOptions as $key => $value) {
                                                $optionName = 'CURLOPT_' . strtoupper($key);

                                                if (!defined($optionName)) {
                                                    throw new InvalidConfigurationException(sprintf(
                                                        'Invalid curl option in eight_points_guzzle: %s. ' .
                                                        'Ex: use sslversion for CURLOPT_SSLVERSION option. ' . PHP_EOL .
                                                        'See all available options: http://php.net/manual/en/function.curl-setopt.php',
                                                        $key
                                                    ));
                                                }

                                                $result[constant($optionName)] = $value;
                                            }

                                            return $result;
                                        })
                                    ->end()
                                    ->prototype('scalar')
                                ->end()
                            ->end()
                            ->variableNode('cert')
                                ->validate()
                                    ->ifTrue(function ($v) {
                                        return !is_string($v) && (!is_array($v) || count($v) !== 2);
                                    })
                                    ->thenInvalid('cert can be: string or array with two entries (path and password)')
                                ->end()
                            ->end()
                            ->scalarNode('connect_timeout')
                                ->beforeNormalization()
                                    ->always(function ($v) {
                                        return is_numeric($v) ? (float) $v : $v;
                                    })
                                ->end()
                                ->validate()
                                    ->ifTrue(function ($v) {
                                        return !is_float($v) && !(is_string($v) && strpos($v, 'env_') === 0);
                                    })
                                    ->thenInvalid('connect_timeout can be: float')
                                ->end()
                            ->end()
                            ->booleanNode('debug')->end()
                            ->variableNode('decode_content')
                                ->validate()
                                    ->ifTrue(function ($v) {
                                        return !is_string($v) && !is_bool($v);
                                    })
                                    ->thenInvalid('decode_content can be: bool or string (gzip, compress, deflate, etc...)')
                                ->end()
                            ->end()
                            ->floatNode('delay')->end()
                            ->arrayNode('form_params')
                                ->useAttributeAsKey('name')
                                ->prototype('variable')->end()
                            ->end()
                            ->arrayNode('multipart')
                                ->prototype('variable')->end()
                            ->end()
                            ->scalarNode('sink')
                                ->validate()
                                    ->ifTrue(function ($v) {
                                        return !is_string($v);
                                    })
                                    ->thenInvalid('sink can be: string')
                                ->end()
                            ->end()
                            ->booleanNode('http_errors')->end()
                            ->variableNode('expect')
                                ->validate()
                                    ->ifTrue(function ($v) {
                                        return !is_bool($v) && !is_int($v);
                                    })
                                    ->thenInvalid('expect can be: bool or int')
                                ->end()
                            ->end()
                            ->variableNode('ssl_key')
                                ->validate()
                                    ->ifTrue(function ($v) {
                                        return !is_string($v) && (!is_array($v) || count($v) !== 2);
                                    })
                                    ->thenInvalid('ssl_key can be: string or array with two entries (path and password)')
                                ->end()
                            ->end()
                            ->booleanNode('stream')->end()
                            ->booleanNode('synchronous')->end()
                            ->scalarNode('read_timeout')
                                ->beforeNormalization()
                                    ->always(function ($v) {
                                        return is_numeric($v) ? (float) $v : $v;
                                    })
                                ->end()
                                ->validate()
                                    ->ifTrue(function ($v) {
                                        return !is_float($v) && !(is_string($v) && strpos($v, 'env_') === 0);
                                    })
                                    ->thenInvalid('read_timeout can be: float')
                                ->end()
                            ->end()
                            ->scalarNode('timeout')
                                ->beforeNormalization()
                                    ->always(function ($v) {
                                        return is_numeric($v) ? (float) $v : $v;
                                    })
                                ->end()
                                ->validate()
                                    ->ifTrue(function ($v) {
                                        return !is_float($v) && !(is_string($v) && strpos($v, 'env_') === 0);
                                    })
                                    ->thenInvalid('timeout can be: float')
                                ->end()
                            ->end()
                            ->variableNode('verify')
                                ->validate()
                                    ->ifTrue(function ($v) {
                                        return !is_bool($v) && !is_string($v);
                                    })
                                    ->thenInvalid('verify can be: bool or string')
                                ->end()
                            ->end()
                            ->booleanNode('cookies')->end()
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
                            ->scalarNode('version')
                                ->validate()
                                    ->ifTrue(function ($v) {
                                        return !is_string($v) && !is_float($v);
                                    })
                                    ->thenInvalid('version can be: string or float')
                                ->end()
                            ->end()
                        ->end()
                    ->end();

        $pluginsNode = $nodeChildren->arrayNode('plugin')->addDefaultsIfNotSet();

        foreach ($this->plugins as $plugin) {
            $pluginNode = new ArrayNodeDefinition($plugin->getPluginName());

            $plugin->addConfiguration($pluginNode);

            $pluginsNode->children()->append($pluginNode);
        }

        return $node;
    }
}
