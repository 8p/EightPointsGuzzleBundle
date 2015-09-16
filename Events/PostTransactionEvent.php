<?php
/**
 * Created by IntelliJ IDEA.
 * User: chris
 * Date: 9/16/15
 * Time: 12:07 PM
 */

namespace EightPoints\Bundle\GuzzleBundle\Events;

use Psr\Http\Message\ResponseInterface;
use Symfony\Component\EventDispatcher\Event;

class PostTransactionEvent extends Event
{
    /**
     * @var ResponseInterface
     */
    protected $response;
    protected $serviceName;

    /**
     * PostTransactionEvent constructor.
     *
     * @param ResponseInterface $response
     */
    public function __construct(ResponseInterface $response, $serviceName)
    {
        $this->response = $response;
        $this->serviceName = $serviceName;
    }


    /**
     * @return ResponseInterface
     */
    public function getTransaction()
    {
        return $this->response;
    }
}
