<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\DependencyInjection;

use EightPoints\Bundle\GuzzleBundle\DependencyInjection\GuzzleExtension;
use EightPoints\Bundle\GuzzleBundle\Tests\DependencyInjection\Fixtures\FakeClient;
use EightPoints\Bundle\GuzzleBundle\Tests\DependencyInjection\Fixtures\FakeWsseAuthMiddleware;
use EightPoints\Guzzle\WsseAuthMiddleware;
use GuzzleHttp\Psr7\Uri;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @version 2.1
 * @since   2015-05
 */
class GuzzleExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testGuzzleExtension()
    {
        $container = $this->createContainer();
        $extension = new GuzzleExtension();
        $extension->load($this->getConfigs(), $container);

        // test Client
        $this->assertTrue($container->hasDefinition('guzzle.client.test_api'));
        $testApi = $container->get('guzzle.client.test_api');
        $this->assertInstanceOf('GuzzleHttp\Client', $testApi);
        $this->assertEquals(new Uri('//api.domain.tld/path'), $testApi->getConfig('base_uri'));

        // test Services
        $this->assertTrue($container->hasDefinition('guzzle_bundle.middleware.log.test_api'));
        $this->assertTrue($container->hasDefinition('guzzle_bundle.middleware.event_dispatch.test_api'));

        // test WSSE Plugin
        $this->assertTrue($container->hasDefinition('guzzle_bundle.middleware.wsse.test_api'));
        $wsse = $container->get('guzzle_bundle.middleware.wsse.test_api');
        $this->assertInstanceOf(WsseAuthMiddleware::class, $wsse);
        $this->assertSame('my-user', $wsse->getUsername());
        $this->assertSame('my-pass', $wsse->getPassword());
    }

    public function testOverwriteClasses()
    {
        $container = $this->createContainer();
        $extension = new GuzzleExtension();
        $extension->load($this->getConfigs(), $container);

        $container->setParameter('guzzle.http_client.class', FakeClient::class);
        $container->setParameter('guzzle_bundle.middleware.wsse.class', FakeWsseAuthMiddleware::class);

        $client = $container->get('guzzle.client.test_api', FakeClient::class);
        $this->assertInstanceOf(FakeClient::class, $client);

        $wsse = $container->get('guzzle_bundle.middleware.wsse.test_api');
        $this->assertInstanceOf(FakeWsseAuthMiddleware::class, $wsse);
    }

    /**
     * @return ContainerBuilder
     */
    private function createContainer()
    {
        $container = new ContainerBuilder();
        $container->setParameter('kernel.debug', true);
        $container->set('event_dispatcher', $this->createMock('Symfony\Component\EventDispatcher\EventDispatcherInterface'));

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
                ],
            ],
        ];
    }
}
