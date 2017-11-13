<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\DependencyInjection;

use EightPoints\Bundle\GuzzleBundle\DependencyInjection\Configuration;
use EightPoints\Bundle\GuzzleBundle\DependencyInjection\EightPointsGuzzleExtension;
use EightPoints\Bundle\GuzzleBundle\Tests\DependencyInjection\Fixtures\FakeClient;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;

/**
 * @version 2.1
 * @since   2015-05
 */
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

    public function testOverwriteClasses()
    {
        $container = $this->createContainer();
        $extension = new EightPointsGuzzleExtension();
        $extension->load($this->getConfigs(), $container);

        $container->setParameter('eight_points_guzzle.http_client.class', FakeClient::class);

        $client = $container->get('eight_points_guzzle.client.test_api', FakeClient::class);
        $this->assertInstanceOf(FakeClient::class, $client);
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
        $this->assertTrue($container->hasDefinition('eight_points_guzzle.middleware.log.test_api'));
        $this->assertTrue($container->hasDefinition('eight_points_guzzle.middleware.log.test_api_with_custom_class'));

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
        $this->assertFalse($container->hasDefinition('eight_points_guzzle.middleware.log.test_api'));
        $this->assertFalse($container->hasDefinition('eight_points_guzzle.middleware.log.test_api_with_custom_class'));

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

    /**
     * @return ContainerBuilder
     */
    private function createContainer() : ContainerBuilder
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.debug', true);
        $container->set('event_dispatcher', $this->createMock(EventDispatcherInterface::class));

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
     * @param ContainerBuilder $container
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
