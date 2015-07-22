<?php

namespace EightPoints\Bundle\GuzzleBundle\DependencyInjection;

use       Symfony\Component\DependencyInjection\ContainerBuilder,
          Symfony\Component\DependencyInjection\Loader\XmlFileLoader,
          Symfony\Component\DependencyInjection\Reference,
          Symfony\Component\DependencyInjection\Definition,

          Symfony\Component\Config\FileLocator,
          Symfony\Component\Config\Definition\Processor,

          Symfony\Component\HttpKernel\DependencyInjection\Extension,
          Symfony\Component\ExpressionLanguage\Expression;

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

    protected $logFormatter;

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

        $this->createLogger($container);

        foreach($config['clients'] as $name => $options) {

            $argument = [
                'base_uri' => $options['base_url'],
                'handler'  => $this->createHandler($container, $name, $options)
            ];

            $client = new Definition($container->getParameter('guzzle.http_client.class'));
            $client->addArgument($argument);

            // set service name based on client name
            $serviceName = sprintf('%s.client.%s', $this->getAlias(), $name);
            $container->setDefinition($serviceName, $client);
        }
    } // end: load()

    /**
     * @author Florian Preusner
     * @since  2015-
     *
     * @param ContainerBuilder $container
     * @param                  $name
     * @param array            $config
     *
     * @return Definition
     */
    protected function createHandler(ContainerBuilder $container, $name, array $config) {

        $logServiceName = sprintf('guzzle_bundle.middleware.log.%s', $name);
        $log = $this->createLogMiddleware($container);
        $container->setDefinition($logServiceName, $log);

        $headerServiceName = sprintf('guzzle_bundle.middleware.request_header.%s', $name);
        $requestHeader = $this->createRequestHeaderMiddleware($container, $config['headers']);
        $container->setDefinition($headerServiceName, $requestHeader);

        $headerExpression = new Expression(sprintf("service('%s').attach()", $headerServiceName));
        $logExpression    = new Expression(sprintf("service('%s').log()", $logServiceName));

        $handler = new Definition('GuzzleHttp\HandlerStack');
        $handler->setFactory(['GuzzleHttp\HandlerStack', 'create']);
        $handler->addMethodCall('push', [$headerExpression]);
        $handler->addMethodCall('push', [$logExpression]);

        // WSSE
        if(isset($config['plugin']['wsse'])
            && $username = $config['plugin']['wsse']['username']
            && $password = $config['plugin']['wsse']['password']) {

            $wsse            = $this->createWsseMiddleware($container, $username, $password);
            $wsseServiceName = sprintf('guzzle_bundle.middleware.wsse.%s', $name);

            $container->setDefinition($wsseServiceName, $wsse);

            $wsseExpression = new Expression(sprintf('service("%s").attach()', $wsseServiceName));

            $handler->addMethodCall('push', [$wsseExpression]);
        }

        return $handler;
    } // end: createHandler()

    /**
     * Create Logger
     *
     * @author Florian Preusner
     * @since  2015-07
     *
     * @param  ContainerBuilder $container
     *
     * @return Definition
     */
    protected function createLogger(ContainerBuilder $container) {

        $logger = new Definition($container->getParameter('guzzle_bundle.logger.class'));

        $container->setDefinition('guzzle_bundle.logger', $logger);

        return $logger;
    } // end: createLogger()

    /**
     * Create Middleware for Logging
     *
     * @author Florian Preusner
     * @since  2015-07
     *
     * @param  ContainerBuilder $container
     *
     * @return Definition
     */
    protected function createLogMiddleware(ContainerBuilder $container) {

        $log = new Definition($container->getParameter('guzzle_bundle.middleware.log.class'));
        $log->addArgument(new Reference('guzzle_bundle.logger'));
        $log->addArgument(new Reference('guzzle_bundle.formatter'));

        return $log;
    } // end: createLogMiddleware()

    /**
     * Create Middleware For Request Headers
     *
     * @author Florian Preusner
     * @since  2015-07
     *
     * @param  ContainerBuilder $container
     * @param  array            $headers
     *
     * @return Definition
     */
    protected function createRequestHeaderMiddleware(ContainerBuilder $container, array $headers) {

        $requestHeader = new Definition($container->getParameter('guzzle_bundle.middleware.request_header.class'));
        $requestHeader->addArgument($headers);

        return $requestHeader;
    } // end: createRequestHeaderMiddleware()

    /**
     * Create Middleware for WSSE
     *
     * @author Florian Preusner
     * @since  2015-07
     *
     * @param  ContainerBuilder $container
     * @param  string           $username
     * @param  string           $password
     *
     * @return Definition
     */
    protected function createWsseMiddleware(ContainerBuilder $container, $username, $password) {

        $wsse = new Definition($container->getParameter('guzzle_bundle.middleware.wsse.class'));
        $wsse->setArguments([$username, $password]);

        return $wsse;
    } // end: createWsseMiddleware()

    /**
     * Returns alias of extension
     *
     * @author  Florian Preusner
     * @version 1.1
     * @since   2013-12
     *
     * @return  string
     */
    public function getAlias() {

        return 'guzzle';
    } // end: getAlias()
} // end: GuzzleExtension
