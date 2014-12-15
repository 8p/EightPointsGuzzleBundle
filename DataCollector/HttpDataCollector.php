<?php

namespace EightPoints\Bundle\GuzzleBundle\DataCollector;

use       Psr\Log\LoggerInterface,

          Symfony\Component\HttpKernel\DataCollector\DataCollector,
          Symfony\Component\HttpFoundation\Request,
          Symfony\Component\HttpFoundation\Response;

/**
 * HttpDataCollector
 * Collecting http data for Symfony profiler
 *
 * @package   EightPoints\Bundle\GuzzleBundle\DataCollector
 * @author    Florian Preusner
 *
 * @version   2.1
 * @since     2014-11
 */
class HttpDataCollector extends DataCollector {

    /**
     * @var LoggerInterface $logger
     */
    protected $logger;

    /**
     * Constructor
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2014-11
     *
     * @param   LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger) {

        $this->logger = $logger;
        $this->data   = array(
            'requests' => array(),
            'logs'     => array()
        );
    } // end: __construct

    /**
     * {@inheritdoc}
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2014-11
     */
    public function collect(Request $request, Response $response, \Exception $exception = null) {

        $messages = $this->logger->getMessages();

        $this->addLogs($messages);
        //$this->addRequest($request, $response); // @todo: only collect information that is interesting
    } // end: collect

    /**
     * {@inheritdoc}
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2014-11
     */
    public function getName() {

        return 'guzzle';
    } // end: getName

    /**
     * Returning log entries
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2014-11
     *
     * @return  array $logs
     */
    public function getLogs() {

        $logs = isset($this->data['logs']) ? $this->data['logs'] : array();

        return $logs;
    } // end: getLogs

    /**
     * Returning requests
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2014-11
     *
     * @return  array $requests
     */
    public function getRequests() {

        $requests = isset($this->data['requests']) ? $this->data['requests'] : array();

        return $requests;
    } // end: getRequests

    /**
     * Add log messages to data variable
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2014-11
     *
     * @param   array $messages
     */
    protected function addLogs(array $messages) {

        foreach($messages as $message) {

            array_push($this->data['logs'], $message);
        }
    } // end: addLogs

    /**
     * Add request/response to data variable
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2014-11
     *
     * @param   Request  $request
     * @param   Response $response
     */
    protected function addRequest(Request $request, Response $response) {

        $data = array(
            'request' => $request,
            'response' => $response
        );

        array_push($this->data['requests'], $data);
    } // end: addRequest
} // end: HttpDataCollector