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

        $container->setParameter('guzzle.base_url', $config['base_url']);

        if($config['headers']) {

//            $this->setUpHeaders($config['headers'], $container);//@todo
        }

        if(isset($config['plugin']['wsse'])) {

//            $this->setUpWsse($config['plugin']['wsse'], $container);//@todo
        }
    } // end: load

    /**
     * Set up HTTP headers
     *
     * @author  Florian Preusner
     * @version 2.0
     * @since   2013-10
     *
     * @param   array            $headers
     * @param   ContainerBuilder $container
     *
     * @return  void
     */
    protected function setUpHeaders(array $headers, ContainerBuilder $container) {

        $container->setParameter('guzzle.plugin.header.headers', $headers);

        $container->getDefinition('guzzle.emitter')
                  ->addMethodCall('attach', array($container->getDefinition('guzzle.plugin.header')));
    } // end: setUpHeaders

    /**
     * Set up WSSE settings
     *
     * @author  Florian Preusner
     * @version 2.0
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

            $container->getDefinition('guzzle.emitter')
                      ->addMethodCall('attach', array($container->getDefinition('guzzle.plugin.wsse')));
        }
    } // end: setUpWsse

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
