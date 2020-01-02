<?php

namespace EightPoints\Bundle\GuzzleBundle\Tests\DependencyInjection;

use EightPoints\Bundle\GuzzleBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

class ConfigurationTest extends TestCase
{
    public function testSingleClientConfigWithOptions()
    {
        $config = [
            'eight_points_guzzle' => [
                'clients' => [
                    'test_client' => [
                        'base_url' => 'http://baseurl/path',
                        'lazy' => false,
                        'handler' => null,
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
                            'form_params' => [],
                            'multipart' => [],
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
                            'read_timeout' => 30,
                            'verify' => true,
                            'proxy' => [
                                'http' => 'http://proxy.org',
                                'https' => 'https://proxy.org',
                                'no' => ['host.com', 'host.org']
                            ],
                            'version' => '1.1',
                        ],
                        'plugin' => [],
						'class' => '%eight_points_guzzle_bundle.http_client.class%',
                    ]
                ]
            ]
        ];

        $processor = new Processor();
        $processedConfig = $processor->processConfiguration(new Configuration('eight_points_guzzle'), $config);

        $this->assertEquals(array_merge_recursive(
            $config['eight_points_guzzle'],
            [
                'logging' => false,
                'profiling' => false,
                'slow_response_time' => 0,
                'clients' => [
                    'test_client' => [
                        'logging' => null,
                    ]
                ],
            ]
        ), $processedConfig);
    }

    public function testSingleClientConfigWithCertAsArray()
    {
        $config = [
            'eight_points_guzzle' => [
                'clients' => [
                    'test_client' => [
                        'base_url' => 'http://baseurl/path',
                        'lazy' => false,
                        'handler' => null,
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
                            'form_params' => [],
                            'multipart' => [],
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
                            'read_timeout' => 30,
                            'verify' => true,
                            'version' => '1.1',
                        ],
                        'plugin' => [],
						'class' => '%eight_points_guzzle_bundle.http_client.class%',
                    ]
                ]
            ]
        ];

        $processor = new Processor();
        $processedConfig = $processor->processConfiguration(new Configuration('eight_points_guzzle'), $config);

        $this->assertEquals(array_merge_recursive(
            $config['eight_points_guzzle'],
            [
                'logging' => false,
                'profiling' => false,
                'slow_response_time' => 0,
                'clients' => [
                    'test_client' => [
                        'logging' => null,
                    ]
                ],
            ]
        ), $processedConfig);
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
                        'lazy' => false,
                        'handler' => null,
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
                            'proxy' => 'http://proxy.org',
                            'form_params' => [],
                            'multipart' => [],
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
            'profiling' => false,
            'slow_response_time' => 0,
            'clients' => ['test_client' => ['logging' => null, 'options' => ['proxy' => ['http' => 'http://proxy.org']]]]
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
        $this->assertEquals($curlConfig[CURLOPT_SSLVERSION], CURL_HTTP_VERSION_1_1);
    }

    public function testInvalidCustomHandlerOption()
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('handler must be a valid FQCN for a loaded class');

        $config = [
            'eight_points_guzzle' => [
                'clients' => [
                    'test_client' => [
                        'handler' => 'GuzzleHttp\Handler\TestHandler',
                    ],
                ],
            ],
        ];

        $processor = new Processor();
        $processor->processConfiguration(new Configuration('eight_points_guzzle'), $config);
    }

    public function testCustomHandlerOption()
    {
        $config = [
            'eight_points_guzzle' => [
                'clients' => [
                    'test_client' => [
                        'handler' => 'GuzzleHttp\Handler\MockHandler',
                    ],
                ],
            ],
        ];

        $processor = new Processor();
        $processedConfig = $processor->processConfiguration(new Configuration('eight_points_guzzle'), $config);

        $this->assertTrue(isset($processedConfig['clients']['test_client']['handler']));
        $this->assertEquals('GuzzleHttp\Handler\MockHandler', $processedConfig['clients']['test_client']['handler']);
    }

    /**
     * @dataProvider provideValidOptionValues
     *
     * @param array $options
     * @param null|array $expects
     */
    public function testValidOptions(array $options, array $expects = null)
    {
        $config = [
            'eight_points_guzzle' => [
                'clients' => [
                    'test_client' => [
                        'options' => $options
                    ]
                ]
            ]
        ];

        $processor = new Processor();
        $processedConfig = $processor->processConfiguration(new Configuration('eight_points_guzzle'), $config);

        foreach ($options as $key => $value) {
            $this->assertArrayHasKey($key, $processedConfig['clients']['test_client']['options']);
            $this->assertEquals($expects !== null ? $expects[$key] : $value, $processedConfig['clients']['test_client']['options'][$key]);
        }
    }

    public function testSlowRequestTimeout()
    {
        $config = [
            'eight_points_guzzle' => [
                'slow_response_time' => 1000,
                'clients' => []
            ]
        ];

        $processor = new Processor();
        $processedConfig = $processor->processConfiguration(new Configuration('eight_points_guzzle'), $config);

        $this->assertEquals(1000, $processedConfig['slow_response_time']);
    }

    /**
     * @return array
     */
    public function provideValidOptionValues() : array
    {
        return [
            'allow_redirects is bool' => [[
                'allow_redirects' => true,
            ]],
            'allow_redirects is array' => [[
                'allow_redirects' => [
                    'max'  => 5,
                ],
            ]],
            'auth is string' => [[
                'auth' => 'oauth',
            ]],
            'auth is array' => [[
                'auth' => ['acme', 'pa55w0rd'],
            ]],
            'query is string' => [[
                'query' => 'abc=123',
            ]],
            'query is array' => [[
                'query' => ['foo' => 'bar'],
            ]],
            'cert is string' => [[
                'cert' => '/path/server.pem',
            ]],
            'cert is array' => [[
                'cert' => ['/path/server.pem', 'password'],
            ]],
            'connect_timeout is float' => [[
                'connect_timeout' => 3.14,
            ]],
            'connect_timeout is string' => [[
                'connect_timeout' => '3.14',
            ]],
            'connect_timeout is pure environment variable' => [[
                'connect_timeout' => 'env_CONNECT_TIMEOUT_62364f6ed52bf3207e119ceff9760661',
            ]],
            'connect_timeout is type-casted environment variable' => [[
                'connect_timeout' => 'env_float_CONNECT_TIMEOUT_62364f6ed52bf3207e119ceff9760661',
            ]],
            'decode_content is boolean' => [[
                'decode_content' => true,
            ]],
            'decode_content is string' => [[
                'decode_content' => 'gzip',
            ]],
            'delay is float' => [[
                'delay' => 3.14,
            ]],
            'form_params is array' => [[
                'form_params' => [
                    'foo' => 'bar',
                    'baz' => ['hi', 'there!']
                ],
            ]],
            'multipart is array' => [[
                'multipart' => [[
                    'name'     => 'foo',
                    'contents' => 'data',
                    'headers'  => ['X-Baz' => 'bar']
                ]],
            ]],
            'sink is string' => [[
                'sink' => '/path/to/file',
            ]],
            'http_errors is bool' => [[
                'http_errors' => false,
            ]],
            'ssl_key is string' => [[
                'cert' => '/path/server.pem',
            ]],
            'ssl_key is array' => [[
                'cert' => ['/path/server.pem', 'password'],
            ]],
            'stream is bool' => [[
                'stream' => true,
            ]],
            'synchronous is bool' => [[
                'synchronous' => true,
            ]],
            'timeout is float' => [[
                'timeout' => 3.14,
            ]],
            'timeout is string' => [[
                'timeout' => '3.14',
            ]],
            'timeout is pure environment variable' => [[
                'timeout' => 'env_TIMEOUT_62364f6ed52bf3207e119ceff9760661',
            ]],
            'timeout is type-casted environment variable' => [[
                'timeout' => 'env_float_TIMEOUT_62364f6ed52bf3207e119ceff9760661',
            ]],
            'read_timeout is float' => [[
                'read_timeout' => 3.14,
            ]],
            'read_timeout is string' => [[
                'read_timeout' => '3.14',
            ]],
            'read_timeout is pure environment variable' => [[
                'read_timeout' => 'env_READ_TIMEOUT_62364f6ed52bf3207e119ceff9760661',
            ]],
            'read_timeout is type-casted environment variable' => [[
                'connect_timeout' => 'env_float_READ_TIMEOUT_62364f6ed52bf3207e119ceff9760661',
            ]],
            'verify is bool' => [[
                'verify' => true,
            ]],
            'verify is string' => [[
                'verify' => '/path/to/cert.pem',
            ]],
            'cookies is bool' => [[
                'cookies' => true,
            ]],
            'proxy is string' => [
                'options' => ['proxy' => 'tcp://localhost:8125'],
                'expected result' => ['proxy' => ['http' => 'tcp://localhost:8125']],
            ],
            'proxy is array' => [[
                'proxy' => [
                    'http'  => 'tcp://localhost:8125',
                    'no' => ['.mit.edu', 'foo.com'],
                ],
            ]],
            'version is string' => [[
                'version' => '1.1',
            ]],
            'version is float' => [[
                'version' => 1.1,
            ]],
        ];
    }

    /**
     * @dataProvider provideInvalidOptionValues
     *
     * @param array $options
     * @param string $exceptionMessage
     */
    public function testInvalidOptions(array $options, string $exceptionMessage)
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage($exceptionMessage);

        $config = [
            'eight_points_guzzle' => [
                'clients' => [
                    'test_client' => [
                        'options' => $options
                    ]
                ]
            ]
        ];

        $processor = new Processor();
        $processor->processConfiguration(new Configuration('eight_points_guzzle'), $config);
    }

    /**
     * @return array
     */
    public function provideInvalidOptionValues() : array
    {
        return [
            'form_params and multipart at the same time' => [
                'options' => [
                    'form_params' => ['foo' => 'bar'],
                    'multipart' => ['baz' => 'bar'],
                ],
                'exception message' => 'You cannot use form_params and multipart at the same time.',
            ],
            'invalid curl option' => [
                'options' => [
                    'curl' => [
                        'invalid_option' => true,
                    ],
                ],
                'exception message' => 'Invalid curl option',
            ],
            'cert as array with one value' => [
                'options' => [
                    'cert' => ['/path/to/cert.pem'],
                ],
                'exception message' => 'cert can be: string or array with two entries',
            ],
            'ssl_key as array with one value' => [
                'options' => [
                    'ssl_key' => ['/path/to/cert.pem'],
                ],
                'exception message' => 'ssl_key can be: string or array with two entries',
            ],
        ];
    }
}
