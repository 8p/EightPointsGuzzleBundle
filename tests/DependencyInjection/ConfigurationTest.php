<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\DependencyInjection;

use EightPoints\Bundle\GuzzleBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

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
                            'proxy' => [
                                'http' => 'http://proxy.org',
                                'https' => 'https://proxy.org',
                                'no' => ['host.com', 'host.org']
                            ],
                            'version' => '1.1'
                        ],
                        'plugin' => [
                            'wsse' => [
                                'username' => 'user',
                                'password' => 'pass',
                                'created_at' => false
                            ]
                        ],
						'class' => '%guzzle.http_client.class%',
                    ]
                ]
            ]
        ];

        $processor = new Processor();
        $processedConfig = $processor->processConfiguration(new Configuration(true), $config);

        $this->assertEquals(array_merge($config['guzzle'], ['logging' => false]), $processedConfig);
    }

    public function testSingleClientConfigWithCertAsArray()
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
                            'cert' => [
                                'path/to/cert',
                                'password'
                            ],
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
                        ],
						'class' => '%guzzle.http_client.class%',
                    ]
                ]
            ]
        ];

        $processor = new Processor();
        $processedConfig = $processor->processConfiguration(new Configuration(true), $config);

        $this->assertEquals(array_merge($config['guzzle'], ['logging' => false]), $processedConfig);
    }

    public function testInvalidCertConfiguration()
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
                            'cert' => [
                                'path/to/cert',
                                'password',
                                'Invalid'
                            ],
                        ],
                    ]
                ]
            ]
        ];

        $this->expectException(InvalidConfigurationException::class);

        $processor = new Processor();
        $processor->processConfiguration(new Configuration(true), $config);
    }

    public function testSingleClientConfigWithProxyAsString()
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
                            'proxy' => 'http://proxy.org'
                        ],
                        'plugin' => [
                            'wsse' => [
                                'username' => 'user',
                                'password' => 'pass',
                                'created_at' => false
                            ]
                        ],
						'class' => '%guzzle.http_client.class%',
                    ]
                ]
            ]
        ];

        $processor = new Processor();
        $processedConfig = $processor->processConfiguration(new Configuration(true), $config);

        unset($config['guzzle']['clients']['test_client']['options']['proxy']);

        $this->assertEquals(array_merge_recursive($config['guzzle'], [
            'logging' => false,
            'clients' => ['test_client' => ['options' => ['proxy' => ['http' => 'http://proxy.org']]]]
        ]), $processedConfig);
    }

    public function testHeaderWithUnderscore()
    {
        $config = [
            'guzzle' => [
                'clients' => [
                    'test_client' => [
                        'headers' => [
                            'Header_underscored' => 'some-random-hash',
                            'Header-hyphened' => 'another-random-hash'
                        ],
                        'options' => [
                            'headers' => [
                                'Header_underscored' => 'some-random-hash',
                                'Header-hyphened' => 'another-random-hash'
                            ],
                        ]
                    ]
                ]
            ]
        ];

        $processor = new Processor();
        $processedConfig = $processor->processConfiguration(new Configuration(true), $config);

        $headers = $processedConfig['clients']['test_client']['headers'];
        $optionsHeaders = $processedConfig['clients']['test_client']['options']['headers'];

        foreach ([$headers, $optionsHeaders] as $headerConfig)
        {
            $this->assertArrayHasKey('Header_underscored', $headerConfig);
            $this->assertArrayHasKey('Header-hyphened', $headerConfig);
        }
    }
}
