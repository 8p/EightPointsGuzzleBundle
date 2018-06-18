# Disable throwing exceptions on HTTP errors

For example you are having configured next client:

```yaml
eight_points_guzzle:
    clients:
        api_payment:
            base_url: "http://api.domain.tld"
```

and you are doing request, but exception is thrown in case when API returns, for example, response with code 400.
It can be `RequestException`, `ClientException` or `ServerException`, but for some reasons you don't want to catch them.
You just want to receive response and to work with it.  

It's possible! Just setup `http_errors` to false:

```yaml
eight_points_guzzle:
    clients:
        api_payment:
            base_url: "http://api.domain.tld"
            options:
                http_errors: false
```

Read more about this option in the [official Guzzle documentation][1].

[1]: http://docs.guzzlephp.org/en/latest/request-options.html#http-errors
