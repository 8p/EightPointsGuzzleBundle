# How to redefine class used for clients

GuzzleBundle is using `GuzzleHttp\Client` class to create clients. In some cases you may need to extend/rewrite it.
First of all you need to create your own class, but don't forget to extend `GuzzleHttp\Client`:

```php

namespace Namespace\Of\Your\Client;

use GuzzleHttp\Client;

class AwesomeClient extends Client
{

}
```

And now we have two possibilities to change the default class:

#### Global

Redefine `eight_points_guzzle.http_client.class` parameter.
For example in `config/services.yaml` for Symfony 4 and in `app/config/parameters.yml` in Symfony 2 and 3:

```yaml
parameters:
    eight_points_guzzle.http_client.class: Namespace\Of\Your\Client\AwesomeClient
```

Note that this method will redefine class for **all** clients.

#### For specific client

```yaml
eight_points_guzzle:
    clients:
        api_payment:
            class: 'Namespace\Of\Your\Client\AwesomeClient'
```

This method will redefine client class only for specific GuzzleBundle client.
