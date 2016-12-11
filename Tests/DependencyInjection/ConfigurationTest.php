<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\DependencyInjection;

use EightPoints\Bundle\GuzzleBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

/**
 * @version   2.1
 * @since     2015-05
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testSingleClientConfigWithOptions()
    {
        $config = [
            'guzzle' => [
                'clients' => [
                    'test_client' => [
                        'base_url' => 'http://baseurl/path',
                        'headers' => [
                            'Accept' => 'application/json'
                        ],
                        'options' => [
                            'auth' => [
                                'user',
                                'pass'
                            ],
                            'headers' => [
                                'Accept' => 'application/json'
                            ],
                            'query' => [
                            ],
                            'cert' => 'path/to/cert',
                            'connect_timeout' => 5,
                            'debug' => false,
                            'decode_content' => true,
                            'delay' => 1,
                            'http_errors' => false,
                            'expect' => true,
                            'ssl_key' => 'key',
                            'stream' => true,
                            'synchronous' => true,
                            'timeout' => 30,
                            'verify' => true,
                            'version' => '1.1'
                        ],
                        'plugin' => [
                            'wsse' => [
                                'username' => 'user',
                                'password' => 'pass',
                                'created_at' => false
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $processor = new Processor();
        $processedConfig = $processor->processConfiguration(new Configuration(true), $config);

        $this->assertEquals(array_merge($config['guzzle'], ['logging' => false]), $processedConfig);
    }
}
