<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\DependencyInjection;

use       EightPoints\Bundle\GuzzleBundle\DependencyInjection\Configuration;
use       Symfony\Component\Config\Definition\Processor;

/**
 * Class ConfigurationTest
 *
 * @package   EightPoints\Bundle\GuzzleBundle\Tests\DependencyInjection
 * @author    Florian Preusner
 *
 * @version   2.1
 * @since     2015-05
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase {

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
                                'password' => 'pass'
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $processor = new Processor();
        $processedConfig = $processor->processConfiguration(new Configuration(true), $config);

        $this->assertEquals(array_merge($config['guzzle'], [ 'logging' => false ]), $processedConfig);
    }
} // end: ConfigurationTest
