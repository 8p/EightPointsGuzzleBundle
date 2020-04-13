# Autowiring Clients

Autowiring was introduced in Symfony 3.3 and let's read how [Symfony Documentation][1] describes it:

> Autowiring allows you to manage services in the container with minimal configuration. It reads the type-hints on your constructor (or other methods) and automatically passes the correct services to each method. Symfony's autowiring is designed to be predictable: if it is not absolutely clear which dependency should be passed, you'll see an actionable exception.

## Symfony >= 4.2
In Symfony 4.2, it is made possible to [bind services by type and name](https://symfony.com/blog/new-in-symfony-4-2-autowiring-by-type-and-name). This feature makes using Guzzle clients a lot easier. Given the following configuration:

```yaml
eight_points_guzzle:
    clients:
        api_payment:
            base_url: "http://api.domain1.tld"
        api_crm:
            class: App\Client\ApiCrmClient
            base_url: "http://api.domain2.tld"
```
The clients can be autowired without further configuration (but mandatory variable names), like this:

```php
namespace App\Controller;

use GuzzleHttp\ClientInterface;
use App\Client\ApiCrmClient;

class FooController extends AbstractController
{
    public function bar(ClientInterface $apiPaymentClient)
    {
        // Default Client class    
    }

    public function baz(ApiCrmClient $apiCrmClient)
    {
        // Custom Client class (must extend GuzzleHttp\Client)    
    }
}
```

Autowiring takes place by the combination of the class name and the variable name, as described in [this blog].

## Symfony < 4.2

Getting in consideration, that Guzzle Bundle creates clients of same class, it becomes obvious that Symfony will not be able to guess what to inject.
With some small configurations we can help Symfony to do it. 

For example you have configured next client:

```yaml
eight_points_guzzle:
    clients:
        api_payment:
            base_url: "http://api.domain.tld"
```

By default, Guzzle Bundle uses `GuzzleHttp\Client` class but we have to use another one.
For example, let's create file `ApiPaymentClient.php` in folder `src/Client`:

```php
namespace App\Client;

use GuzzleHttp\Client;

class ApiPaymentClient extends Client
{

}
```

Configure Guzzle Bundle to use this class for `api_payment` client:

```yaml
eight_points_guzzle:
    clients:
        api_payment:
            class: App\Client\ApiPaymentClient
            base_url: "http://api.domain.tld"
```

Forbid to use classes from `src/Client` as services in `config/services.yaml` file:

```diff
 services:
     App\:
         resource: '../src/*'
-        exclude: '../src/{Entity,Migrations,Tests,Kernel.php}'
+        exclude: '../src/{Entity,Migrations,Tests,Kernel.php,Client}'
```
*Note: Guzzle Bundle will create services with these classes. DI system do not need to do this.*  

Link client created by Guzzle Bundle with class on the level of DI:

```yaml
services:
    # ...
       
    App\Client\ApiPaymentClient: '@eight_points_guzzle.client.api_payment'
```

Use it anywhere:

```php
namespace App\Controller;

use App\Client\ApiPaymentClient;

class FooController extends AbstractController
{
    /**
     * @param ApiPaymentClient $client
     */
    public function bar(ApiPaymentClient $client)
    {
    
    }
}
```

Note that:
 - this flow should be repeated for each client.
 - don't use the same client class for more than one client
 
That's all.
 
[1]: https://symfony.com/doc/current/service_container/autowiring.html
