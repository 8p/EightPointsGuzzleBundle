<?php
namespace EightPoints\Bundle\GuzzleBundle\Events;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Created by IntelliJ IDEA.
 * User: chris
 * Date: 9/16/15
 * Time: 12:04 PM
 */
class PreTransactionEvent extends Event
{
    /**
     * @var RequestInterface
     */
    protected $requestTransaction;
    protected $serviceName;

    /**
     * PreTransactionEvent constructor.
     *
     * @param RequestInterface $requestTransaction
     */
    public function __construct(RequestInterface $requestTransaction, $serviceName)
    {
        $this->requestTransaction = $requestTransaction;
        $this->serviceName = $serviceName;
    }

    /**
     * @return RequestInterface
     */
    public function getTransaction()
    {
        return $this->requestTransaction;
    }

    public function setTransaction(RequestInterface $requestTransaction)
    {
        $this->requestTransaction = $requestTransaction;
    }

    public function getServiceName()
    {
        return $this->serviceName;
    }

}
