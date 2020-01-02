# Configuration Reference

##### Minimal Configuration

```yaml
eight_points_guzzle:
    clients:
        api_payment: ~
```

##### Full Configuration

```yaml
eight_points_guzzle:
    # (de)activate logging; default: %kernel.debug%
    logging: true

    # (de)activate profiler; default: %kernel.debug%
    profiling: true

    # configure when a response is considered to be slow (in ms); default 0 (disabled)
    slow_response_time: 500

    clients:
        api_payment:
            base_url: "http://api.domain.tld"

            # Read more here: https://github.com/8p/EightPointsGuzzleBundle/blob/master/src/Resources/doc/redefine-client-class.md
            class: 'Namespace\Of\Your\Client\AwesomeClient'

            # NOTE: This option makes Guzzle Client as lazy (https://symfony.com/doc/master/service_container/lazy_services.html)
            lazy: true # Default `false`

            # Allows to configure logging mode on a specific client
            logging: null # Default null, possible values: null (global settings), true, false, request, request_and_response_headers

            # Handler class to be used for the client
            handler: 'GuzzleHttp\Handler\MockHandler'

            # guzzle client options (full description here: https://guzzle.readthedocs.org/en/latest/request-options.html)
            options:
                auth:
                    - acme     # login
                    - pa55w0rd # password

                headers:
                    Accept: "application/json"

                # Find proper php const, for example CURLOPT_SSLVERSION, remove CURLOPT_ and transform to lower case.
                # List of curl options: http://php.net/manual/en/function.curl-setopt.php
                curl:
                    sslversion: 1 # or !php/const:CURL_HTTP_VERSION_1_0 for symfony >= 3.2

                timeout: 30

                connect_timeout: 3.14

                allow_redirects: true

                query:
                    foo: bar

                debug: true

                decode_content: true

                delay: 1000

                http_errors: true

                synchronous: false

                verify: true

                version: 1.1

                cert: ['/path/server.pem', 'password']

                form_params:
                    foo: 'bar'
                    baz: ['hi', 'there!']

                multipart:
                    - name: 'foo'
                      contents: 'data'
                      headers:
                        'X-Baz' => 'bar'

                sink: '/path/to/file'

                expect: 1048576

                ssl_key: '/path/to/file'

                stream: false

                cookies: '/path/to/file'

                proxy: 'tcp://localhost:8125'

            # plugin settings
            plugin:
                # More information: https://packagist.org/packages/gregurco/guzzle-bundle-oauth2-plugin
                oauth2:
                    base_uri:       "https://example.com"
                    token_url:      "/oauth/token"
                    client_id:      "test-client-id"
                    client_secret:  "test-client-secret" # optional
                    scope:          "administration"

                # More information: https://packagist.org/packages/gregurco/guzzle-bundle-cache-plugin
                cache:
                    enabled: true

                # More information: https://packagist.org/packages/gregurco/guzzle-bundle-wsse-plugin
                wsse:
                    username:   "acme"
                    password:   "pa55w0rd"
                    created_at: "-10 seconds" # optional

                # More information: https://packagist.org/packages/neirda24/guzzle-bundle-header-forward-plugin
                header_forward:
                    enabled: true
                    headers:
                        - 'Accept-Language'

                # More information: https://packagist.org/packages/neirda24/guzzle-bundle-header-disable-cache-plugin
                header_disable_cache:
                    enabled: true
                    header: 'X-Guzzle-Skip-Cache' # Optional
```

Description for all options and examples of parameters can be found [here][1].



[1]: http://docs.guzzlephp.org/en/latest/request-options.html
