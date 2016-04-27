<?php

namespace EightPoints\Bundle\GuzzleBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\ExpressionLanguage\Expression;

/**
 * @version   1.0
 * @since     2013-10
 */
class GuzzleExtension extends Extension
{
    protected $logFormatter;

    /**
     * Loads the Guzzle configuration.
     *
     * @version 1.0
     * @since   2013-10
     *
     * @param   array            $configs   an array of configuration settings
     * @param   ContainerBuilder $container a ContainerBuilder instance
     *
     * @throws  \InvalidArgumentException
     * @throws  \Symfony\Component\DependencyInjection\Exception\BadMethodCallException
     * @throws  \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws  \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configPath = implode(DIRECTORY_SEPARATOR, array(__DIR__, '..', 'Resources', 'config'));
        $loader     = new XmlFileLoader($container, new FileLocator($configPath));

        $loader->load('services.xml');

        $processor     = new Processor();
        $configuration = new Configuration($this->getAlias(), $container->getParameter('kernel.debug'));
        $config        = $processor->processConfiguration($configuration, $configs);

        $this->createLogger($container);

        foreach ($config['clients'] as $name => $options) {

            $argument = [
                'base_uri' => $options['base_url'],
                'handler'  => $this->createHandler($container, $name, $options)
            ];

            // If present, add default options to the constructor argument for the Guzzle client
            if (array_key_exists('options', $options)) {
                $argument = array_merge($options['options'], $argument);
            }

            $client = new Definition('%guzzle.http_client.class%');
            $client->addArgument($argument);

            // set service name based on client name
            $serviceName = sprintf('%s.client.%s', $this->getAlias(), $name);
            $container->setDefinition($serviceName, $client);
        }
    }

    /**
     * @since  2015-07
     *
     * @param  ContainerBuilder $container
     * @param  string           $name
     * @param  array            $config
     *
     * @return Definition
     * @throws \Symfony\Component\DependencyInjection\Exception\BadMethodCallException
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    protected function createHandler(ContainerBuilder $container, $name, array $config)
    {
        $logServiceName = sprintf('guzzle_bundle.middleware.log.%s', $name);
        $log = $this->createLogMiddleware();
        $container->setDefinition($logServiceName, $log);

        $headerServiceName = sprintf('guzzle_bundle.middleware.request_header.%s', $name);
        $requestHeader = $this->createRequestHeaderMiddleware($config['headers']);
        $container->setDefinition($headerServiceName, $requestHeader);

        // Event Dispatching service
        $eventServiceName = sprintf('guzzle_bundle.middleware.event_dispatch.%s', $name);
        $eventService = $this->createEventMiddleware($name);
        $container->setDefinition($eventServiceName, $eventService);

        $headerExpression = new Expression(sprintf("service('%s').attach()", $headerServiceName));
        $logExpression    = new Expression(sprintf("service('%s').log()", $logServiceName));
        // Create the event Dispatch Middleware
        $eventExpression  = new Expression(sprintf("service('%s').dispatchEvent()", $eventServiceName));

        $handler = new Definition('GuzzleHttp\HandlerStack');
        $handler->setFactory(['GuzzleHttp\HandlerStack', 'create']);

        // Plugins
        if (isset($config['plugin'])){
            // Wsse if required
            if (isset($config['plugin']['wsse'])
                && $config['plugin']['wsse']['username']
                && $config['plugin']['wsse']['password']) {

                $wsseConfig = $config['plugin']['wsse'];
                unset($config['plugin']['wsse']);

                $username = $wsseConfig['username'];
                $password = $wsseConfig['password'];

                $wsse = $this->createWsseMiddleware($username, $password);
                $wsseServiceName = sprintf('guzzle_bundle.middleware.wsse.%s', $name);

                $container->setDefinition($wsseServiceName, $wsse);

                $wsseExpression = new Expression(sprintf('service("%s").attach()', $wsseServiceName));

                $handler->addMethodCall('push', [$wsseExpression]);
            }
        }

        $handler->addMethodCall('push', [$headerExpression]);
        $handler->addMethodCall('push', [$logExpression]);
        // goes on the end of the stack.
        $handler->addMethodCall('unshift', [$eventExpression]);

        return $handler;
    }

    /**
     * Create Logger
     *
     * @since  2015-07
     *
     * @param  ContainerBuilder $container
     *
     * @return Definition
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\BadMethodCallException
     */
    protected function createLogger(ContainerBuilder $container)
    {

        $logger = new Definition('%guzzle_bundle.logger.class%');

        $container->setDefinition('guzzle_bundle.logger', $logger);

        return $logger;
    }

    /**
     * Create Middleware for Logging
     *
     * @since  2015-07
     *
     * @return Definition
     */
    protected function createLogMiddleware()
    {
        $log = new Definition('%guzzle_bundle.middleware.log.class%');
        $log->addArgument(new Reference('guzzle_bundle.logger'));
        $log->addArgument(new Reference('guzzle_bundle.formatter'));

        return $log;
    }

    /**
     * Create Middleware For Request Headers
     *
     * @since  2015-07
     *
     * @param  array $headers
     *
     * @return Definition
     */
    protected function createRequestHeaderMiddleware(array $headers)
    {
        $requestHeader = new Definition('%guzzle_bundle.middleware.request_header.class%');
        $requestHeader->addArgument($this->cleanUpHeaders($headers));

        return $requestHeader;
    }

    /**
     * Create Middleware For dispatching events
     *
     * @since  2015-09
     *
     * @param  string $name
     *
     * @return Definition
     */
    protected function createEventMiddleware($name)
    {
        $eventMiddleWare = new Definition('%guzzle_bundle.middleware.event_dispatcher.class%');
        $eventMiddleWare->addArgument(new Reference('event_dispatcher'));
        $eventMiddleWare->addArgument($name);

        return $eventMiddleWare;
    }

    /**
     * Create Middleware for WSSE
     *
     * @since  2015-07
     *
     * @param  string  $username
     * @param  string  $password
     *
     * @return Definition
     */
    protected function createWsseMiddleware($username, $password)
    {
        $wsse = new Definition('%guzzle_bundle.middleware.wsse.class%');
        $wsse->setArguments([$username, $password]);

        return $wsse;
    }

    /**
     * Clean up HTTP headers
     *
     * @since  2015-07
     *
     * @param  array $headers
     *
     * @return array
     */
    protected function cleanUpHeaders(array $headers)
    {
        foreach ($headers as $name => $value) {

            // because of standard conventions in YAML dashes are converted to underscores
            // underscores are not allowed in HTTP standard, will be replaced by dash
            $nameClean = str_replace('_', '-', $name);

            unset($headers[$name]);

            $headers[$nameClean] = $value;
        }

        return $headers;
    }

    /**
     * Returns alias of extension
     *
     * @version 1.1
     * @since   2013-12
     *
     * @return  string
     */
    public function getAlias()
    {
        return 'guzzle';
    }
}
