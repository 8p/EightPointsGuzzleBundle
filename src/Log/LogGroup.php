<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

/**
 * @version 2.1
 * @since   2015-05
 */
class LogGroup
{
    /** @var array */
    protected $messages = [];

    /** @var string */
    protected $requestName;

    /**
     * Set Request Name
     *
     * @version 2.1
     * @since   2015-05
     *
     * @param string $value
     *
     * @return void
     */
    public function setRequestName(string $value)
    {
        $this->requestName = $value;
    }

    /**
     * Get Request Name
     *
     * @version 2.1
     * @since   2015-05
     *
     * @return string
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
     * @param array $value
     *
     * @return void
     */
    public function setMessages(array $value)
    {
        $this->messages = $value;
    }

    /**
     * Add Log Messages
     *
     * @version 4.5
     * @since   2016-09
     *
     * @param array $value
     *
     * @return void
     */
    public function addMessages(array $value)
    {
        $this->messages = array_merge($this->messages, $value);
    }

    /**
     * Return Log Messages
     *
     * @version 2.1
     * @since   2015-05
     *
     * @return array
     */
    public function getMessages() : array
    {
        return $this->messages;
    }
}
