**[Requirements](#requirements)** |
**[Installation](#installation)** |
**[Usage](#usage)** |
**[Plugins](#plugins)** |
**[Events](#events)** |
**[Features](#features)** |
**[Suggestions](#suggestions)** |
**[Contributing](#contributing)** |
**[License](#license)**

# Symfony GuzzleBundle

[![Total Downloads](https://poser.pugx.org/eightpoints/guzzle-bundle/downloads.png)](https://packagist.org/packages/eightpoints/guzzle-bundle)
[![Monthly Downloads](https://poser.pugx.org/eightpoints/guzzle-bundle/d/monthly.png)](https://packagist.org/packages/eightpoints/guzzle-bundle)
[![Latest Stable Version](https://poser.pugx.org/eightpoints/guzzle-bundle/v/stable.png)](https://packagist.org/packages/eightpoints/guzzle-bundle)
[![Build Status](https://travis-ci.org/8p/EightPointsGuzzleBundle.svg)](https://travis-ci.org/8p/EightPointsGuzzleBundle)
[![Scrutinizer Score](https://scrutinizer-ci.com/g/8p/EightPointsGuzzleBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/8p/EightPointsGuzzleBundle/)
[![License](https://poser.pugx.org/eightpoints/guzzle-bundle/license)](https://packagist.org/packages/eightpoints/guzzle-bundle)
[![SymfonyInsight](https://insight.symfony.com/projects/72b7a8fd-3e23-47e9-8bcf-101da4190e6f/mini.svg)](https://insight.symfony.com/projects/72b7a8fd-3e23-47e9-8bcf-101da4190e6f)

This bundle integrates [Guzzle 6.x][1] into Symfony. Guzzle is a PHP framework for building RESTful web service clients.

GuzzleBundle follows semantic versioning. Read more on [semver.org][2].

----

## Requirements
 - PHP 7.0 or above
 - Symfony 2.7 or above
 - [Guzzle PHP Framework][1] (included by composer)

----

## Installation

##### Command line:
To install this bundle, run the command below and you will get the latest version by [Packagist][3].

``` bash
composer require eightpoints/guzzle-bundle
```

##### composer.json
To use the newest (maybe unstable) version please add following into your composer.json:

``` json
{
    "require": {
        "eightpoints/guzzle-bundle": "dev-master"
    }
}
```

_Note: we created [Symfony Flex Recipe][14] to speed up the installation of package._

----

## Usage

##### Load bundle in AppKernel.php:

*Skip it for Symfony >= 4.0*

``` php
new EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundle()
```

##### Configuration in config.yml:
``` yaml
eight_points_guzzle:
    # (de)activate logging/profiler; default: %kernel.debug%
    logging: true

    # configure when a response is considered to be slow (in ms); default 0 (disabled)
    slow_response_time: 1000

    clients:
        api_payment:
            base_url: "http://api.domain.tld"

            # NOTE: This option makes Guzzle Client as lazy (https://symfony.com/doc/master/service_container/lazy_services.html)
            lazy: true # Default `false`

            # Handler class to be used for the client
            handler: 'GuzzleHttp\Handler\MockHandler'

            # guzzle client options (full description here: https://guzzle.readthedocs.org/en/latest/request-options.html)
            # NOTE: "headers" option is not accepted here as it is provided as described above.
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

            # plugin settings
            plugin: ~

        api_crm:
            base_url: "http://api.crm.tld"
            options:            
                headers:
                    Accept: "application/json"

        ...
```

Open [Configuration Reference](src/Resources/doc/configuration-reference.md) page to see the complete list of allowed options.

##### Install assets _(if it's not performed automatically)_:
``` bash
# for symfony >= 3.0
bin/console assets:install

# for symfony < 3.0
app/console assets:install
```

Using services in controller (eight_points_guzzle.client.**api_crm** represents the client name of the yaml config and is an instance of GuzzleHttp\Client):

``` php
/** @var \GuzzleHttp\Client $client */
$client   = $this->get('eight_points_guzzle.client.api_crm');
$response = $client->get('/users');
```

----

## Plugins
This bundle allows to register and integrate plugins to extend functionality of guzzle and this bundle.

### Usage

#### Symfony 2.x and 3.x
All plugins will be activated/connected through bundle constructor in AppKernel, like this:

``` php
new EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundle([
    new Gregurco\Bundle\GuzzleBundleOAuth2Plugin\GuzzleBundleOAuth2Plugin(),
])
```

#### Symfony 4
The registration of bundles was changed in Symfony 4 and now you have to change `src/Kernel.php` to achieve the same functionality.  
Find next lines:

```php
foreach ($contents as $class => $envs) {
    if (isset($envs['all']) || isset($envs[$this->environment])) {
        yield new $class();
    }
}
```

and replace them by:

```php
foreach ($contents as $class => $envs) {
    if (isset($envs['all']) || isset($envs[$this->environment])) {
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

----

## Events
Handling events. Events are dispatched before and after the request to the remote host.

### Listening To Events
```xml
    <service id="listenerID" class="Your\ListenerClass\That\Implements\GuzzleEventListenerInterface">  
        <tag name="kernel.event_listener" event="eight_points_guzzle.pre_transaction" method="onPreTransaction" service="servicename"/>  
    </service>  
```

Your event Listener, or Subscriber **MUST** implement GuzzleBundle\Events\GuzzleEventListenerInterface.  
Events dispatched are eight_points_guzzle.pre_transaction, eight_points_guzzle.post_transaction.  
The service on the tag, is so that if you have multiple REST endpoints you can define which service a particular listener is interested in.

Read more [here](src/Resources/doc/intercept-request-and-response.md).

----

## Features

### Symfony Debug Toolbar / Profiler
<img src="/src/Resources/doc/img/debug_logs.png" alt="Debug Logs" title="Symfony Debug Toolbar - Guzzle Logs" style="width: 360px" />

### Symfony logs
All requests are logged into symfony default logger (`@logger` service) with next format:
```
[{datetime}] eight_points_guzzle.{log_level}: {method} {uri} {code}
```
Example:
```
[2017-12-01 00:00:00] eight_points_guzzle.INFO: GET http://api.domain.tld 200
```

You can change message format by overriding `eight_points_guzzle.symfony_log_formatter.pattern` parameter. See allowed variables [here][8].

----

## Suggestions
Adding aliases:
If you want to use different names for provided services you can use aliases. This is a good idea if you don't want
have any dependency to guzzle in your service name.
``` yaml
services:
   crm.client:
       alias: eight_points_guzzle.client.api_crm
```

Use Guzzle MockHandler in tests :
If you want to mock api calls, you can force the clients to use the Guzzle MockHandler instead of the default one.
``` yaml
eight_points_guzzle:
    clients:
        api_payment:
            base_url: "http://api.domain.tld"
            handler: 'GuzzleHttp\Handler\MockHandler'
```

----

## Contributing
👍 If you would like to contribute to the project, please read the [CONTRIBUTING.md](CONTRIBUTING.md).

<img src="/src/Resources/doc/img/icon_slack.png" alt="Slack" title="Slack" style="width: 23px; margin-right: -4px;" /> Join our Slack channel on [Symfony Devs][9] for discussions, questions and more: [#8p-guzzlebundle][10].

🎉 Thanks to the [contributors][11] who participated in this project.

----

## Learn more
- [Configuration Reference](src/Resources/doc/configuration-reference.md)
- [Environment variables integration](src/Resources/doc/environment-variables-integration.md)
- [How to redefine class used for clients](src/Resources/doc/redefine-client-class.md)
- [Disable throwing exceptions on HTTP errors (4xx and 5xx responses)](src/Resources/doc/disable-exception-on-http-error.md)
- [Intercept request and response](src/Resources/doc/intercept-request-and-response.md)
- [Autowiring Clients](src/Resources/doc/autowiring-clients.md)
- [How to create a single-file plugin](src/Resources/doc/how-to-create-a-single-file-plugin.md)

## License
This bundle is released under the [MIT license](LICENSE)

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
