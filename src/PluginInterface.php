<?php

namespace EightPoints\Bundle\GuzzleBundle;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

interface PluginInterface
{
    /**
     * The name of this plugin. It will be used as the configuration key.
     */
    public function getPluginName(): string;

    public function addConfiguration(ArrayNodeDefinition $pluginNode): void;

    /**
     * Load this plugin: define services, load service definition files, etc.
     */
    public function load(array $configs, ContainerBuilder $container): void;

    /**
     * Add configuration nodes for this plugin to the provided node.
     */
    public function loadForClient(array $config, ContainerBuilder $container, string $clientName, Definition $handler): void;

    /**
     * When the container is generated for the first time, you can register compiler passes inside this method.
     */
    public function build(ContainerBuilder $container);

    /**
     * When the bundles are booted, you can do any runtime initialization required inside this method.
     */
    public function boot();
}
