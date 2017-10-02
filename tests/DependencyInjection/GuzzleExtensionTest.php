<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\DependencyInjection;

use EightPoints\Bundle\GuzzleBundle\DependencyInjection\Configuration;
use EightPoints\Bundle\GuzzleBundle\DependencyInjection\EightPointsGuzzleExtension;
use EightPoints\Bundle\GuzzleBundle\Tests\DependencyInjection\Fixtures\FakeClient;
use EightPoints\Bundle\GuzzleBundle\Tests\DependencyInjection\Fixtures\FakeWsseAuthMiddleware;
use EightPoints\Guzzle\WsseAuthMiddleware;
use GuzzleHttp\Psr7\Uri;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @version 2.1
 * @since   2015-05
 */
class GuzzleExtensionTest extends TestCase
{
    public function testGuzzleExtension()
    {
        $container = $this->createContainer();
        $extension = new EightPointsGuzzleExtension();
        $extension->load($this->getConfigs(), $container);

        // test Client
        $this->assertTrue($container->hasDefinition('eight_points_guzzle.client.test_api'));
        $testApi = $container->get('eight_points_guzzle.client.test_api');
        $this->assertInstanceOf('GuzzleHttp\Client', $testApi);
        $this->assertEquals(new Uri('//api.domain.tld/path'), $testApi->getConfig('base_uri'));

        // test Services
        $this->assertTrue($container->hasDefinition('eight_points_guzzle.middleware.log.test_api'));
        $this->assertTrue($container->hasDefinition('eight_points_guzzle.middleware.event_dispatch.test_api'));

        // test WSSE Plugin
        $this->assertTrue($container->hasDefinition('eight_points_guzzle.middleware.wsse.test_api'));
        $wsse = $container->get('eight_points_guzzle.middleware.wsse.test_api');
        $this->assertInstanceOf(WsseAuthMiddleware::class, $wsse);
        $this->assertSame('my-user', $wsse->getUsername());
        $this->assertSame('my-pass', $wsse->getPassword());

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
        $container->setParameter('eight_points_guzzle.middleware.wsse.class', FakeWsseAuthMiddleware::class);

        $client = $container->get('eight_points_guzzle.client.test_api', FakeClient::class);
        $this->assertInstanceOf(FakeClient::class, $client);

        $wsse = $container->get('eight_points_guzzle.middleware.wsse.test_api');
        $this->assertInstanceOf(FakeWsseAuthMiddleware::class, $wsse);
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
    private function createContainer()
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.debug', true);
        $container->set('event_dispatcher', $this->createMock(EventDispatcherInterface::class));

        return $container;
    }

    /**
     * @return array
     */
    private function getConfigs()
    {
        return [
            [
                'clients' => [
                    'test_api' => [
                        'base_url' => '//api.domain.tld/path',
                        'plugin' => [
                            'wsse' => [
                                'username' => 'my-user',
                                'password' => 'my-pass',
                            ],
                        ],
                    ],
                    'test_api_with_custom_class' => [
                        'class' => 'CustomGuzzleClass',
                    ],
                ],
            ],
        ];
    }
}
