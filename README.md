**[Requirements](#requirements)** |
**[Installation](#installation)** |
**[Usage](#usage)** |
**[Events](#events)** |
**[Features](#features)** |
**[Suggestions](#suggestions)** |
**[Contributing](#contributing)** |
**[License](#license)**

# Symfony GuzzleBundle

[![Latest Stable Version](https://poser.pugx.org/eightpoints/guzzle-bundle/v/stable.png)](https://packagist.org/packages/eightpoints/guzzle-bundle)
[![Total Downloads](https://poser.pugx.org/eightpoints/guzzle-bundle/downloads.png)](https://packagist.org/packages/eightpoints/guzzle-bundle)
[![Build Status](https://travis-ci.org/8p/GuzzleBundle.svg)](https://travis-ci.org/8p/GuzzleBundle)
[![Dependency Status](https://www.versioneye.com/user/projects/57c83100968d640039516d62/badge.svg?style=flat-square)](https://www.versioneye.com/user/projects/57c83100968d640039516d62)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/39a6e10b-ce29-44f6-97ce-44b2ff230424/mini.png)](https://insight.sensiolabs.com/projects/39a6e10b-ce29-44f6-97ce-44b2ff230424)


This bundle integrates [Guzzle 6.x][1] into Symfony. Guzzle is a PHP framework for building RESTful web service clients.
It comes with a WSSE Auth Plugin that can be used optionally.

GuzzleBundle follows semantic versioning. Read more on [semver.org][2].

----

## Requirements
 - PHP 5.6 or above
 - Symfony 2.7 or above
 - [Guzzle PHP Framework][1] (included by composer)
 - [WSSE Auth Plugin][3] (included by composer)

----

## Installation
To install this bundle, run the command below and you will get the latest version by [Packagist][4].

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
new EightPoints\Bundle\GuzzleBundle\GuzzleBundle()
```

Configuration in config.yml:
``` yaml
guzzle:
    # (de)activate logging/profiler; default: %kernel.debug%
    logging: true

    clients:
        api_payment:
            base_url: "http://api.domain.tld"

            # custom headers (@deprecated, will be removed in v6; new: "headers" in options (see below))
            headers:
                Accept: "application/json"

            # guzzle client options (full description here: https://guzzle.readthedocs.org/en/latest/request-options.html)
            # NOTE: "headers" option is not accepted here as it is provided as described above.
            options:
                auth:
                    - acme     # login
                    - pa55w0rd # password

                headers:
                    Accept: "application/json"

                timeout: 30

            # plugin settings
            plugin:
                wsse:
                    username:   "acme"
                    password:   "pa55w0rd"
                    created_at: "-10 seconds" # optional

        api_crm:
            base_url: "http://api.crm.tld"
            headers:
                Accept: "application/json"

        ...
```
All these settings are optional. If WSSE username is defined the WSSE plugin will be injected automatically.

Using services in controller (guzzle.client.**api_crm** represents the client name of the yaml config and is an instance of GuzzleHttp\Client):
``` php
/** @var \GuzzleHttp\Client $client */
$client   = $this->get('guzzle.client.api_crm');
$response = $client->get('/users');
```

----

## Events
Handling events.  Events are dispatched before and after the request to the remote host.
### Listening To Events
```xml
    <service id="listenerID" class="Your\ListenerClass\That\Implements\GuzzleEventListenerInterface">  
        <tag name="kernel.event_listener" event="guzzle_bundle.pre_transaction" method="onPreTransaction" service="servicename"/>  
    </service>  
```

Your event Listener, or Subscriber **MUST** implement GuzzleBundle\Events\GuzzleEventListenerInterface.  
Events dispatched are guzzle_bundle.pre_transaction, guzzle_bundle.post_transaction.  
The service on the tag, is so that if you have multiple REST endpoints you can define which service a particular listener is interested in.

----

## Features
### Symfony Debug Toolbar / Profiler
<img src="/Resources/doc/img/debug_logs.png" alt="Debug Logs" title="Symfony Debug Toolbar - Guzzle Logs" style="width: 360px" />

----

## Suggestions
Adding aliases:
If you want to use different names for provided services you can use aliases. This is a good idea if you don't want
have any dependency to guzzle in your service name.
``` yaml
services:
   crm.client:
       alias: guzzle.client.api_crm
```

----

## Contributing
👍 If you would like to contribute to the project, please read the [CONTRIBUTING.md](CONTRIBUTING.md).

🎉 Thanks to the [contributors][5] who participated in this project.

----

## License
This bundle is released under the [MIT license](Resources/meta/LICENSE)


[1]: http://guzzlephp.org/
[2]: http://semver.org/
[3]: https://github.com/8p/guzzle-wsse-plugin
[4]: https://packagist.org/packages/eightpoints/guzzle-bundle
[5]: https://github.com/8p/GuzzleBundle/graphs/contributors
