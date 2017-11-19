<?php

namespace EightPoints\Bundle\GuzzleBundle\DependencyInjection;

use EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundlePlugin;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\ExpressionLanguage\Expression;
use GuzzleHttp\HandlerStack;

/**
 * @version   1.0
 * @since     2013-10
 */
class EightPointsGuzzleExtension extends Extension
{
    /** @var EightPointsGuzzleBundlePlugin[] */
    protected $plugins;

    /**
     * @param EightPointsGuzzleBundlePlugin[] $plugins
     */
    public function __construct(array $plugins = [])
    {
        $this->plugins = $plugins;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration(array $config, ContainerBuilder $container) : Configuration
    {
        return new Configuration($this->getAlias(), $container->getParameter('kernel.debug'), $this->plugins);
    }

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
        $configPath = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'Resources', 'config']);
        $loader     = new XmlFileLoader($container, new FileLocator($configPath));

        $loader->load('services.xml');

        $configuration = new Configuration($this->getAlias(), $container->getParameter('kernel.debug'), $this->plugins);
        $config        = $this->processConfiguration($configuration, $configs);
        $logging       = $config['logging'] === true;

        foreach ($this->plugins as $plugin) {
            $container->addObjectResource(new \ReflectionClass(get_class($plugin)));
            $plugin->load($config, $container);
        }

        if ($logging) {
            $this->defineLogger($container);
            $this->defineDataCollector($container);
            $this->defineFormatter($container);
        }

        foreach ($config['clients'] as $name => $options) {
            $argument = [
                'base_uri' => $options['base_url'],
                'handler'  => $this->createHandler($container, $name, $options, $logging)
            ];

            // if present, add default options to the constructor argument for the Guzzle client
            if (isset($options['options']) && is_array($options['options'])) {
                foreach ($options['options'] as $key => $value) {
                    if ($value === null || (is_array($value) && count($value) === 0)) {
                        continue;
                    }

                    $argument[$key] = $value;
                }
            }

            $client = new Definition($options['class']);
            $client->addArgument($argument);
            $client->setPublic(true);

            // set service name based on client name
            $serviceName = sprintf('%s.client.%s', $this->getAlias(), $name);
            $container->setDefinition($serviceName, $client);
        }
    }

    /**
     * @since  2015-07
     *
     * @param  ContainerBuilder $container
     * @param  string           $clientName
     * @param  array            $options
     * @param  bool             $logging
     *
     * @return Definition
     * @throws \Symfony\Component\DependencyInjection\Exception\BadMethodCallException
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    protected function createHandler(ContainerBuilder $container, string $clientName, array $options, bool $logging) : Definition
    {
        // Event Dispatching service
        $eventServiceName = sprintf('eight_points_guzzle.middleware.event_dispatch.%s', $clientName);
        $eventService = $this->createEventMiddleware($clientName);
        $container->setDefinition($eventServiceName, $eventService);

        // Create the event Dispatch Middleware
        $eventExpression  = new Expression(sprintf("service('%s').dispatchEvent()", $eventServiceName));

        $handler = new Definition(HandlerStack::class);
        $handler->setFactory([HandlerStack::class, 'create']);
        $handler->setPublic(true);

        if ($logging) {
            $this->defineLogMiddleware($container, $handler, $clientName);
            $this->defineRequestTimeMiddleware($container, $handler, $clientName);
        }

        foreach ($this->plugins as $plugin) {
            $plugin->loadForClient($options['plugin'][$plugin->getPluginName()], $container, $clientName, $handler);
        }

        // goes on the end of the stack.
        $handler->addMethodCall('unshift', [$eventExpression, 'events']);

        return $handler;
    }

    /**
     * Define Logger
     *
     * @since  2015-07
     *
     * @param  ContainerBuilder $container
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\BadMethodCallException
     */
    protected function defineLogger(ContainerBuilder $container)
    {
        $loggerDefinition = new Definition('%eight_points_guzzle.logger.class%');
        $loggerDefinition->setPublic(true);
        $container->setDefinition('eight_points_guzzle.logger', $loggerDefinition);
    }

    /**
     * Define Data Collector
     *
     * @since  2015-07
     *
     * @param  ContainerBuilder $container
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\BadMethodCallException
     */
    protected function defineDataCollector(ContainerBuilder $container)
    {
        $dataCollectorDefinition = new Definition('%eight_points_guzzle.data_collector.class%');
        $dataCollectorDefinition->addArgument(new Reference('eight_points_guzzle.logger'));
        $dataCollectorDefinition->setPublic(false);
        $dataCollectorDefinition->addTag('data_collector', [
            'id' => 'eight_points_guzzle',
            'template' => '@EightPointsGuzzle/debug.html.twig',
        ]);
        $container->setDefinition('eight_points_guzzle.data_collector', $dataCollectorDefinition);
    }

    /**
     * Define Formatter
     *
     * @param  ContainerBuilder $container
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\BadMethodCallException
     */
    protected function defineFormatter(ContainerBuilder $container)
    {
        $formatterDefinition = new Definition('%eight_points_guzzle.formatter.class%');
        $formatterDefinition->setPublic(true);
        $container->setDefinition('eight_points_guzzle.formatter', $formatterDefinition);
    }

    /**
     * Define Request Time Middleware
     *
     * @param ContainerBuilder $container
     * @param Definition $handler
     * @param string $clientName
     */
    protected function defineRequestTimeMiddleware(ContainerBuilder $container, Definition $handler, string $clientName)
    {
        $requestTimeMiddlewareDefinitionName = sprintf('eight_points_guzzle.middleware.request_time.%s', $clientName);
        $requestTimeMiddlewareDefinition = new Definition('%eight_points_guzzle.middleware.request_time.class%');
        $requestTimeMiddlewareDefinition->addArgument(new Reference('eight_points_guzzle.data_collector'));
        $requestTimeMiddlewareDefinition->setPublic(false);
        $container->setDefinition($requestTimeMiddlewareDefinitionName, $requestTimeMiddlewareDefinition);

        $requestTimeExpression = new Expression(sprintf("service('%s')", $requestTimeMiddlewareDefinitionName));
        $handler->addMethodCall('push', [$requestTimeExpression, 'request_time']);
    }

    /**
     * Define Log Middleware for client
     *
     * @param ContainerBuilder $container
     * @param Definition $handler
     * @param string $clientName
     */
    protected function defineLogMiddleware(ContainerBuilder $container, Definition $handler, string $clientName)
    {
        $logMiddlewareDefinitionName = sprintf('eight_points_guzzle.middleware.log.%s', $clientName);
        $logMiddlewareDefinition = new Definition('%eight_points_guzzle.middleware.log.class%');
        $logMiddlewareDefinition->addArgument(new Reference('eight_points_guzzle.logger'));
        $logMiddlewareDefinition->addArgument(new Reference('eight_points_guzzle.formatter'));
        $logMiddlewareDefinition->setPublic(true);
        $container->setDefinition($logMiddlewareDefinitionName, $logMiddlewareDefinition);

        $logExpression = new Expression(sprintf("service('%s').log()", $logMiddlewareDefinitionName));
        $handler->addMethodCall('push', [$logExpression, 'log']);
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
    protected function createEventMiddleware(string $name) : Definition
    {
        $eventMiddleWare = new Definition('%eight_points_guzzle.middleware.event_dispatcher.class%');
        $eventMiddleWare->addArgument(new Reference('event_dispatcher'));
        $eventMiddleWare->addArgument($name);
        $eventMiddleWare->setPublic(true);

        return $eventMiddleWare;
    }

    /**
     * Returns alias of extension
     *
     * @version 1.1
     * @since   2013-12
     *
     * @return  string
     */
    public function getAlias() : string
    {
        return 'eight_points_guzzle';
    }
}
