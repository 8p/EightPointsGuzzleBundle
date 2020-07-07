<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\DependencyInjection;

use EightPoints\Bundle\GuzzleBundle\DependencyInjection\Configuration;
use EightPoints\Bundle\GuzzleBundle\DependencyInjection\EightPointsGuzzleExtension;
use EightPoints\Bundle\GuzzleBundle\Log\DevNullLogger;
use EightPoints\Bundle\GuzzleBundle\PluginInterface;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\Client;
use Symfony\Component\Stopwatch\Stopwatch;

class EightPointsGuzzleExtensionTest extends TestCase
{
    public function testGuzzleExtension()
    {
        $container = $this->createContainer();
        $extension = new EightPointsGuzzleExtension();
        $extension->load($this->getConfigs(), $container);

        // test Client
        $this->assertTrue($container->hasDefinition('eight_points_guzzle.client.test_api'));
        $testApi = $container->get('eight_points_guzzle.client.test_api');
        $this->assertInstanceOf(Client::class, $testApi);
        $this->assertEquals(new Uri('//api.domain.tld/path'), $testApi->getConfig('base_uri'));

        if (method_exists($container, 'registerAliasForArgument')) {
            $this->assertTrue($container->hasAlias(ClientInterface::class . ' $testApiClient'));
            $this->assertSame($testApi, $container->get(ClientInterface::class . ' $testApiClient'));

            $this->assertFalse($container->hasAlias('%eight_points_guzzle.http_client.class% $testApiClient'));
        }

        // test Services
        $this->assertTrue($container->hasDefinition('eight_points_guzzle.middleware.event_dispatch.test_api'));

        // test Client with custom class
        $this->assertTrue($container->hasDefinition('eight_points_guzzle.client.test_api_with_custom_class'));
        $definition = $container->getDefinition('eight_points_guzzle.client.test_api_with_custom_class');
        $this->assertSame(CustomClient::class, $definition->getClass());

        if (method_exists($container, 'registerAliasForArgument')) {
            $testApi = $container->get('eight_points_guzzle.client.test_api_with_custom_class');
            $this->assertTrue($container->hasAlias(CustomClient::class . ' $testApiWithCustomClassClient'));
            $this->assertSame($testApi, $container->get(ClientInterface::class . ' $testApiWithCustomClassClient'));
        }

        // test Client with custom handler
        $this->assertTrue($container->hasDefinition('eight_points_guzzle.client.test_api_with_custom_handler'));
        /** @var ClientInterface $client */
        $client = $container->get('eight_points_guzzle.client.test_api_with_custom_handler');
        $this->assertInstanceOf(HandlerStack::class, $client->getConfig('handler'));

        // The handler property doesn't have a setter so we have to use reflection to get to its value
        $handlerStackRefl = new \ReflectionClass($client->getConfig('handler'));
        $handler = $handlerStackRefl->getProperty('handler');
        $handler->setAccessible(true);

        $this->assertInstanceOf(MockHandler::class, $handler->getValue($client->getConfig('handler')));
    }

    public function testOverwriteHttpClientClass()
    {
        $container = $this->createContainer();
        $extension = new EightPointsGuzzleExtension();
        $extension->load($this->getConfigs(), $container);
        $container->setParameter('eight_points_guzzle.http_client.class', \stdClass::class);

        $this->assertInstanceOf(
            \stdClass::class,
            $container->get('eight_points_guzzle.client.test_api')
        );
    }

    public function testOverrideFormatterClass()
    {
        $container = $this->createContainer();
        $extension = new EightPointsGuzzleExtension();
        $extension->load($this->getConfigs(), $container);
        $container->setParameter('eight_points_guzzle.formatter.class', \stdClass::class);

        $this->assertInstanceOf(
            \stdClass::class,
            $container->get('eight_points_guzzle.formatter')
        );
    }

    public function testOverrideSymfonyLogFormatterClass()
    {
        $container = $this->createContainer();
        $extension = new EightPointsGuzzleExtension();
        $extension->load($this->getConfigs(), $container);
        $container->setParameter('eight_points_guzzle.symfony_log_formatter.class', \stdClass::class);

        $this->assertInstanceOf(
            \stdClass::class,
            $container->get('eight_points_guzzle.symfony_log_formatter')
        );
    }

    public function testOverrideSymfonyLogFormatterPatternClass()
    {
        $container = $this->createContainer();
        $extension = new EightPointsGuzzleExtension();
        $extension->load($this->getConfigs(), $container);
        $container->setParameter('eight_points_guzzle.symfony_log_formatter.pattern', '{uri} {code}');

        $container->compile();

        $this->assertEquals(
            '{uri} {code}',
            $container->getDefinition('eight_points_guzzle.symfony_log_formatter')->getArgument(0)
        );
    }

    public function testOverrideDataCollectorClass()
    {
        $container = $this->createContainer();
        $extension = new EightPointsGuzzleExtension();
        $extension->load($this->getConfigs(), $container);
        $container->setParameter('eight_points_guzzle.data_collector.class', \stdClass::class);

        $this->assertInstanceOf(
            \stdClass::class,
            $container->get('eight_points_guzzle.data_collector')
        );
    }

    public function testOverrideLoggerClass()
    {
        $container = $this->createContainer();
        $extension = new EightPointsGuzzleExtension();
        $extension->load($this->getConfigs(), $container);
        $container->setParameter('eight_points_guzzle.logger.class', DevNullLogger::class);

        $this->assertInstanceOf(
            DevNullLogger::class,
            $container->get('eight_points_guzzle.test_api_logger')
        );
    }

    public function testOverrideLogMiddlewareClass()
    {
        $container = $this->createContainer();
        $extension = new EightPointsGuzzleExtension();
        $extension->load($this->getConfigs(), $container);
        $container->setParameter('eight_points_guzzle.middleware.log.class', \stdClass::class);

        $this->assertInstanceOf(
            \stdClass::class,
            $container->get('eight_points_guzzle.middleware.log.test_api')
        );
    }

    public function testOverrideSymfonyLogMiddlewareClass()
    {
        $container = $this->createContainer();
        $extension = new EightPointsGuzzleExtension();
        $extension->load($this->getConfigs(), $container);
        $container->setParameter('eight_points_guzzle.middleware.symfony_log.class', \stdClass::class);

        $this->assertInstanceOf(
            \stdClass::class,
            $container->get('eight_points_guzzle.middleware.symfony_log')
        );
    }

    public function testOverrideEventDispatchMiddlewareClass()
    {
        $container = $this->createContainer();
        $extension = new EightPointsGuzzleExtension();
        $extension->load($this->getConfigs(), $container);
        $container->setParameter('eight_points_guzzle.middleware.event_dispatcher.class', \stdClass::class);

        $this->assertInstanceOf(
            \stdClass::class,
            $container->get('eight_points_guzzle.middleware.event_dispatch.test_api')
        );
    }

    public function testOverrideRequestTimeMiddlewareClass()
    {
        $container = $this->createContainer();
        $extension = new EightPointsGuzzleExtension();
        $extension->load($this->getConfigs(), $container);
        $container->setParameter('eight_points_guzzle.middleware.request_time.class', \stdClass::class);

        $this->assertInstanceOf(
            \stdClass::class,
            $container->get('eight_points_guzzle.middleware.request_time.test_api')
        );
    }

    public function testLoadWithLogging()
    {
        $config = $this->getConfigs();
        $config[0]['logging'] = true;

        $container = $this->createContainer();
        $extension = new EightPointsGuzzleExtension();
        $extension->load($config, $container);

        // test Client
        $this->assertTrue($container->hasDefinition('eight_points_guzzle.client.test_api'));
        $this->assertTrue($container->hasDefinition('eight_points_guzzle.client.test_api_with_custom_class'));

        // test Services
        $this->assertTrue($container->hasDefinition('eight_points_guzzle.middleware.log.test_api'));
        $this->assertTrue($container->hasDefinition('eight_points_guzzle.middleware.event_dispatch.test_api'));

        // test logging services (logger, data collector and log middleware for each client)
        foreach (['test_api', 'test_api_with_custom_class','test_api_with_custom_handler'] as $clientName) {
            $this->assertTrue($container->hasDefinition(sprintf('eight_points_guzzle.%s_logger', $clientName)));
        }
        $this->assertTrue($container->hasDefinition('eight_points_guzzle.data_collector'));
        $this->assertTrue($container->hasDefinition('eight_points_guzzle.formatter'));
        $this->assertTrue($container->hasDefinition('eight_points_guzzle.symfony_log_formatter'));
        $this->assertTrue($container->hasDefinition('eight_points_guzzle.twig_extension.debug'));
        $this->assertTrue($container->hasDefinition('eight_points_guzzle.middleware.symfony_log'));
        $this->assertTrue($container->hasDefinition('eight_points_guzzle.middleware.log.test_api'));
        $this->assertTrue($container->hasDefinition('eight_points_guzzle.middleware.log.test_api_with_custom_class'));
        $this->assertTrue($container->hasDefinition('eight_points_guzzle.middleware.request_time.test_api'));
        $this->assertTrue($container->hasDefinition('eight_points_guzzle.middleware.request_time.test_api_with_custom_class'));

        // test log middleware in handler of the client
        $this->assertCount(1, $this->getClientLogMiddleware($container, 'eight_points_guzzle.client.test_api'));
        $this->assertCount(1, $this->getClientLogMiddleware($container, 'eight_points_guzzle.client.test_api_with_custom_class'));
    }

    public function testLoadWithLoggingSpecificClient()
    {
        $config = $this->getConfigs();
        $config[0]['clients']['test_api_with_custom_class']['logging'] = false;

        $container = $this->createContainer();
        $extension = new EightPointsGuzzleExtension();
        $extension->load($config, $container);

        // test Client
        $this->assertTrue($container->hasDefinition('eight_points_guzzle.client.test_api'));
        $this->assertTrue($container->hasDefinition('eight_points_guzzle.client.test_api_with_custom_class'));

        // test logging services (logger, data collector and log middleware for each client)
        $clientLoggingStatuses = [
            'test_api' => true,
            'test_api_with_custom_class' => false,
            'test_api_with_custom_handler' => true
        ];
        foreach ($clientLoggingStatuses as $clientName => $expectedStatus) {
            $this->assertSame($expectedStatus, $container->hasDefinition(sprintf('eight_points_guzzle.%s_logger', $clientName)));
            $this->assertSame($expectedStatus, $container->hasDefinition(sprintf('eight_points_guzzle.middleware.log.%s', $clientName)));
            $this->assertSame($expectedStatus, $container->hasDefinition(sprintf('eight_points_guzzle.middleware.request_time.%s', $clientName)));

            // test log middleware in handler of the client
            $this->assertCount($expectedStatus ? 1 : 0, $this->getClientLogMiddleware($container, sprintf('eight_points_guzzle.client.%s', $clientName)));
        }

        $this->assertTrue($container->hasDefinition('eight_points_guzzle.data_collector'));
        $this->assertTrue($container->hasDefinition('eight_points_guzzle.formatter'));
        $this->assertTrue($container->hasDefinition('eight_points_guzzle.symfony_log_formatter'));
        $this->assertTrue($container->hasDefinition('eight_points_guzzle.twig_extension.debug'));
        $this->assertTrue($container->hasDefinition('eight_points_guzzle.middleware.symfony_log'));
    }

    public function testLoadWithoutLogging()
    {
        $config = $this->getConfigs();
        $config[0]['logging'] = false;

        $container = $this->createContainer();
        $extension = new EightPointsGuzzleExtension();
        $extension->load($config, $container);


        // test logging services (logger, data collector and log middleware for each client)
        $clientLoggingStatuses = [
            'test_api' => false,
            'test_api_with_custom_class' => false,
            'test_api_with_custom_handler' => false
        ];
        foreach ($clientLoggingStatuses as $clientName => $expectedStatus) {
            $this->assertSame($expectedStatus, $container->hasDefinition(sprintf('eight_points_guzzle.%s_logger', $clientName)));
            $this->assertSame($expectedStatus, $container->hasDefinition(sprintf('eight_points_guzzle.middleware.log.%s', $clientName)));
            $this->assertSame($expectedStatus, $container->hasDefinition(sprintf('eight_points_guzzle.middleware.request_time.%s', $clientName)));

            // test log middleware in handler of the client
            $this->assertCount($expectedStatus ? 1 : 0, $this->getClientLogMiddleware($container, sprintf('eight_points_guzzle.client.%s', $clientName)));
        }

        $this->assertFalse($container->hasDefinition('eight_points_guzzle.data_collector'));
        $this->assertFalse($container->hasDefinition('eight_points_guzzle.formatter'));
        $this->assertFalse($container->hasDefinition('eight_points_guzzle.symfony_log_formatter'));
        $this->assertFalse($container->hasDefinition('eight_points_guzzle.twig_extension.debug'));
        $this->assertFalse($container->hasDefinition('eight_points_guzzle.middleware.symfony_log'));
    }

    public function testGetConfiguration()
    {
        $extension = new EightPointsGuzzleExtension();
        $configuration = $extension->getConfiguration([], $this->createContainer());

        $this->assertInstanceOf(Configuration::class, $configuration);
    }

    public function testLoadWithPlugin()
    {
        $plugin = $this->createMock(PluginInterface::class);
        $plugin->method('getPluginName')
            ->willReturn('test');

        $plugin->expects($this->once())
            ->method('addConfiguration');

        $plugin->expects($this->once())
            ->method('load');

        $plugin->expects($this->once())
            ->method('loadForClient');

        $config = [
            [
                'clients' => [
                    'test_api' => [
                        'base_url' => '//api.domain.tld/path',
                        'plugin' => [
                            'test' => [],
                        ],
                    ],
                ],
            ],
        ];

        $container = $this->createContainer();
        $extension = new EightPointsGuzzleExtension([$plugin]);
        $extension->load($config, $container);
    }

    public function testLoadWithoutPlugin()
    {
        $plugin = $this->createMock(PluginInterface::class);
        $plugin->method('getPluginName')
            ->willReturn('test');

        $config = [
            [
                'clients' => [
                    'client_with_plugin' => [
                        'base_url' => '//api.domain.tld/path',
                        'plugin' => [
                            'test' => [],
                        ]
                    ],
                    'client_without_plugin' => [
                        'base_url' => '//api.domain.tld/path',
                    ],
                ],
            ],
        ];

        $plugin->expects($this->once())
            ->method('loadForClient');

        $container = $this->createContainer();
        $extension = new EightPointsGuzzleExtension([$plugin]);
        $extension->load($config, $container);
    }

    public function testLoadWithOptions()
    {
        $config = [
            [
                'clients' => [
                    'test_api' => [
                        'base_url' => '//api.domain.tld/path',
                        'options' => [
                            'auth' => ['acme', 'pa55w0rd'],
                            'headers' => [
                                'Accept' => 'application/json',
                            ],
                            'timeout' => 30,
                        ],
                    ],
                ],
            ],
        ];

        $container = $this->createContainer();
        $extension = new EightPointsGuzzleExtension();
        $extension->load($config, $container);

        $this->assertTrue($container->hasDefinition('eight_points_guzzle.client.test_api'));
        $definition = $container->getDefinition('eight_points_guzzle.client.test_api');
        $this->assertCount(1, $definition->getArguments());
        $this->assertArraySubset(
            [
                'base_uri' => '//api.domain.tld/path',
                'auth' => ['acme', 'pa55w0rd'],
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'timeout' => 30,
            ],
            $definition->getArgument(0)
        );
    }

    /**
     * @see https://github.com/8p/EightPointsGuzzleBundle/issues/235
     */
    public function testCompilation()
    {
        $container = $this->createContainer();
        $extension = new EightPointsGuzzleExtension();
        $extension->load($this->getConfigs(), $container);

        $container->compile();

        $this->assertInstanceOf(Client::class, $container->get('eight_points_guzzle.client.test_api'));
        $this->assertInstanceOf(CustomClient::class, $container->get('eight_points_guzzle.client.test_api_with_custom_class'));
        $this->assertInstanceOf(Client::class, $container->get('eight_points_guzzle.client.test_api_with_custom_handler'));
    }

    public function testMergingOfConfigurations()
    {
        $container = $this->createContainer();
        $container->registerExtension(new EightPointsGuzzleExtension());
        $container->setResourceTracking(false);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Fixtures/config'));
        $loader->load('test_api_1.yaml');
        $loader->load('test_api_2.yaml');
        $container->compile();

        $this->assertTrue($container->hasDefinition('eight_points_guzzle.client.test_api'));
        $this->assertEquals(
            ['foobar' => 'test', 'foobaz' => 'test'],
            $container->getDefinition('eight_points_guzzle.client.test_api')->getArgument(0)['headers']
        );

        $this->assertEquals(
            ['param1' => 1, 'param2' => 2],
            $container->getDefinition('eight_points_guzzle.client.test_api')->getArgument(0)['form_params']
        );
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private function createContainer() : ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.debug', true);
        $container->set('event_dispatcher', $this->createMock(EventDispatcherInterface::class));
        $container->set('logger', $this->createMock(LoggerInterface::class));
        $container->set('debug.stopwatch', $this->createMock(Stopwatch::class));

        return $container;
    }

    /**
     * @return array
     */
    private function getConfigs() : array
    {
        return [
            [
                'clients' => [
                    'test_api' => [
                        'base_url' => '//api.domain.tld/path',
                        'plugin' => [],
                    ],
                    'test_api_with_custom_class' => [
                        'class' => CustomClient::class,
                    ],
                    'test_api_with_custom_handler' => [
                        'handler' => 'GuzzleHttp\Handler\MockHandler',
                    ],
                ],
            ],
        ];
    }

    /**
     * @param \Symfony\Component\DependencyInjection\ContainerBuilder $container
     * @param string $clientName
     *
     * @return array
     */
    protected function getClientLogMiddleware(ContainerBuilder $container, string $clientName) : array
    {
        $this->assertCount(1, $container->getDefinition($clientName)->getArguments());
        $clientOptions = $container->getDefinition($clientName)->getArgument(0);
        $this->assertArrayHasKey('handler', $clientOptions);

        /** @var Definition $handler */
        $handler = $clientOptions['handler'];
        $this->assertInstanceOf(Definition::class, $handler);

        return array_filter($handler->getMethodCalls(), function(array $a) {
            return isset($a[1][1]) && $a[1][1] === 'log';
        });
    }
}

class CustomClient extends Client {}
