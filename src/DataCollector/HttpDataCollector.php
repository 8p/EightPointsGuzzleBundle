<?php

namespace EightPoints\Bundle\GuzzleBundle\DataCollector;

use EightPoints\Bundle\GuzzleBundle\Log\LogGroup;
use EightPoints\Bundle\GuzzleBundle\Log\LoggerInterface;
use EightPoints\Bundle\GuzzleBundle\Log\LogMessage;
use Psr\Log\LogLevel;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Collecting http data for Symfony profiler
 */
class HttpDataCollector extends DataCollector
{
    /** @var \EightPoints\Bundle\GuzzleBundle\Log\LoggerInterface */
    protected $logger;

    /**
     * @param \EightPoints\Bundle\GuzzleBundle\Log\LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;

        $this->reset();
    }

    /**
     * {@inheritdoc}
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
     */
    public function getName() : string
    {
        return 'eight_points_guzzle';
    }

    /**
     * Resets this data collector to its initial state.
     *
     * @return void
     */
    public function reset()
    {
        $this->data = [
            'logs' => [],
            'callCount' => 0,
            'totalTime' => 0,
        ];
    }

    /**
     * Returning log entries
     *
     * @return array
     */
    public function getLogs() : array
    {
        return array_key_exists('logs', $this->data) ? $this->data['logs'] : [];
    }

    /**
     * Get all messages
     *
     * @return array
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
     * @return integer
     */
    public function getCallCount() : int
    {
        return count($this->getMessages());
    }

    /**
     * Get Error Count
     *
     * @return integer
     */
    public function getErrorCount(): int
    {
        return count($this->getErrorsByType(LogLevel::ERROR));
    }

    /**
     * @param string $type
     *
     * @return array
     */
    public function getErrorsByType(string $type): array
    {
        return array_filter(
            $this->getMessages(),
            function (LogMessage $message) use ($type) {
                return $message->getLevel() === $type;
            }
        );
    }

    /**
     * Get total time of all requests
     *
     * @return float
     */
    public function getTotalTime() : float
    {
        return $this->data['totalTime'];
    }

    /**
     * @param float $time
     *
     * @return void
     */
    public function addTotalTime(float $time)
    {
        $this->data['totalTime'] += $time;
    }

    /**
     * Returns (new) LogGroup based on given id
     *
     * @param string $id
     *
     * @return \EightPoints\Bundle\GuzzleBundle\Log\LogGroup
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
     *
     * @return string
     */
    public final function getIconColor() : string
    {
        $iconColor = version_compare(Kernel::VERSION, '2.8.0', '>=') ? '#AAAAAA' : '#3F3F3F';
        return $this->data['iconColor'] = $iconColor;
    }
}
