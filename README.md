Symfony GuzzleBundle [![Latest Stable Version](https://poser.pugx.org/eightpoints/guzzle-bundle/v/stable.png)](https://packagist.org/packages/eightpoints/guzzle-bundle) [![Total Downloads](https://poser.pugx.org/eightpoints/guzzle-bundle/downloads.png)](https://packagist.org/packages/eightpoints/guzzle-bundle)
====================
[![knpbundles.com](http://knpbundles.com/8p/GuzzleBundle/badge)](http://knpbundles.com/8p/GuzzleBundle)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/5cf56080-9357-49a3-83b2-a3dd20a8a590/big.png)](https://insight.sensiolabs.com/projects/5cf56080-9357-49a3-83b2-a3dd20a8a590)

This plugin integrates [Guzzle 5.x][1] into Symfony. Guzzle is a PHP framework for building RESTful web service clients.
It comes with a WSSE Auth Plugin that can be used optionally.

Requirements
------------
 - PHP 5.4 or above
 - Guzzle PHP Framework
 - [WSSE Auth Plugin][2]

 
Installation
------------
To install this bundle, run the command below and you will get the latest version by [Packagist][3].

``` bash
composer require eightpoints/guzzle-bundle
```

To use the newest (maybe unstable) version, please add following into your composer.json:

``` json
{
    "require": {
        "eightpoints/guzzle-bundle": "dev-master"
    }
}
```


Usage
-----
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
$response = $client->get('/users')->send();
```


Authors
-------
 - Florian Preusner ([Twitter][4])

See also the list of [contributors][5] who participated in this project.


License
-------
This bundle is licensed under the MIT License - see the LICENSE file for details


[1]: http://guzzlephp.org/
[2]: https://github.com/8p/guzzle-wsse-plugin
[3]: https://packagist.org/packages/eightpoints/guzzle-bundle
[4]: http://twitter.com/floeH
[5]: https://github.com/8p/GuzzleBundle/graphs/contributors
