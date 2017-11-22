<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

class LogGroup
{
    /** @var array */
    protected $messages = [];

    /** @var string */
    protected $requestName;

    /**
     * Set Request Name
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
     * @return string
     */
    public function getRequestName()
    {
        return $this->requestName;
    }

    /**
     * Set Log Messages
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
     * @return array
     */
    public function getMessages() : array
    {
        return $this->messages;
    }
}
