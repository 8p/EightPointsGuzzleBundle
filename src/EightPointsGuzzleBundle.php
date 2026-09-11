<?php

namespace EightPoints\Bundle\GuzzleBundle;

use EightPoints\Bundle\GuzzleBundle\DependencyInjection\EightPointsGuzzleExtension;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EightPointsGuzzleBundle extends Bundle
{
    /** @var PluginInterface[] */
    protected array $plugins = [];

    /**
     * @param PluginInterface[] $plugins
     */
    public function __construct(array $plugins = [])
    {
        foreach ($plugins as $plugin) {
            $this->registerPlugin($plugin);
        }
    }

    /**
     * Build EightPointsGuzzleBundle
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        foreach ($this->plugins as $plugin) {
            $plugin->build($container);
        }
    }

    /**
     * Overwrite getContainerExtension
     *  - no naming convention of alias needed
     *  - extension class can be moved easily now
     *
     * @return ExtensionInterface The container extension
     */
    public function getContainerExtension(): ExtensionInterface
    {
        if ($this->extension === null) {
            $this->extension = new EightPointsGuzzleExtension($this->plugins);
        }

        return $this->extension;
    }

    public function boot(): void
    {
        foreach ($this->plugins as $plugin) {
            $plugin->boot();
        }
    }

    /**
     * @throws InvalidConfigurationException
     */
    protected function registerPlugin(PluginInterface $plugin): void
    {
        // Check plugins name duplication
        foreach ($this->plugins as $registeredPlugin) {
            if ($registeredPlugin->getPluginName() === $plugin->getPluginName()) {
                throw new InvalidConfigurationException(sprintf(
                    'Trying to connect two plugins with same name: %s',
                    $plugin->getPluginName()
                ));
            }
        }

        $this->plugins[] = $plugin;
    }
}
