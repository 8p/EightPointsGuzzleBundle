<?php

namespace EightPoints\Bundle\GuzzleBundle\DataCollector;

use EightPoints\Bundle\GuzzleBundle\Log\LogGroup;
use EightPoints\Bundle\GuzzleBundle\Log\LoggerInterface;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Collecting http data for Symfony profiler
 *
 * @version 2.1
 * @since   2014-11
 */
class HttpDataCollector extends DataCollector
{

    /**
     * @var \EightPoints\Bundle\GuzzleBundle\Log\Logger $logger
     */
    protected $logger;

    /**
     * Constructor
     *
     * @version 2.1
     * @since   2014-11
     *
     * @param   LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;

        $this->reset();
    }

    /**
     * {@inheritdoc}
     *
     * @version 2.1
     * @since   2014-11
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        $messages = $this->logger->getMessages();
        $requestId = $request->getUri();

        // clear log to have only messages related to Symfony request context
        $this->logger->clear();

        $logGroup = $this->getLogGroup($requestId);
        $logGroup->setRequestName($request->getPathInfo());
        $logGroup->addMessages($messages);
    }

    /**
     * {@inheritdoc}
     *
     * @version 2.1
     * @since   2014-11
     */
    public function getName()
    {
        return 'guzzle';
    }

    /**
     * Resets this data collector to its initial state.
     */
    public function reset()
    {
        $this->data = [
            'logs' => [],
            'callCount' => 0,
        ];
    }

    /**
     * Returning log entries
     *
     * @version 2.1
     * @since   2014-11
     *
     * @return  array $logs
     */
    public function getLogs()
    {
        return array_key_exists('logs', $this->data) ? $this->data['logs'] : array();
    }

    /**
     * Get all messages
     *
     * @version 2.1
     * @since   2015-05
     *
     * @return  array
     */
    public function getMessages()
    {
        $messages = array();

        foreach ($this->getLogs() as $log) {

            foreach ($log->getMessages() as $message) {

                $messages[] = $message;
            }
        }

        return $messages;
    }

    /**
     * Return amount of http calls
     *
     * @version 2.1
     * @since   2015-05
     *
     * @return  integer
     */
    public function getCallCount()
    {
        return count($this->getMessages());
    }

    /**
     * Get Error Count
     *
     * @version 2.2
     * @since   2015-05
     *
     * @return  integer
     */
    public function getErrorCount()
    {
        return 0; //@todo
    }

    /**
     * Get total time of all requests
     *
     * @since  2015-05
     *
     * @return float
     */
    public function getTotalTime()
    {
        return 0; //@todo
    }

    /**
     * Returns (new) LogGroup based on given id
     *
     * @version 2.1
     * @since   2015-05
     *
     * @param   string $id
     * @return  LogGroup
     */
    protected function getLogGroup($id)
    {
        if (!isset($this->data['logs'][$id])) {

            $this->data['logs'][$id] = new LogGroup();
        }

        return $this->data['logs'][$id];
    }

    /**
     * Return the color used version
     * @since 2016-06
     *
     * @return string
     */
    public final function getIconColor()
    {
        if ((float)$this->getSymfonyVersion() >= 2.8) {
            return $this->data['iconColor'] = '#AAAAAA';
        }
        return $this->data['iconColor'] = '#3F3F3F';
    }

    /**
     * Returns current version symfony
     * @since  2016-06
     *
     * @return string
     */
    private function getSymfonyVersion()
    {
        $symfonyVersion = \Symfony\Component\HttpKernel\Kernel::VERSION;
        $symfonyVersion = explode('.', $symfonyVersion, -1);
        $symfonyMajorMinorVersion = implode('.', $symfonyVersion);
        return $symfonyMajorMinorVersion;
    }

}
