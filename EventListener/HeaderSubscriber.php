<?php

namespace EightPoints\Bundle\GuzzleBundle\EventListener;

use       Guzzle\Common\Event,
          Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Adds headers to request
 *
 * @package   EightPoints\Bundle\GuzzleBundle\EventListener
 *
 * @copyright 8points IT
 * @author    Florian Preusner
 *
 * @version   1.0
 * @since     2013-10
 */
class HeaderSubscriber implements EventSubscriberInterface {

    /**
     * @var array $headers
     */
    private $headers;

    /**
     * Constructor
     *
     * @author  Florian Preusner
     * @version 1.0
     * @since   2013-10
     *
     * @param   array $headers
     */
    public function __construct(array $headers) {

        $this->setHeaders($headers);
    } // end: __construct

    /**
     * Retrieve headers that have been set
     *
     * @author  Florian Preusner
     * @version 1.0
     * @since   2013-10
     *
     * @return  array $headers
     */
    public function getHeaders() {

        return $this->headers;
    } // end: getHeaders

    /**
     * Set headers
     *
     * @author  Florian Preusner
     * @version 1.0
     * @since   2013-10
     *
     * @param   array $value
     * @return  void
     */
    public function setHeaders(array $value) {

        $this->headers = $value;
    } // end: setHeaders

    /**
     * Add header
     *
     * @author  Florian Preusner
     * @version 1.0
     * @since   2013-10
     *
     * @param   string $key
     * @param   string $value
     *
     * @return  void
     */
    public function addHeader($key, $value) {

        $this->headers[$key] = $value;
    } // end: addHeader

    /**
     * Get specified header
     *
     * @author  Florian Preusner
     * @version 1.0
     * @since   2013-10
     *
     * @param   string $key
     * @return  string
     */
    public function getHeader($key) {

        if(isset($this->headers[$key])) {

            return $this->headers[$key];
        }

        return null;
    } // end: getHeader

    /**
     * {@inheritdoc}
     *
     * @author  Florian Preusner
     * @version 1.0
     * @since   2013-10
     */
    public static function getSubscribedEvents() {

        return array('client.create_request' => 'onRequestCreate');
    } // end: getSubscribedEvents

    /**
     * Add given headers to request
     *
     * @author  Florian Preusner
     * @version 1.0
     * @since   2013-10
     *
     * @param   Event $event
     *
     * @return  void
     */
    public function onRequestCreate(Event $event) {

        $request = $event['request'];

        // make sure to keep headers that have been already set
        foreach($this->headers as $key => $value) {

            $request->addHeader($key, $value);
        }
    } // end: onRequestCreate
} // end: HeaderSubscriber