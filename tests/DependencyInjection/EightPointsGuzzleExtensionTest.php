<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\DependencyInjection;

use EightPoints\Bundle\GuzzleBundle\DependencyInjection\Configuration;
use EightPoints\Bundle\GuzzleBundle\DependencyInjection\EightPointsGuzzleExtension;
use EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundlePlugin;
use EightPoints\Bundle\GuzzleBundle\Log\DevNullLogger;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
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

        // test Services
        $this->assertTrue($container->hasDefinition('eight_points_guzzle.middleware.event_dispatch.test_api'));

        // test Client with custom class
        $this->assertTrue($container->hasDefinition('eight_points_guzzle.client.test_api_with_custom_class'));
        $definition = $container->getDefinition('eight_points_guzzle.client.test_api_with_custom_class');
        $this->assertSame('CustomGuzzleClass', $definition->getClass());
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
            $container->get('eight_points_guzzle.logger')
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
        $this->assertTrue($container->hasDefinition('eight_points_guzzle.logger'));
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

    public function testLoadWithoutLogging()
    {
        $config = $this->getConfigs();
        $config[0]['logging'] = false;

        $container = $this->createContainer();
        $extension = new EightPointsGuzzleExtension();
        $extension->load($config, $container);

        $this->assertFalse($container->hasDefinition('eight_points_guzzle.logger'));
        $this->assertFalse($container->hasDefinition('eight_points_guzzle.data_collector'));
        $this->assertFalse($container->hasDefinition('eight_points_guzzle.formatter'));
        $this->assertFalse($container->hasDefinition('eight_points_guzzle.symfony_log_formatter'));
        $this->assertFalse($container->hasDefinition('eight_points_guzzle.twig_extension.debug'));
        $this->assertFalse($container->hasDefinition('eight_points_guzzle.middleware.symfony_log'));
        $this->assertFalse($container->hasDefinition('eight_points_guzzle.middleware.log.test_api'));
        $this->assertFalse($container->hasDefinition('eight_points_guzzle.middleware.log.test_api_with_custom_class'));
        $this->assertFalse($container->hasDefinition('eight_points_guzzle.middleware.request_time.test_api'));
        $this->assertFalse($container->hasDefinition('eight_points_guzzle.middleware.request_time.test_api_with_custom_class'));

        // test log middleware in handler of the client
        $this->assertCount(0, $this->getClientLogMiddleware($container, 'eight_points_guzzle.client.test_api'));
        $this->assertCount(0, $this->getClientLogMiddleware($container, 'eight_points_guzzle.client.test_api_with_custom_class'));
    }

    public function testGetConfiguration()
    {
        $extension = new EightPointsGuzzleExtension();
        $configuration = $extension->getConfiguration([], $this->createContainer());

        $this->assertInstanceOf(Configuration::class, $configuration);
    }

    public function testLoadWithPlugin()
    {
        $plugin = $this->createMock(EightPointsGuzzleBundlePlugin::class);
        $plugin->expects($this->exactly(2))
            ->method('getPluginName')
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
                        'class' => 'CustomGuzzleClass',
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
