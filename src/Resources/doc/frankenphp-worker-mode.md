# FrankenPHP / long-running worker compatibility

This bundle is compatible with [FrankenPHP](https://frankenphp.dev/) worker mode and other long-running Symfony runtimes
(RoadRunner, Swoole, etc.) where the PHP process and the service container survive across HTTP requests.

## What was fixed

Symfony resets services tagged with `kernel.reset` between requests. Without that, singleton services can leak state.

| Component | Behavior |
|-----------|----------|
| In-memory `Logger` (profiler logging) | Implements `ResetInterface` and is tagged with `kernel.reset` so buffered messages (including request/response bodies) are cleared after each request. |
| `HttpDataCollector` | `reset()` also clears attached loggers, so state is wiped even if `collect()` did not run. |
| `options.cookies: true` | Injects a shared `CookieJar` service tagged with `kernel.reset` (`clear`), so cookies do not leak between requests on a singleton client. |

## Recommendations

1. Keep `logging` / `profiling` tied to `%kernel.debug%` in production workers unless you need them; they allocate memory for each HTTP call.
2. Prefer `options.cookies: false` (default) unless you need a cookie jar. When cookies are required, `cookies: true` is worker-safe with this bundle.
3. Do not store request-specific data in custom Guzzle middleware registered as shared services without implementing reset.

## Verification

After deploying, confirm under load that memory stays stable and that authenticated upstream sessions (cookies) are not shared across unrelated Symfony requests.
