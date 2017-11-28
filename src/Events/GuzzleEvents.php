<?php

namespace EightPoints\Bundle\GuzzleBundle\Events;

final class GuzzleEvents
{
    const PRE_TRANSACTION = 'eight_points_guzzle.pre_transaction';

    const POST_TRANSACTION = 'eight_points_guzzle.post_transaction';

    const EVENTS = [
        self::PRE_TRANSACTION,
        self::POST_TRANSACTION,
    ];
}
