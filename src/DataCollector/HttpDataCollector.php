<?php

namespace EightPoints\Bundle\GuzzleBundle\DataCollector;

use EightPoints\Bundle\GuzzleBundle\Log\LogGroup;
use EightPoints\Bundle\GuzzleBundle\Log\LoggerInterface;
use EightPoints\Bundle\GuzzleBundle\Log\LogMessage;
use Psr\Log\LogLevel;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Collecting http data for Symfony profiler
 */
class HttpDataCollector extends DataCollector
{
    use DataCollectorSymfonyCompatibilityTrait;

    /** @var \EightPoints\Bundle\GuzzleBundle\Log\LoggerInterface[] */
    protected $loggers;

    /** @var float */
    private $slowResponseTime;

    /**
     * @param \EightPoints\Bundle\GuzzleBundle\Log\LoggerInterface[] $loggers
     * @param float|int $slowResponseTime Time in seconds
     */
    public function __construct(array $loggers, float $slowResponseTime)
    {
        $this->loggers = $loggers;
        $this->slowResponseTime = $slowResponseTime;

        $this->reset();
    }

    /**
     * {@inheritdoc}
     */
    protected function doCollect(Request $request, Response $response, \Throwable $exception = null)
    {
        $messages = [];
        foreach ($this->loggers as $logger) {
            $messages = array_merge($messages, $logger->getMessages());
        }

        if ($this->slowResponseTime > 0) {
            foreach ($messages as $message) {
                if (!$message instanceof LogMessage) {
                    continue;
                }

                if ($message->getTransferTime() >= $this->slowResponseTime) {
                    $this->data['hasSlowResponse'] = true;
                    break;
                }
            }
        }

        $requestId = $request->getUri();

        // clear log to have only messages related to Symfony request context
        foreach ($this->loggers as $logger) {
            $logger->clear();
        }

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
    public function reset() : void
    {
        $this->data = [
            'logs' => [],
            'callCount' => 0,
            'totalTime' => 0,
            'hasSlowResponse' => false,
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
    public function getErrorCount() : int
    {
        return count($this->getErrorsByType(LogLevel::ERROR));
    }

    /**
     * @param string $type
     *
     * @return array
     */
    public function getErrorsByType(string $type) : array
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
     * Check if there were any slow responses
     *
     * @return bool
     */
    public function hasSlowResponses() : bool
    {
        return $this->data['hasSlowResponse'];
    }

    /**
     * @param float $time
     *
     * @return void
     */
    public function addTotalTime(float $time) : void
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
}
