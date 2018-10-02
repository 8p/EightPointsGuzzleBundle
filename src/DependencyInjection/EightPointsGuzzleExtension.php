<?php

namespace EightPoints\Bundle\GuzzleBundle\DependencyInjection;

use EightPoints\Bundle\GuzzleBundle\Twig\Extension\DebugExtension;
use RuntimeException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\ExpressionLanguage\Expression;
use GuzzleHttp\HandlerStack;

class EightPointsGuzzleExtension extends Extension
{
    /** @var \EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundlePlugin[] */
    protected $plugins;

    /**
     * @param \EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundlePlugin[] $plugins
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
     * @param array $configs an array of configuration settings
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container a ContainerBuilder instance
     *
     * @throws \InvalidArgumentException
     * @throws \Symfony\Component\DependencyInjection\Exception\BadMethodCallException
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws \Exception
     *
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configPath = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'Resources', 'config']);
        $loader     = new XmlFileLoader($container, new FileLocator($configPath));

        $loader->load('services.xml');

        $configuration = new Configuration($this->getAlias(), $container->getParameter('kernel.debug'), $this->plugins);
        $config        = $this->processConfiguration($configuration, $configs);
        $logging       = $config['logging'] === true;
        $profiling     = $config['profiling'] === true;

        foreach ($this->plugins as $plugin) {
            $container->addObjectResource(new \ReflectionClass(get_class($plugin)));
            $plugin->load($config, $container);
        }

        if ($logging) {
            $this->defineTwigDebugExtension($container);
            $this->defineLogger($container);
            $this->defineDataCollector($container);
            $this->defineFormatter($container);
            $this->defineSymfonyLogFormatter($container);
            $this->defineSymfonyLogMiddleware($container);
        }

        foreach ($config['clients'] as $name => $options) {
            $argument = [
                'base_uri' => $options['base_url'],
                'handler'  => $this->createHandler($container, $name, $options, $logging, $profiling)
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
            $client->setLazy($options['lazy']);

            // set service name based on client name
            $serviceName = sprintf('%s.client.%s', $this->getAlias(), $name);
            $container->setDefinition($serviceName, $client);
        }
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param string $clientName
     * @param array $options
     * @param bool $logging
     * @param bool $profiling
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\BadMethodCallException
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     *
     * @return \Symfony\Component\DependencyInjection\Definition
     */
    protected function createHandler(ContainerBuilder $container, string $clientName, array $options, bool $logging, bool $profiling) : Definition
    {
        // Event Dispatching service
        $eventServiceName = sprintf('eight_points_guzzle.middleware.event_dispatch.%s', $clientName);
        $eventService = $this->createEventMiddleware($clientName);
        $container->setDefinition($eventServiceName, $eventService);

        // Create the event Dispatch Middleware
        $eventExpression  = new Expression(sprintf("service('%s').dispatchEvent()", $eventServiceName));

        $handler = new Definition(HandlerStack::class);
        $handler->setFactory([HandlerStack::class, 'create']);
        if (isset($options['handler'])) {

            $handlerServiceName = sprintf('eight_points_guzzle.handler.%s', $clientName);
            $handlerService = new Definition($options['handler']);
            $container->setDefinition($handlerServiceName, $handlerService);

            $handler->addArgument($handlerService);
        }
        $handler->setPublic(true);
        $handler->setLazy($options['lazy']);

        $handlerStackServiceName = sprintf('eight_points_guzzle.handler_stack.%s', $clientName);
        $container->setDefinition($handlerStackServiceName, $handler);

        if ($logging) {
            $this->defineLogMiddleware($container, $handler, $clientName);
            $this->defineRequestTimeMiddleware($container, $handler, $clientName);
            $this->attachSymfonyLogMiddlewareToHandler($handler);
        }

        if ($profiling) {
            $this->defineProfileMiddleware($container, $handler, $clientName);
        }

        foreach ($this->plugins as $plugin) {
            $plugin->loadForClient($options['plugin'][$plugin->getPluginName()], $container, $clientName, $handler);
        }

        // goes on the end of the stack.
        $handler->addMethodCall('unshift', [$eventExpression, 'events']);

        return $handler;
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return void
     */
    protected function defineTwigDebugExtension(ContainerBuilder $container)
    {
        $twigDebugExtensionDefinition = new Definition(DebugExtension::class);
        $twigDebugExtensionDefinition->addTag('twig.extension');
        $twigDebugExtensionDefinition->setPublic(false);
        $container->setDefinition('eight_points_guzzle.twig_extension.debug', $twigDebugExtensionDefinition);
    }

    /**
     * Define Logger
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\BadMethodCallException
     *
     * @return void
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
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\BadMethodCallException
     *
     * @return void
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
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\BadMethodCallException
     *
     * @return void
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
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param \Symfony\Component\DependencyInjection\Definition $handler
     * @param string $clientName
     *
     * @return void
     */
    protected function defineRequestTimeMiddleware(ContainerBuilder $container, Definition $handler, string $clientName)
    {
        $requestTimeMiddlewareDefinitionName = sprintf('eight_points_guzzle.middleware.request_time.%s', $clientName);
        $requestTimeMiddlewareDefinition = new Definition('%eight_points_guzzle.middleware.request_time.class%');
        $requestTimeMiddlewareDefinition->addArgument(new Reference('eight_points_guzzle.logger'));
        $requestTimeMiddlewareDefinition->addArgument(new Reference('eight_points_guzzle.data_collector'));
        $requestTimeMiddlewareDefinition->setPublic(true);
        $container->setDefinition($requestTimeMiddlewareDefinitionName, $requestTimeMiddlewareDefinition);

        $requestTimeExpression = new Expression(sprintf("service('%s')", $requestTimeMiddlewareDefinitionName));
        $handler->addMethodCall('after', ['log', $requestTimeExpression, 'request_time']);
    }

    /**
     * Define Log Middleware for client
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param \Symfony\Component\DependencyInjection\Definition $handler
     * @param string $clientName
     *
     * @return void
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
     * Define Profile Middleware for client
     *
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param \Symfony\Component\DependencyInjection\Definition $handler
     * @param string $clientName
     *
     * @return void
     */
    protected function defineProfileMiddleware(ContainerBuilder $container, Definition $handler, string $clientName)
    {
        if (!class_exists('Symfony\Component\Stopwatch\Stopwatch')) {
            throw new RuntimeException('Eightpoint GuzzleBundle profiling requires "symfony/stopwatch" package');
        }

        $profileMiddlewareDefinitionName = sprintf('eight_points_guzzle.middleware.profile.%s', $clientName);
        $profileMiddlewareDefinition = new Definition('%eight_points_guzzle.middleware.profile.class%');
        $profileMiddlewareDefinition->addArgument(new Reference('debug.stopwatch'));
        $profileMiddlewareDefinition->setPublic(false);
        $container->setDefinition($profileMiddlewareDefinitionName, $profileMiddlewareDefinition);

        $profileExpression = new Expression(sprintf("service('%s').profile()", $profileMiddlewareDefinitionName));
        $handler->addMethodCall('push', [$profileExpression, 'profile']);
    }

    /**
     * @param \Symfony\Component\DependencyInjection\Definition $handler
     *
     * @return void
     */
    protected function attachSymfonyLogMiddlewareToHandler(Definition $handler)
    {
        $logExpression = new Expression(sprintf("service('%s')", 'eight_points_guzzle.middleware.symfony_log'));
        $handler->addMethodCall('push', [$logExpression, 'symfony_log']);
    }

    /**
     * Create Middleware For dispatching events
     *
     * @param string $name
     *
     * @return \Symfony\Component\DependencyInjection\Definition
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
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    protected function defineSymfonyLogFormatter(ContainerBuilder $container)
    {
        $formatterDefinition = new Definition('%eight_points_guzzle.symfony_log_formatter.class%');
        $formatterDefinition->setArguments(['%eight_points_guzzle.symfony_log_formatter.pattern%']);
        $formatterDefinition->setPublic(true);
        $container->setDefinition('eight_points_guzzle.symfony_log_formatter', $formatterDefinition);
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     *
     * @return void
     */
    protected function defineSymfonyLogMiddleware(ContainerBuilder $container)
    {
        $logMiddlewareDefinition = new Definition('%eight_points_guzzle.middleware.symfony_log.class%');
        $logMiddlewareDefinition->addArgument(new Reference('logger'));
        $logMiddlewareDefinition->addArgument(new Reference('eight_points_guzzle.symfony_log_formatter'));
        $logMiddlewareDefinition->setPublic(true);
        $logMiddlewareDefinition->addTag('monolog.logger', ['channel' => 'eight_points_guzzle']);
        $container->setDefinition('eight_points_guzzle.middleware.symfony_log', $logMiddlewareDefinition);
    }

    /**
     * Returns alias of extension
     *
     * @return string
     */
    public function getAlias() : string
    {
        return 'eight_points_guzzle';
    }
}
