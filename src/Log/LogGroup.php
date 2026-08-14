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
     */
    public function setRequestName(string $value): void
    {
        $this->requestName = $value;
    }

    /**
     * Get Request Name
     */
    public function getRequestName(): ?string
    {
        return $this->requestName;
    }

    /**
     * Set Log Messages
     */
    public function setMessages(array $value): void
    {
        $this->messages = $value;
    }

    /**
     * Add Log Messages
     */
    public function addMessages(array $value): void
    {
        $this->messages = array_merge($this->messages, $value);
    }

    /**
     * Return Log Messages
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
}
