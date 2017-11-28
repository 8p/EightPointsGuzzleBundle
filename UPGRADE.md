# Upgrade instruction

This document describes the changes needed when upgrading from one version to another.

## Upgrading From 6.x to 7.0

### Step 1: change namespace in AppKernel
before:
```php
public function registerBundles()
{
    $bundles = [
        ...
        new EightPoints\Bundle\GuzzleBundle\GuzzleBundle(),
        ...
    ];
}
```

after:
```php
public function registerBundles()
{
    $bundles = [
        ...
        new EightPoints\Bundle\GuzzleBundle\EightPointsGuzzleBundle(),
        ...
    ];
}
```

### Step 2: change config key in app/config/config.yml
before:
```yaml
guzzle:
    clients:
        api_payment:
            base_url: "http://api.domain.tld"
```

after:
```yaml
eight_points_guzzle:
    clients:
        api_payment:
            base_url: "http://api.domain.tld"
```

### Step 3: move headers key under options config
before:
```yaml
guzzle:
    clients:
        api_payment:
            base_url: "http://api.domain.tld"
            headers:
                Accept: "application/json"
```

after:
```yaml
eight_points_guzzle:
    clients:
        api_payment:
            base_url: "http://api.domain.tld"
            options:
                headers:
                    Accept: "application/json"
```

### Step 4: client call
before:
```php
$this->get('guzzle.client.api_crm');
```

after:
```php
$this->get('eight_points_guzzle.client.api_crm');
```

### Step 5: event listeners definition
before:
```xml
<service id="listenerID" class="Your\ListenerClass\That\Implements\GuzzleEventListenerInterface">  
    <tag name="kernel.event_listener" event="guzzle_bundle.pre_transaction" method="onPreTransaction" service="servicename"/>  
</service>  
```

after:
```xml
<service id="listenerID" class="Your\ListenerClass\That\Implements\GuzzleEventListenerInterface">  
    <tag name="kernel.event_listener" event="eight_points_guzzle.pre_transaction" method="onPreTransaction" service="servicename"/>  
</service>  
```

### Step 6: if you have created any services, you should change name
before:
```xml
<argument type="service" id="guzzle.client.xyz" />
```

after:
```xml
<argument type="service" id="eight_points_guzzle.client.xyz" />
```

### Step 7: WSSE plugin

WSSE plugin was moved to separate repository.
If you are using WSSE then follow install guide from [gregurco/guzzle-bundle-wsse-plugin](https://github.com/gregurco/GuzzleBundleWssePlugin).
