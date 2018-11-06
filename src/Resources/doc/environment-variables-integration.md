# Environment variables integration

Environment variable processors were [introduced][1] in Symfony 3.4 and with each symfony release this functionality is constantly improved.
We also want to support this direction and let's see how to use environment variables with Guzzle Bundle.

For example you are having configured next client:

```yaml
# config/packages/eight_points_guzzle.yaml

eight_points_guzzle:
    clients:
        api_payment:
            base_url: "http://api.domain.tld"
```

and you want to move `base_url` value to env variables:

```dotenv
# .env

API_PAYMENT_URL=http://api.domain.tld
```

Next let's do some small adjustments in configuration file:

```yaml
parameters:
    env(API_PAYMENT_URL): ''

eight_points_guzzle:
    clients:
        api_payment:
            base_url: '%env(string:API_PAYMENT_URL)%'
```

We added `env(API_PAYMENT_URL): ''` to define this variable if it was not defined not to stop the build process (for example in CI system).  
Also we used `'%env(string:API_PAYMENT_URL)%'` to insert the value of environment variable with type casting.

That's all!

## Learn more
- [Symfony blog: New in Symfony 3.4 - Advanced environment variables][1]
- [Symfony doc: How to Set external Parameters in the Service Container][2]

[1]: https://symfony.com/blog/new-in-symfony-3-4-advanced-environment-variables
[2]: https://symfony.com/doc/current/configuration/external_parameters.html
