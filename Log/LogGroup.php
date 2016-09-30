<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

/**
 * Class LogGroup
 *
 * @package EightPoints\Bundle\GuzzleBundle\Log
 * @author  Florian Preusner
 *
 * @version 2.1
 * @since   2015-05
 */
class LogGroup {

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
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @param   string $value
     */
    public function setRequestName($value) {

        $this->requestName = $value;
    } // end: setRequestName()

    /**
     * Get Request Name
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @return  string
     */
    public function getRequestName() {

        return $this->requestName;
    } // end: getRequestName()

    /**
     * Set Log Messages
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @param   array $value
     */
    public function setMessages(array $value) {

        $this->messages = $value;
    } // end: setMessages()

    /**
     * Add Log Messages
     *
     * @author  Bart Swaalf
     * @since   2016-09
     *
     * @param   array $value
     */
    public function addMessages(array $value) {

        $this->messages = array_merge($this->messages, $value);
    } // end: addMessages()

    /**
     * Return Log Messages
     *
     * @author  Florian Preusner
     * @version 2.1
     * @since   2015-05
     *
     * @return  array
     */
    public function getMessages() {

        return $this->messages;
    } // end: getMessages()
} // end: LogGroup
