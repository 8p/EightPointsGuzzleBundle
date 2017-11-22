<?php

namespace EightPoints\Bundle\GuzzleBundle\Events;

interface GuzzleEventListenerInterface
{
    /**
     * @param string $serviceName
     *
     * @return mixed
     */
    public function setServiceName($serviceName);
}
