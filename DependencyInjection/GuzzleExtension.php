<?php

namespace EightPoints\Bundle\GuzzleBundle\DependencyInjection;

use       Symfony\Component\Config\FileLocator,
          Symfony\Component\DependencyInjection\ContainerBuilder,
          Symfony\Component\DependencyInjection\Loader\XmlFileLoader,
          Symfony\Component\HttpKernel\DependencyInjection\Extension,
          Symfony\Component\Config\Definition\Processor;

/**
 * GuzzleExtension
 *
 * @package   EightPoints\Bundle\GuzzleBundle\DependencyInjection
 *
 * @copyright 8points IT
 * @author    Florian Preusner
 *
 * @version   1.0
 * @since     2013-10
 */
class GuzzleExtension extends Extension {

    /**
     * Loads the Guzzle configuration.
     *
     * @author  Florian Preusner
     * @version 1.0
     * @since   2013-10
     *
     * @param   array            $configs   an array of configuration settings
     * @param   ContainerBuilder $container a ContainerBuilder instance
     *
     * @throws  \InvalidArgumentException
     */
    public function load(array $configs, ContainerBuilder $container) {

        $loader = new XmlFileLoader(
            $container,
            new FileLocator(implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'Resources', 'config')))
        );

        $loader->load('services.xml');

        $processor     = new Processor();
        $configuration = new Configuration($container->getParameter('kernel.debug'));
        $config        = $processor->processConfiguration($configuration, $configs);

        $container->setParameter('guzzle.base_url', $config['base_url']);

        if(isset($config['plugin']['wsse'])) {

            $this->setUpWsse($config['plugin']['wsse'], $container);
        }
    } // end: load

    /**
     * Set up WSSE settings
     *
     * @author  Florian Preusner
     * @version 1.0
     * @since   2013-10
     *
     * @param   array            $config
     * @param   ContainerBuilder $container
     *
     * @return  void
     */
    protected function setUpWsse(array $config, ContainerBuilder $container) {

        if($config['username']) {

            $container->setParameter('guzzle.plugin.wsse.username', $config['username']);
            $container->setParameter('guzzle.plugin.wsse.password', $config['password']);

            $container->getDefinition('guzzle.client')
                      ->addMethodCall('getEventDispatcher')
                      ->addMethodCall('addSubscriber', array($container->getDefinition('guzzle.plugin.wsse')));
        }
    } // end: setUpWsse
} // end: GuzzleExtension