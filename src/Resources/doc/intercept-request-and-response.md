# Intercept request and response

In some situations you might want to change a request before it's send out from
a Guzzle client, or change the response that comes back from a Guzzle client.

This bundle allows you to do just that using Symfony event dispatch component.

Let's assume you've configured the following clients in your application:

```yaml
eight_points_guzzle:
    clients:
        payment:
            base_url: 'http://api.payment.example'
        crm:
            base_url: 'http://api.crm.tld'
```

And suppose that `payment` requires authorization using some token in the request header.
How can we do that?

## Event listener for intercepting requests

First of all we have to write an event listener, that will see all requests from our `payment`
client, and can modify them.

```php
namespace App\EventListener;

use EightPoints\Bundle\GuzzleBundle\Events\PreTransactionEvent;

class PaymentApiGuzzleEventListener
{
    /**
     * @param PreTransactionEvent $event
     */
    public function onPreTransaction(PreTransactionEvent $event)
    {
        // get request from the event
        $request = $event->getTransaction();

        // setup new header to request
        $modifiedRequest = $request->withHeader('Authorization', 'Bearer longLongLongToken');

        // replace request in event
        $event->setTransaction($modifiedRequest);
    }
}
```

It's important to note that `Response` is immutable. As such, `withHeader` method does not change the request
by reference, but rather clones it, changes it and then returns it.

Now, just writing this class won't make it fire for every request on the `payment` client.
For that we need to register it in the Symfony configuration as an event listener, as follows:

```yaml
services:
    App\EventListener\PaymentApiGuzzleEventListener:
        class: App\EventListener\PaymentApiGuzzleEventListener
        tags:       
            - { name: kernel.event_listener, event: eight_points_guzzle.pre_transaction.payment, method: onPreTransaction }
```

Because this listener listens for the event `eight_points_guzzle.pre_transaction.payment` it will _only_ receive
events regarding the `payment` client, no other clients. If you want a listener that receives events for all clients,
subscribe to the `eight_points_guzzle.pre_transaction` event instead. This can be useful for logging, auditing, etc.

Note that if a generic listener and a client specific listener both change a request in the same way
(for example, both add the same header), the value from the client specific listener overrides the value
from the generic listener.

## Event listener for intercepting responses

In previous step we intercepted the request and changed it, but we want to track the response too.
For example we can invalidate token if the `payment` API rejected it.

Let's subscribe our service to one more event:

```yaml
services:
    App\EventListener\PaymentApiGuzzleEventListener:
        class: App\EventListener\PaymentApiGuzzleEventListener
        tags:
            - { name: kernel.event_listener, event: eight_points_guzzle.pre_transaction.payment, method: onPreTransaction }
            - { name: kernel.event_listener, event: eight_points_guzzle.post_transaction.payment, method: onPostTransaction }
```

Again, because this listener listens for the event `eight_points_guzzle.post_transaction.payment` it will _only_ receive
events regarding the `payment` client, no other clients. Just like with pre transaction events, if you want a listener that
receives events for all clients, subscribe to the `eight_points_guzzle.post_transaction` event instead.
This can be useful for logging, auditing, etc.

Note that if a generic listener and a client specific listener both change a response in the same way
(for example, both add the same header), the value from the client specific listener overrides the value
from the generic listener.

Now we can implement the `onPostTransaction` method on our service:

```php
namespace App\EventListener;

use EightPoints\Bundle\GuzzleBundle\Events\PostTransactionEvent;

class PaymentApiGuzzleEventListener
{
    // ...
    
    /**
     * @param PostTransactionEvent $event
     */
    public function onPostTransaction(PostTransactionEvent $event)
    {
        // get response from the event
        $response = $event->getTransaction();

        // check if response status code is 403
        if ($response->getStatusCode() === Response::HTTP_FORBIDDEN) {
            // invalidate token
        }
    }
}
```

## Using event subscribers instead of listeners

If you want to you can also use an event subscriber to the same as above

```php
namespace App\EventSubscriber;

use EightPoints\Bundle\GuzzleBundle\Events\GuzzleEvents;
use EightPoints\Bundle\GuzzleBundle\Events\PreTransactionEvent;
use EightPoints\Bundle\GuzzleBundle\Events\PostTransactionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PaymentApiGuzzleEventSubscriber implements EventSubscriberInterface
{
    /**
     * @param PreTransactionEvent $event
     */
    public function onPreTransaction(PreTransactionEvent $event)
    {
        // get request from the event
        $request = $event->getTransaction();

        // setup new header to request
        $modifiedRequest = $request->withHeader('Authorization', 'Bearer longLongLongToken');

        // replace request in event
        $event->setTransaction($modifiedRequest);
    }
    
    /**
     * @param PostTransactionEvent $event
     */
    public function onPostTransaction(PostTransactionEvent $event)
    {
        // get response from the event
        $response = $event->getTransaction();

        // check if response status code is 403
        if ($response->getStatusCode() === Response::HTTP_FORBIDDEN) {
            // invalidate token
        }
    }
    
     public static function getSubscribedEvents()
     {
         return [
             GuzzleEvents::preTransactionFor('payment') => 'onPreTransaction',
             GuzzleEvents::postTransactionFor('payment') => 'onPostTransaction'
        ];
     }
}
```

And configure the Symfony service as usual for event subscribers:

```yaml
services:
    App\EventSubscriber\PaymentApiGuzzleEventSubscriber:
        class: App\EventSubscriber\PaymentApiGuzzleEventSubscriber
        tags:
            - { name: kernel.event_subscriber }
```

(This is not required when your project uses autoconfiguration, it will
be tagged automatically)

## Learn more
- [Symfony doc: Events and Event Listeners][1]

[1]: https://symfony.com/doc/current/event_dispatcher.html
