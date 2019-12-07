<?php

namespace EightPoints\Bundle\GuzzleBundle\Events;

use function trigger_error;

@trigger_error(
    sprintf('Interface "%s" is deprecated and will be removed in EightPointsGuzzleBundle version 8', GuzzleEventListenerInterface::class),
    E_USER_DEPRECATED
);

interface GuzzleEventListenerInterface
{
    /**
     * @param string $serviceName
     *
     * @return mixed
     */
    public function setServiceName($serviceName);
}
