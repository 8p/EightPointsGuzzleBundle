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

        if($config['plugin']['wsse']['username']) {

            $container->setParameter('guzzle.plugin.wsse.username', $config['plugin']['wsse']['username']);
            $container->setParameter('guzzle.plugin.wsse.password', $config['plugin']['wsse']['password']);

            $wsse   = $container->get('guzzle.plugin.wsse');
            $client = $container->get('guzzle.client');
            $client->getEventDispatcher()->addSubscriber($wsse);
        }
    } // end: load()
} // end: GuzzleExtension