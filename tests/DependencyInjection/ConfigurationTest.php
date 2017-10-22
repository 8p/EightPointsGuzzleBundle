<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\DependencyInjection;

use EightPoints\Bundle\GuzzleBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * @version   2.1
 * @since     2015-05
 */
class ConfigurationTest extends TestCase
{
    public function testSingleClientConfigWithOptions()
    {
        $config = [
            'eight_points_guzzle' => [
                'clients' => [
                    'test_client' => [
                        'base_url' => 'http://baseurl/path',
                        'options' => [
                            'auth' => [
                                'user',
                                'pass'
                            ],
                            'headers' => [
                                'Accept' => 'application/json'
                            ],
                            'query' => [],
                            'curl' => [],
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
                        'plugin' => [],
						'class' => '%eight_points_guzzle_bundle.http_client.class%',
                    ]
                ]
            ]
        ];

        $processor = new Processor();
        $processedConfig = $processor->processConfiguration(new Configuration('eight_points_guzzle'), $config);

        $this->assertEquals(array_merge($config['eight_points_guzzle'], ['logging' => false]), $processedConfig);
    }

    public function testSingleClientConfigWithCertAsArray()
    {
        $config = [
            'eight_points_guzzle' => [
                'clients' => [
                    'test_client' => [
                        'base_url' => 'http://baseurl/path',
                        'options' => [
                            'auth' => [
                                'user',
                                'pass'
                            ],
                            'headers' => [
                                'Accept' => 'application/json'
                            ],
                            'query' => [],
                            'curl' => [],
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
                        'plugin' => [],
						'class' => '%eight_points_guzzle_bundle.http_client.class%',
                    ]
                ]
            ]
        ];

        $processor = new Processor();
        $processedConfig = $processor->processConfiguration(new Configuration('eight_points_guzzle'), $config);

        $this->assertEquals(array_merge($config['eight_points_guzzle'], ['logging' => false]), $processedConfig);
    }

    public function testInvalidCertConfiguration()
    {
        $config = [
            'eight_points_guzzle' => [
                'clients' => [
                    'test_client' => [
                        'base_url' => 'http://baseurl/path',
                        'options' => [
                            'headers' => [
                                'Accept' => 'application/json'
                            ],
                            'cert' => [
                                'path/to/cert',
                                'password',
                                'Invalid'
                            ],
                            'curl' => [],
                        ],
                    ]
                ]
            ]
        ];

        $this->expectException(InvalidConfigurationException::class);

        $processor = new Processor();
        $processor->processConfiguration(new Configuration('eight_points_guzzle'), $config);
    }

    public function testSingleClientConfigWithProxyAsString()
    {
        $config = [
            'eight_points_guzzle' => [
                'clients' => [
                    'test_client' => [
                        'base_url' => 'http://baseurl/path',
                        'options' => [
                            'auth' => [
                                'user',
                                'pass'
                            ],
                            'headers' => [
                                'Accept' => 'application/json'
                            ],
                            'query' => [],
                            'curl' => [],
                            'proxy' => 'http://proxy.org'
                        ],
                        'plugin' => [],
						'class' => '%eight_points_guzzle_bundle.http_client.class%',
                    ]
                ]
            ]
        ];

        $processor = new Processor();
        $processedConfig = $processor->processConfiguration(new Configuration('eight_points_guzzle'), $config);

        unset($config['eight_points_guzzle']['clients']['test_client']['options']['proxy']);

        $this->assertEquals(array_merge_recursive($config['eight_points_guzzle'], [
            'logging' => false,
            'clients' => ['test_client' => ['options' => ['proxy' => ['http' => 'http://proxy.org']]]]
        ]), $processedConfig);
    }

    public function testHeaderWithUnderscore()
    {
        $config = [
            'eight_points_guzzle' => [
                'clients' => [
                    'test_client' => [
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
        $processedConfig = $processor->processConfiguration(new Configuration('eight_points_guzzle'), $config);

        $headers = $processedConfig['clients']['test_client']['options']['headers'];

        $this->assertArrayHasKey('Header_underscored', $headers);
        $this->assertArrayHasKey('Header-hyphened', $headers);
    }

    public function testCurlOption()
    {
        $config = [
            'eight_points_guzzle' => [
                'clients' => [
                    'test_client' => [
                        'options' => [
                            'curl' => [
                                'sslversion' => CURL_HTTP_VERSION_1_1,
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $processor = new Processor();
        $processedConfig = $processor->processConfiguration(new Configuration('eight_points_guzzle'), $config);

        $this->assertTrue(isset($processedConfig['clients']['test_client']['options']['curl']));

        $curlConfig = $processedConfig['clients']['test_client']['options']['curl'];
        $this->assertCount(1, $curlConfig);
        $this->assertArrayHasKey(CURLOPT_SSLVERSION, $curlConfig);
        $this->assertEquals($curlConfig[CURLOPT_SSLVERSION], CURL_HTTP_VERSION_1_0);
    }

    public function testInvalidCurlOption()
    {
        $this->expectException(InvalidConfigurationException::class);

        $config = [
            'eight_points_guzzle' => [
                'clients' => [
                    'test_client' => [
                        'options' => [
                            'curl' => [
                                'invalid_option' => true,
                            ]
                        ]
                    ]
                ]
            ]
        ];

        $processor = new Processor();
        $processor->processConfiguration(new Configuration('eight_points_guzzle'), $config);
    }
}
