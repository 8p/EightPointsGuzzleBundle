<?php

namespace EightPoints\Bundle\GuzzleBundle\DependencyInjection;

use       Symfony\Component\Config\FileLocator,
          Symfony\Component\DependencyInjection\ContainerBuilder,
          Symfony\Component\DependencyInjection\Loader\XmlFileLoader,
          Symfony\Component\HttpKernel\DependencyInjection\Extension,
          Symfony\Component\Config\Definition\Processor;
use Symfony\Component\ExpressionLanguage\Expression;

/**
 * GuzzleExtension
 *
 * @package   EightPoints\Bundle\GuzzleBundle\DependencyInjection
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

        $configPath = implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'Resources', 'config'));
        $loader     = new XmlFileLoader($container, new FileLocator($configPath));

        $loader->load('services.xml');

        $processor     = new Processor();
        $configuration = new Configuration($this->getAlias(), $container->getParameter('kernel.debug'));
        $config        = $processor->processConfiguration($configuration, $configs);

        $container->setParameter('guzzle.base_url',              $config['base_url']);
        $container->setParameter('guzzle.plugin.header.headers', $config['headers']);

        // WSSE
        if(isset($config['plugin']['wsse'])
            && $username = $config['plugin']['wsse']['username']
            && $password = $config['plugin']['wsse']['password']) {

            $container->setParameter('guzzle.plugin.wsse.username', $username);
            $container->setParameter('guzzle.plugin.wsse.password', $password);

            $container->getDefinition('guzzle.handler')
                      ->addMethodCall('push', array(new Expression('service("guzzle_bundle.middleware.wsse").attach()')));
        }
    } // end: load

    /**
     * Returns alias of class
     *
     * @author  Florian Preusner
     * @version 1.1
     * @since   2013-12
     *
     * @return  string
     */
    public function getAlias() {

        return 'guzzle';
    } // end: getAlias
} // end: GuzzleExtension
