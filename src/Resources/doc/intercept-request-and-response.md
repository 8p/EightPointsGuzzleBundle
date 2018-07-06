# Intercept request and response

For example you are having configured next clients:

```yaml
eight_points_guzzle:
    clients:
        api_payment:
            base_url: "http://api.domain.tld"
        api_partners:
            base_url: "http://pastners.tld"
```

and `api_partners` requires authorization using some token in header.  
How we can do that?

## Interceptor class

First of all we have to crate out interceptor class, where will be all the logic:

```php
namespace App\EventListener;

use EightPoints\Bundle\GuzzleBundle\Events\GuzzleEventListenerInterface;

class PartnersApiGuzzleEventListener implements GuzzleEventListenerInterface
{
    /** @var string|null */
    private $serviceName;

    /**
     * @param string|null $serviceName
     */
    public function setServiceName($serviceName)
    {
        $this->serviceName = $serviceName;
    }
}
```

It's good to start from this initial file. We implemented `GuzzleEventListenerInterface` interface and added `setServiceName` method requested from interface.
Later we will use this method and property.

## Register our interceptor as listener service

The class is created and it does nothing, because Guzzle Bundle doesn't know about it.

Register newly created class as service in `config/services.yaml`:

```yaml
services:
    App\EventListener\PartnersApiGuzzleEventListener:
        class: App\EventListener\PartnersApiGuzzleEventListener
```

and subscribe to the pre transaction event:

```yaml
services:
    App\EventListener\PartnersApiGuzzleEventListener:
        class: App\EventListener\PartnersApiGuzzleEventListener
        tags:
            - { name: kernel.event_listener, event: eight_points_guzzle.pre_transaction, method: onPreTransaction, service: api_partners }
```

We just subscribed to the `eight_points_guzzle.pre_transaction` event and exposed method `onPreTransaction` to be triggered. In next step we will implement this method.

Also notice that we configured `service: api_partners` parameter. String `api_partners` will be passed to `setServiceName` method and we will use it to track if the client is the right one.

## Implement interceptor logic

As we configured in previous step, we have to implement `onPreTransaction` method in our interceptor class:

```php
namespace App\EventSubscriber;

use EightPoints\Bundle\GuzzleBundle\Events\GuzzleEventListenerInterface;
use EightPoints\Bundle\GuzzleBundle\Events\PreTransactionEvent;

class PartnersApiGuzzleEventListener implements GuzzleEventListenerInterface
{
    /** @var string|null */
    private $serviceName;

    /**
     * @param string|null $serviceName
     */
    public function setServiceName($serviceName)
    {
        $this->serviceName = $serviceName;
    }

    /**
     * @param PreTransactionEvent $event
     */
    public function onPreTransaction(PreTransactionEvent $event)
    {
        // track that the request client is the right one (api_partners in our case)
        if ($event->getServiceName() !== $this->serviceName) {
            return;
        }

        // get request from the event
        $request = $event->getTransaction();

        // setup new header to request
        $modifiedRequest = $request->withHeader('Authorization', 'Bearer longLongLongToken');

        // replace request in event
        $event->setTransaction($modifiedRequest);
    }
}
```

It's important to know that `withHeader` method doesn't change the request by reference.
It clones the request, change it and return us.

## Intercept response

In previous step we intercepted request and changed it, but we want to track the response too.  
For example we can invalidate token if api of partners rejected it.

Let's subscribe out service to one more event:

```yaml
services:
    App\EventListener\PartnersApiGuzzleEventListener:
        class: App\EventListener\PartnersApiGuzzleEventListener
        tags:
            - { name: kernel.event_listener, event: eight_points_guzzle.pre_transaction, method: onPreTransaction, service: api_partners }
            - { name: kernel.event_listener, event: eight_points_guzzle.post_transaction, method: onPostTransaction, service: api_partners }
```

and implement `onPostTransaction` method:

```php
namespace App\EventSubscriber;

use EightPoints\Bundle\GuzzleBundle\Events\GuzzleEventListenerInterface;
use EightPoints\Bundle\GuzzleBundle\Events\PreTransactionEvent;
use EightPoints\Bundle\GuzzleBundle\Events\PostTransactionEvent;
use Symfony\Component\HttpFoundation\Response;

class PartnersApiGuzzleEventListener implements GuzzleEventListenerInterface
{
    // ...
    
    /**
     * @param PostTransactionEvent $event
     */
    public function onPostTransaction(PostTransactionEvent $event)
    {
        // track that the request client is the right one (api_partners in our case)
        if ($event->getServiceName() !== $this->serviceName) {
            return;
        }

        // get response from the event
        $response = $event->getTransaction();

        // check if response status code is 403
        if ($response->getStatusCode() === Response::HTTP_FORBIDDEN) {
            // invalidate token
        }
    }
}
```

Now remained to call the api of partners:

```php
$this->get('eight_points_guzzle.client.api_partners')->get('/some-api-route')
```

## Learn more
- [Symfony doc: Events and Event Listeners][1]

[1]: https://symfony.com/doc/current/event_dispatcher.html
