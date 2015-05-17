# Symfony GuzzleBundle [![Latest Stable Version](https://poser.pugx.org/eightpoints/guzzle-bundle/v/stable.png)](https://packagist.org/packages/eightpoints/guzzle-bundle) [![Total Downloads](https://poser.pugx.org/eightpoints/guzzle-bundle/downloads.png)](https://packagist.org/packages/eightpoints/guzzle-bundle) [![License](https://poser.pugx.org/eightpoints/guzzle-bundle/license.svg)](https://packagist.org/packages/eightpoints/guzzle-bundle)
[![knpbundles.com](http://knpbundles.com/8p/GuzzleBundle/badge)](http://knpbundles.com/8p/GuzzleBundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/5cf56080-9357-49a3-83b2-a3dd20a8a590/big.png)](https://insight.sensiolabs.com/projects/5cf56080-9357-49a3-83b2-a3dd20a8a590)

This bundle integrates [Guzzle 5.x][1] into Symfony. Guzzle is a PHP framework for building RESTful web service clients.
It comes with a WSSE Auth Plugin that can be used optionally.

GuzzleBundle follows semantic versioning. Read more on [semver.org][2].

## Requirements
 - PHP 5.4 or above
 - [Guzzle PHP Framework][1] (included by composer)
 - [WSSE Auth Plugin][3] (included by composer)

 
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


## Usage
Load bundle in AppKernel.php:
``` php
new EightPoints\Bundle\GuzzleBundle\GuzzleBundle()
```

Configuration in config.yml:
``` yaml
guzzle:
    base_url: "http://api.domain.tld"

    # custom headers
    headers:
        Accept: "application/json"

    # plugin settings
    plugin:
       wsse:
           username: acme
           password: pa55w0rd
```
All these settings are optional. If WSSE username is defined the WSSE plugin will be injected automatically.

Using services in controller:
``` php
$client   = $this->get('guzzle.client');
$response = $client->get('/users');
```


## Features
### Symfony Debug Toolbar / Profiler
<img src="/Resources/doc/img/debug_logs.png" alt="Debug Logs" title="Symfony Debug Toolbar - Guzzle Logs" style="width: 360px" />


## Suggestions
Adding aliases:
If you want to use different names for provided services you can use aliases. This is a good idea if you don't want 
have any dependency to guzzle in your service name.
``` yaml
services:
   http.client:
       alias: guzzle.client
```


## Authors
 - Florian Preusner ([Twitter][5])

See also the list of [contributors][6] who participated in this project.


## License
This bundle is released under the [MIT license](Resources/meta/LICENSE)


[1]: http://guzzlephp.org/
[2]: http://semver.org/
[3]: https://github.com/8p/guzzle-wsse-plugin
[4]: https://packagist.org/packages/eightpoints/guzzle-bundle
[5]: http://twitter.com/floeH
[6]: https://github.com/8p/GuzzleBundle/graphs/contributors
