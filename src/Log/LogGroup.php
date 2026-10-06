<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

class LogGroup
{
    /** @var LogMessage[] */
    protected array $messages = [];

    protected ?string $requestName = null;

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
     *
     * @param LogMessage[] $value
     */
    public function setMessages(array $value): void
    {
        $this->messages = $value;
    }

    /**
     * Add Log Messages
     *
     * @param LogMessage[] $value
     */
    public function addMessages(array $value): void
    {
        $this->messages = array_merge($this->messages, $value);
    }

    /**
     * Return Log Messages
     *
     * @return LogMessage[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }
}
