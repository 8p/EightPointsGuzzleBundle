<?php

namespace EightPoints\Bundle\GuzzleBundle\DataCollector;

use       EightPoints\Bundle\GuzzleBundle\Log\LogGroup,

	      Psr\Log\LoggerInterface,

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
     * @var \EightPoints\Bundle\GuzzleBundle\Log\Logger $logger
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
            'logs'      => array(),
            'callCount' => 0,
        );
    } // end: __construct()

    /**
     * {@inheritdoc}
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2014-11
     */
    public function collect(Request $request, Response $response, \Exception $exception = null) {

        $messages  = $this->logger->getMessages();
        $requestId = $request->getUri();

        // clear log to have only messages related to symfony request context
        $this->logger->clear();

        $logGroup = $this->getLogGroup($requestId);
        $logGroup->setRequestName($request->getPathInfo());
        $logGroup->addMessages($messages);
    } // end: collect()

    /**
     * {@inheritdoc}
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2014-11
     */
    public function getName() {

        return 'guzzle';
    } // end: getName()

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
    } // end: getLogs()

    /**
     * Get all messages
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @return  array
     */
    public function getMessages() {

        $messages = array();

        foreach($this->getLogs() as $log) {

            foreach($log->getMessages() as $message) {

                $messages[] = $message;
            }
        }

        return $messages;
    } // end: getMessages()

    /**
     * Return amount of http calls
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @return  integer
     */
    public function getCallCount() {

        $callCount = count($this->getMessages());

        return $callCount;
    } // end: getCallCount()

    /**
     * Get Error Count
     *
     * @author  Florian Preusner
     * @version 2.2
     * @since   2015-05
     *
     * @return  integer
     */
    public function getErrorCount() {

        return 0; //@todo
    } // end: getErrorCount()

    /**
     * Get total time of all requests
     *
     * @author Florian Preusner
     * @since  2015-05
     *
     * @return float
     */
    public function getTotalTime() {

        return 0; //@todo
    } // end: getTotalTime()

    /**
     * Returns (new) LogGroup based on given id
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @param   string $id
     * @return  LogGroup
     */
    protected function getLogGroup($id) {

        if(!isset($this->data['logs'][$id])) {

            $this->data['logs'][$id] = new LogGroup();
        }

        return $this->data['logs'][$id];
    } // end: getLogGroup()
} // end: HttpDataCollector
