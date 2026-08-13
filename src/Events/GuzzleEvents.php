<?php

namespace EightPoints\Bundle\GuzzleBundle\Events;

use function sprintf;

final class GuzzleEvents
{
    public const PRE_TRANSACTION = 'eight_points_guzzle.pre_transaction';

    public const POST_TRANSACTION = 'eight_points_guzzle.post_transaction';

    public const EVENTS = [
        self::PRE_TRANSACTION,
        self::POST_TRANSACTION,
    ];

    public static function preTransactionFor(string $serviceName): string
    {
        return sprintf('%s.%s', self::PRE_TRANSACTION, $serviceName);
    }

    public static function postTransactionFor(string $serviceName): string
    {
        return sprintf('%s.%s', self::POST_TRANSACTION, $serviceName);
    }
}
