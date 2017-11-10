<?php

namespace EightPoints\Bundle\GuzzleBundle\DataCollector;

use EightPoints\Bundle\GuzzleBundle\Log\LogGroup;
use EightPoints\Bundle\GuzzleBundle\Log\LoggerInterface;
use EightPoints\Bundle\GuzzleBundle\Log\Logger;
use EightPoints\Bundle\GuzzleBundle\Log\LogMessage;
use Psr\Log\LogLevel;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Collecting http data for Symfony profiler
 *
 * @version 2.1
 * @since   2014-11
 */
class HttpDataCollector extends DataCollector
{

    /** @var Logger $logger */
    protected $logger;

    /**
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
    public function getName() : string
    {
        return 'eight_points_guzzle';
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
    public function getLogs() : array
    {
        return array_key_exists('logs', $this->data) ? $this->data['logs'] : [];
    }

    /**
     * Get all messages
     *
     * @version 2.1
     * @since   2015-05
     *
     * @return  array
     */
    public function getMessages() : array
    {
        $messages = [];

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
    public function getCallCount() : int
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
    public function getErrorCount() : int
    {
        return count(array_filter($this->getMessages(), function (LogMessage $message) {
            return $message->getLevel() === LogLevel::ERROR;
        }));
    }

    /**
     * Get total time of all requests
     *
     * @since  2015-05
     *
     * @return float
     */
    public function getTotalTime() : float
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
    protected function getLogGroup(string $id) : LogGroup
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
    public final function getIconColor() : string
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
    private function getSymfonyVersion() : string
    {
        $symfonyVersion = Kernel::VERSION;
        $symfonyVersion = explode('.', $symfonyVersion, -1);
        $symfonyMajorMinorVersion = implode('.', $symfonyVersion);

        return $symfonyMajorMinorVersion;
    }

}
