<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

use       Psr\Log\LoggerTrait,
          Psr\Log\LoggerInterface;

/**
 * Logger
 *
 * @package   EightPoints\Bundle\GuzzleBundle\Log
 * @author    Florian Preusner
 *
 * @version   2.1
 * @since     2014-11
 */
class Logger implements LoggerInterface {

    use LoggerTrait;

    /**
     * @var array $messages
     */
    private $messages = array();

    /**
     * Log message
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2014-11
     *
     * @param   string $level
     * @param   string $message
     * @param   array  $context
     *
     * @return  void
     */
    public function log($level, $message, array $context = array()) {

        // @todo: reactivate context cause of serializing closures is not possible
        //[1] => EightPoints\Bundle\GuzzleBundle\Log\LogMessage Object(
        //    [level:EightPoints\Bundle\GuzzleBundle\Log\LogMessage:private] => info
        //    [message:EightPoints\Bundle\GuzzleBundle\Log\LogMessage:private] => vagrant-debian64 Guzzle/5.0.3 curl/7.26.0 PHP/5.5.10-1~dotdeb.1 - [2014-12-02T03:57:38+00:00] "GET /restfullink?p2i_key=8d97e7b12a93172f&p2i_url=http%3A%2F%2Fwww.stuttgarter-zeitung.de%2F&p2i_fullpage=1&p2i_wait=5&p2i_imageformat=png /" 200 181
        //    [context:EightPoints\Bundle\GuzzleBundle\Log\LogMessage:private] => Array(
        //        [request] => GuzzleHttp\Message\Request Object(
        //        [url:GuzzleHttp\Message\Request:private] => GuzzleHttp\Url Object(
        //            [scheme:GuzzleHttp\Url:private] => http
        //            [host:GuzzleHttp\Url:private] => api.page2images.com
        //            [port:GuzzleHttp\Url:private] =>
        //            [username:GuzzleHttp\Url:private] =>
        //            [password:GuzzleHttp\Url:private] =>
        //            [path:GuzzleHttp\Url:private] => /restfullink
        //            [fragment:GuzzleHttp\Url:private] =>
        //            [query:GuzzleHttp\Url:private] => GuzzleHttp\Query Object(
        //                [encoding:GuzzleHttp\Query:private] => rawurlencode
        //                [aggregator:GuzzleHttp\Query:private] => Closure Object(
        //                    [static] => Array(
        //                        [numericIndices] => 1
        //                    )
        //                    [parameter] => Array(
        //                        [$data] =>
        //                    )

        $message = new LogMessage($level, $message, array());

        $this->messages[] = $message;
    } // end: log

    /**
     * Return log messages
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2014-11
     *
     * @return  array
     */
    public function getMessages() {

        return $this->messages;
    } // end: getMessages
} // end: Logger
