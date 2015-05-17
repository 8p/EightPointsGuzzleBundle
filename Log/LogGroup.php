<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

use       Symfony\Component\HttpFoundation\Request;

/**
 * Class LogGroup
 *
 * @package EightPoints\Bundle\GuzzleBundle\Log
 * @author  Florian Preusner
 *
 * @version 2.2
 * @since   2015-05
 */
class LogGroup {

	/**
     * @var array
     */
    protected $messages = array();

    /**
     * @var Request
     */
    protected $request;

	/**
	 * Set Symfony Request
	 *
	 * @author  Florian Preusner
	 * @version 2.2
	 * @since   2015-05
	 *
	 * @param   Request $value
	 */
    public function setRequest(Request $value) {

		$this->request = $value;
	} // end: setRequest()

	/**
	 * Get Symfony Request
	 *
	 * @author  Florian Preusner
	 * @version 2.2
	 * @since   2015-05
	 *
	 * @return  Request
	 */
	public function getRequest() {

		return $this->request;
	} // end: getRequest()

	/**
	 * Set Log Messages
	 *
	 * @author  Florian Preusner
	 * @version 2.2
	 * @since   2015-05
	 *
	 * @param   array $value
	 */
	public function setMessages(array $value) {

		$this->messages = $value;
	} // end: setMessages()

	/**
	 * Return Log Messages
	 *
	 * @author  Florian Preusner
	 * @version 2.2
	 * @since   2015-05
	 *
	 * @return  array
	 */
	public function getMessages() {

		return $this->messages;
	} // end: getMessages()
} // end: LogGroup
