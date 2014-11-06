<?php

namespace EightPoints\Bundle\GuzzleBundle\EventListener;

use       GuzzleHttp\Event\BeforeEvent,
          GuzzleHttp\Event\SubscriberInterface;

/**
 * Adds headers to request
 *
 * @package   EightPoints\Bundle\GuzzleBundle\EventListener
 * @author    Florian Preusner
 *
 * @version   2.0
 * @since     2013-10
 */
class HeaderSubscriber implements SubscriberInterface {

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
     * @version 2.0
     * @since   2013-10
     */
    public function getEvents() {

        return ['before' => ['onBefore']];
    } // end: getEvents

    /**
     * Add given headers to request
     *
     * @author  Florian Preusner
     * @version 2.0
     * @since   2013-10
     *
     * @param   BeforeEvent $event
     *
     * @return  void
     */
    public function onBefore(BeforeEvent $event) {

        $request = $event->getRequest();

        // make sure to keep headers that have been already set
        foreach($this->getHeaders() as $key => $value) {

            $request->addHeader($key, $value);
        }
    } // end: onBefore
} // end: HeaderSubscriber
