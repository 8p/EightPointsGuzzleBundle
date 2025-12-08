<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use EightPoints\Bundle\GuzzleBundle\DataCollector\HttpDataCollector;
use EightPoints\Bundle\GuzzleBundle\Log\Logger;
use EightPoints\Bundle\GuzzleBundle\Middleware\EventDispatchMiddleware;
use EightPoints\Bundle\GuzzleBundle\Middleware\LogMiddleware;
use EightPoints\Bundle\GuzzleBundle\Middleware\ProfileMiddleware;
use EightPoints\Bundle\GuzzleBundle\Middleware\RequestTimeMiddleware;
use EightPoints\Bundle\GuzzleBundle\Middleware\SymfonyLogMiddleware;
use GuzzleHttp\Client;
use GuzzleHttp\MessageFormatter;
use GuzzleHttp\Middleware;

return static function (ContainerConfigurator $container) {
    $parameters = $container->parameters();

    //classes
    $parameters->set('eight_points_guzzle.http_client.class', Client::class);
    $parameters->set('eight_points_guzzle.formatter.class', MessageFormatter::class);
    $parameters->set('eight_points_guzzle.symfony_log_formatter.class', MessageFormatter::class);
    $parameters->set('eight_points_guzzle.data_collector.class', HttpDataCollector::class);
    $parameters->set('eight_points_guzzle.logger.class', Logger::class);

    //middlewares
    $parameters->set('eight_points_guzzle.middleware.log.class', LogMiddleware::class);
    $parameters->set('eight_points_guzzle.middleware.profile.class', ProfileMiddleware::class);
    $parameters->set('eight_points_guzzle.middleware.event_dispatcher.class', EventDispatchMiddleware::class);
    $parameters->set('eight_points_guzzle.middleware.request_time.class', RequestTimeMiddleware::class);
    $parameters->set('eight_points_guzzle.middleware.symfony_log.class', SymfonyLogMiddleware::class);

    //parameters
    $parameters->set('eight_points_guzzle.symfony_log_formatter.pattern', '{method} {uri} {code}');

    //Deprecated! Remove them in v8.0
    $parameters->set('eight_points_guzzle.middleware.class', Middleware::class);
    $parameters->set('eight_points_guzzle.plugin.header.headers', []);
};
