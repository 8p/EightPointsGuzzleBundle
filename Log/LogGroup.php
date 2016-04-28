<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

/**
 * @version 2.1
 * @since   2015-05
 */
class LogGroup
{
    /**
     * @var array
     */
    protected $messages = array();

    /**
     * @var string
     */
    protected $requestName;

    /**
     * Set Request Name
     *
     * @version 2.1
     * @since   2015-05
     *
     * @param   string $value
     */
    public function setRequestName($value)
    {
        $this->requestName = $value;
    }

    /**
     * Get Request Name
     *
     * @version 2.1
     * @since   2015-05
     *
     * @return  string
     */
    public function getRequestName()
    {
        return $this->requestName;
    }

    /**
     * Set Log Messages
     *
     * @version 2.1
     * @since   2015-05
     *
     * @param   array $value
     */
    public function setMessages(array $value)
    {
        $this->messages = $value;
    }

    /**
     * Return Log Messages
     *
     * @version 2.1
     * @since   2015-05
     *
     * @return  array
     */
    public function getMessages()
    {
        return $this->messages;
    }
}
