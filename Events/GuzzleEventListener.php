<?php
/**
 * Created by IntelliJ IDEA.
 * User: chris
 * Date: 9/16/15
 * Time: 2:40 PM
 */

namespace EightPoints\Bundle\GuzzleBundle\Events;


interface GuzzleEventListener
{
    public function setServiceName($serviceName);
}
