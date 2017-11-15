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
[![License](https://poser.pugx.org/eightpoints/guzzle-bundle/license)](https://packagist.org/packages/eightpoints/guzzle-bundle)
[![Dependency Status](https://www.versioneye.com/user/projects/59ebb26715f0d71dd281adb2/badge.svg?style=square)](https://www.versioneye.com/user/projects/59ebb26715f0d71dd281adb2)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/5619922c-2705-40d8-ba68-a8f6ce71b50e/mini.png)](https://insight.sensiolabs.com/projects/5619922c-2705-40d8-ba68-a8f6ce71b50e)



This bundle integrates [Guzzle 6.x][1] into Symfony. Guzzle is a PHP framework for building RESTful web service clients.

GuzzleBundle follows semantic versioning. Read more on [semver.org][2].

----

## Requirements
 - PHP 7.0 or above
 - Symfony 2.7 or above
 - [Guzzle PHP Framework][1] (included by composer)

----

## Installation
To install this bundle, run the command below and you will get the latest version by [Packagist][3].

``` bash
composer require eightpoints/guzzle-bundle
```

To use the newest (maybe unstable) version please add following into your composer.json:

``` json
{
    "require": {
        "eightpoints/guzzle-bundle": "dev-master"
    }
}
```

----

## Usage
Load bundle in AppKernel.php:
``` php
new EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundle()
```

Configuration in config.yml:
``` yaml
eight_points_guzzle:
    # (de)activate logging/profiler; default: %kernel.debug%
    logging: true

    clients:
        api_payment:
            base_url: "http://api.domain.tld"

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
Allowed options: headers, allow_redirects, auth, query, curl, cert, connect_timeout, debug, decode_content, delay, form_params, multipart, sink, http_errors, expect, ssl_key, stream, synchronous, timeout, verify, cookies, proxy, version. All these settings are optional.  
Description for all options and examples of parameters can be found [here][4].

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
All plugins will be activated/connected through bundle constructor in AppKernel, like this:

``` php 
new EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundle([
    new Gregurco\Bundle\GuzzleBundleWssePlugin\GuzzleBundleWssePlugin(),
])
```

### Known and Supported Plugins
- [gregurco/GuzzleBundleWssePlugin][5]
- [gregurco/GuzzleBundleCachePlugin][6]
- [gregurco/GuzzleBundleOAuth2Plugin][7]

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

----

## Features

### Symfony Debug Toolbar / Profiler
<img src="/src/Resources/doc/img/debug_logs.png" alt="Debug Logs" title="Symfony Debug Toolbar - Guzzle Logs" style="width: 360px" />

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

----

## Contributing
👍 If you would like to contribute to the project, please read the [CONTRIBUTING.md](CONTRIBUTING.md).

🎉 Thanks to the [contributors][8] who participated in this project.

----

## License
This bundle is released under the [MIT license](src/Resources/meta/LICENSE)


[1]: http://guzzlephp.org/
[2]: http://semver.org/
[3]: https://packagist.org/packages/eightpoints/guzzle-bundle
[4]: http://docs.guzzlephp.org/en/latest/request-options.html
[5]: https://github.com/gregurco/GuzzleBundleWssePlugin
[6]: https://github.com/gregurco/GuzzleBundleCachePlugin
[7]: https://github.com/gregurco/GuzzleBundleOAuth2Plugin
[8]: https://github.com/8p/EightPointsGuzzleBundle/graphs/contributors
