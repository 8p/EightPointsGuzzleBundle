**[Prerequisites](#prerequisites)** |
**[Installation](#installation)** |
**[Configuration](#configuration)** |
**[Usage](#usage)** |
**[Plugins](#plugins)** |
**[Events](#events)** |
**[Features](#features)** |
**[Suggestions](#suggestions)** |
**[Contributing](#contributing)** |
**[Learn more](#learn-more)** |
**[License](#license)**

# EightPoints GuzzleBundle for Symfony

[![Total Downloads](https://poser.pugx.org/eightpoints/guzzle-bundle/downloads.png)](https://packagist.org/packages/eightpoints/guzzle-bundle)
[![Monthly Downloads](https://poser.pugx.org/eightpoints/guzzle-bundle/d/monthly.png)](https://packagist.org/packages/eightpoints/guzzle-bundle)
[![Latest Stable Version](https://poser.pugx.org/eightpoints/guzzle-bundle/v/stable.png)](https://packagist.org/packages/eightpoints/guzzle-bundle)
[![Build Status](https://travis-ci.org/8p/EightPointsGuzzleBundle.svg)](https://travis-ci.org/8p/EightPointsGuzzleBundle)
[![Scrutinizer Score](https://scrutinizer-ci.com/g/8p/EightPointsGuzzleBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/8p/EightPointsGuzzleBundle/)
[![License](https://poser.pugx.org/eightpoints/guzzle-bundle/license)](https://packagist.org/packages/eightpoints/guzzle-bundle)

This bundle integrates [Guzzle 6.x|7.x][1] into [Symfony][16]. Guzzle is a PHP library for building RESTful web service clients.

GuzzleBundle follows semantic versioning. Read more on [semver.org][2].

----

## Prerequisites
 - PHP 7.1 or higher
 - Symfony 4.x or 5.x

----

## Installation

### Installing the bundle

To install this bundle, run the command below on the command line and you will get the latest stable version from [Packagist][3].

``` bash
composer require eightpoints/guzzle-bundle
```

_Note: this bundle has a [Symfony Flex Recipe][14] to automatically register and configure this bundle into your symfony application._

If your project does *not* use Symfony Flex the following needs to be added to `config/bundles.php` manually:

```php
<?php

return [
    // other bundles here
    EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundle::class => ['all' => true],
];
```

----

## Configuration

Guzzle clients can be configured in `config/packages/eight_points_guzzle.yaml`. For projects that use Symfony Flex this file is created
automatically upon installation of this bundle. For projects that don't use Symfony Flex this file should be created manually.

```yaml
eight_points_guzzle:
    # (de)activate logging/profiler; default: %kernel.debug%
    logging: true

    # configure when a response is considered to be slow (in ms); default 0 (disabled)
    slow_response_time: 1000

    clients:
        payment:
            base_url: 'http://api.payment.example'

            # NOTE: This option marks this Guzzle Client as lazy (https://symfony.com/doc/master/service_container/lazy_services.html)
            lazy: true # Default `false`

            # guzzle client options (full description here: https://guzzle.readthedocs.org/en/latest/request-options.html)
            options:
                auth:
                    - acme     # login
                    - pa55w0rd # password

                headers:
                    Accept: 'application/json'

                # Find proper php const, for example CURLOPT_SSLVERSION, remove CURLOPT_ and transform to lower case.
                # List of curl options: http://php.net/manual/en/function.curl-setopt.php
                curl:
                    !php/const:CURL_HTTP_VERSION_1_0: 1

                timeout: 30

            # plugin settings
            plugin: ~

        crm:
            base_url: 'http://api.crm.tld'
            options:
                headers:
                    Accept: 'application/json'

        # More clients here
```

Please refer to the [Configuration Reference](src/Resources/doc/configuration-reference.md) for a complete list of all options.

## Usage

Guzzle clients configured through this bundle are available in the Symfony Dependency Injection container under the name
`eight_points_guzzle.client.<name of client>`. So for example a client configured in the configuration with name `payment` is available
as `eight_points_guzzle.client.payment`.

Suppose you have the following controller that requires a Guzzle Client:

```php
<?php

namespace App\Controller;

use Guzzle\Client;

class ExampleController
{
    public function __construct(Client $client)
    {
        $this->client = $client;
    }
}
```

Using manual wiring this controller can be wired as follows:

```yaml
services:
    my.example.controller:
        class: App\Controller\ExampleController
        arguments: ['@eight_points_guzzle.client.payment']
```

For projects that use [autowiring][18], please refer to [our documentation on autowiring](src/Resources/doc/autowiring-clients.md).

----

## Plugins

This bundle allows to register and integrate plugins to extend functionality of guzzle and this bundle.

### Installation

In order to install a plugin, find the following lines in `src/Kernel.php`:

```php
foreach ($contents as $class => $envs) {
    if ($envs[$this->environment] ?? $envs['all'] ?? false) {
        yield new $class();
    }
}
```

and replace them with the following:

```php
foreach ($contents as $class => $envs) {
    if ($envs[$this->environment] ?? $envs['all'] ?? false) {
        if ($class === \EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundle::class) {
            yield new $class([
                new \Gregurco\Bundle\GuzzleBundleOAuth2Plugin\GuzzleBundleOAuth2Plugin(),
            ]);
        } else {
            yield new $class();
        }
    }
}
```

### Known and Supported Plugins
- [gregurco/GuzzleBundleWssePlugin][5]
- [gregurco/GuzzleBundleCachePlugin][6]
- [gregurco/GuzzleBundleOAuth2Plugin][7]
- [neirda24/GuzzleBundleHeaderForwardPlugin][12]
- [neirda24/GuzzleBundleHeaderDisableCachePlugin][13]
- [EugenGanshorn/GuzzleBundleRetryPlugin][15]

----

## Events

This bundle dispatches Symfony events right before a client makes a call and right after a client has made a call.
There are two types of events dispatched every time; a _generic_ event, that is dispatched regardless of which client is doing the request,
and a _client specific_ event, that is dispatched only to listeners specifically subscribed to events from a specific client.
These events can be used to intercept requests to a remote system as well as responses from a remote system.
In case a generic event listener and a client specific event listener both change a request/response, the changes from the client
specific listener override those of the generic listener in case of a collision (both setting the same header for example).

### Listening To Events

In order to listen to these events you should create a listener and register that listener in the Symfony services configuration as usual:

```yaml
services:
    my_guzzle_listener:
        class: App\Service\MyGuzzleBundleListener
        tags:
            # Listen for generic pre transaction event (will receive events for all clients)
            - { name: 'kernel.event_listener', event: 'eight_points_guzzle.pre_transaction', method: 'onPreTransaction' }
            # Listen for client specific pre transaction events (will only receive events for the "payment" client)
            - { name: 'kernel.event_listener', event: 'eight_points_guzzle.pre_transaction.payment', method: 'onPrePaymentTransaction' }

            - # Listen for generic post transaction event (will receive events for all clients)
            - { name: 'kernel.event_listener', event: 'eight_points_guzzle.post_transaction', method: 'onPostTransaction' }
            # Listen for client specific post transaction events (will only receive events for the "payment" client)
            - { name: 'kernel.event_listener', event: 'eight_points_guzzle.post_transaction.payment', method: 'onPostPaymentTransaction' }
```

For more information, read the docs on [intercepting requests and responses](src/Resources/doc/intercept-request-and-response.md).

----

## Features

### Symfony Debug Toolbar / Profiler
<img src="/src/Resources/doc/img/debug_logs.png" alt="Debug Logs" title="Symfony Debug Toolbar - Guzzle Logs" style="width: 360px" />

### Logging

All requests are logged to Symfony's default logger (`@logger` service) with the following (default) format:
```
[{datetime}] eight_points_guzzle.{log_level}: {method} {uri} {code}
```

Example:
```
[2017-12-01 00:00:00] eight_points_guzzle.INFO: GET http://api.domain.tld 200
```

You can change the message format by overriding the `eight_points_guzzle.symfony_log_formatter.pattern` parameter.
For all options please refer to [Guzzle's MessageFormatter][8].

----

## Suggestions

### Create aliases for clients

In case your project uses manual wiring it is recommended to create aliases for the clients created with this bundle to
get easier service names and also to make it easier to switch to other implementations in the future, might the need arise.

``` yaml
services:
   crm.client: '@eight_points_guzzle.client.crm'
```

In case your project uses autowiring this suggestion does not apply.

----

## Contributing
👍 If you would like to contribute to this bundle, please read [CONTRIBUTING.md](CONTRIBUTING.md).

<img src="/src/Resources/doc/img/icon_slack.png" alt="Slack" title="Slack" style="width: 23px; margin-right: -4px;" /> Join our Slack channel on [Symfony Devs][9] for discussions, questions and more: [#8p-guzzlebundle][10].

🎉 Thanks to all [contributors][11] who participated in this project.

----

## Learn more
- [Autowiring Clients](src/Resources/doc/autowiring-clients.md)
- [Configuration Reference](src/Resources/doc/configuration-reference.md)
- [Disable throwing exceptions on HTTP errors (4xx and 5xx responses)](src/Resources/doc/disable-exception-on-http-error.md)
- [Environment variables integration](src/Resources/doc/environment-variables-integration.md)
- [How to create a single-file plugin](src/Resources/doc/how-to-create-a-single-file-plugin.md)
- [How to redefine class used for clients](src/Resources/doc/redefine-client-class.md)
- [Intercept request and response](src/Resources/doc/intercept-request-and-response.md)

----

## License

This bundle is released under the [MIT license](LICENSE).

[1]: http://guzzlephp.org/
[2]: http://semver.org/
[3]: https://packagist.org/packages/eightpoints/guzzle-bundle
[4]: https://github.com/symfony/flex
[5]: https://github.com/gregurco/GuzzleBundleWssePlugin
[6]: https://github.com/gregurco/GuzzleBundleCachePlugin
[7]: https://github.com/gregurco/GuzzleBundleOAuth2Plugin
[8]: https://github.com/guzzle/guzzle/blob/6.3.0/src/MessageFormatter.php#L14
[9]: https://symfony.com/slack-invite
[10]: https://symfony-devs.slack.com/messages/C8LUKU6JD
[11]: https://github.com/8p/EightPointsGuzzleBundle/graphs/contributors
[12]: https://github.com/Neirda24/GuzzleBundleHeaderForwardPlugin
[13]: https://github.com/Neirda24/GuzzleBundleHeaderDisableCachePlugin
[14]: https://github.com/symfony/recipes-contrib/tree/master/eightpoints/guzzle-bundle
[15]: https://github.com/EugenGanshorn/GuzzleBundleRetryPlugin
[16]: https://symfony.com/
[17]: https://github.com/symfony/skeleton
[18]: https://symfony.com/doc/current/service_container/autowiring.html
