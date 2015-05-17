<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

use       GuzzleHttp\Message\RequestInterface;

/**
 * Class LogRequest
 *
 * @package EightPoints\Bundle\GuzzleBundle\Log
 * @author  Florian Preusner
 *
 * @version 2.2
 * @since   2015-05
 */
class LogRequest {

	/**
	 * @var string
	 */
	protected $host;

	/**
	 * Construct
	 *
	 * @author  Florian Preusner
	 * @version 2.2
	 * @since   2015-05
	 *
	 * @param   RequestInterface $request
	 */
    public function __construct(RequestInterface $request) {

        $this->save($request);
    } // end: __construct

	/**
	 * Save data
	 *
	 * @author  Florian Preusner
	 * @version 2.2
	 * @since   2015-05
	 *
	 * @param   RequestInterface $request
	 */
	public function save(RequestInterface $request) {

		$this->setHost($request->getHost());
	} // end: save()

	/**
	 * Set request host
	 *
	 * @author  Florian Preusner
	 * @version 2.2
	 * @since   2015-05
	 *
	 * @param   string $value
	 */
	public function setHost($value) {

		$this->host = $value;
	} // end: setHost()

	/**
	 * Return host
	 *
	 * @author  Florian Preusner
	 * @version 2.2
	 * @since   2015-05
	 *
	 * @return  string
	 */
	public function getHost() {

		return $this->host;
	} // end: getHost()
} // end: LogRequest
