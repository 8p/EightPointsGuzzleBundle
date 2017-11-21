<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

use Psr\Log\LoggerInterface as PsrLoggerInterface;

/**
 * @author  SuRiKmAn <surikman@surikman.sk>
 */
interface LoggerInterface extends PsrLoggerInterface
{
    /**
     * Clear messages list
     *
     * @return void
     */
    public function clear();

    /**
     * Return if messages exist or not
     *
     * @return boolean
     */
    public function hasMessages() : bool;

    /**
     * Return log messages
     *
     * @return array
     */
    public function getMessages() : array;
}
