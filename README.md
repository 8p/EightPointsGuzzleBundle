Symfony GuzzleBundle 
====================
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/5cf56080-9357-49a3-83b2-a3dd20a8a590/big.png)](https://insight.sensiolabs.com/projects/5cf56080-9357-49a3-83b2-a3dd20a8a590)
[![knpbundles.com](http://knpbundles.com/8p/GuzzleBundle/badge)](http://knpbundles.com/8p/GuzzleBundle)

This plugin integrates [Guzzle][1] into Symfony. Guzzle is a PHP framework for building RESTful web service clients.
It comes with a WSSE Auth Plugin that can be used optional.


Requirements
------------
 - PHP 5.3.2 or above (at least 5.3.4 recommended to avoid potential bugs)
 - Guzzle PHP Framework
 - [WSSE Auth Plugin][2]

 
Installation
------------
Using composer:

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
    base_url:  "http://api.domain.tld"
    plugin:
       wsse:
           username: acme
           password: pa55w0rd
```
All these settings are optional. If WSSE username is defined the plugin will be injected automatically.

Using services in controller:
``` php
$client   = $this->get('guzzle.client');
$response = $client->get('/users')->send();
```


Authors
-------
Florian Preusner - <florian.preusner@8points.de> - <http://twitter.com/floeH> - <http://floeh.com><br />

See also the list of [contributors][3] who participated in this project.


License
-------
This plugin is licensed under the MIT License - see the LICENSE file for details


[1]: http://guzzlephp.org/
[2]: https://github.com/8p/guzzle-wsse-plugin