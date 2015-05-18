<?php

namespace EightPoints\Bundle\GuzzleBundle\Log;

use       GuzzleHttp\Message\ResponseInterface;

/**
 * Class LogResponse
 *
 * @package EightPoints\Bundle\GuzzleBundle\Log
 * @author  Florian Preusner
 *
 * @version 2.1
 * @since   2015-05
 */
class LogResponse {

	/**
	 * @var array
	 */
	protected $headers = array();

	/**
	 * Construct
	 *
	 * @author  Florian Preusner
	 * @version 2.1
	 * @since   2015-05
	 *
	 * @param   ResponseInterface $response
	 */
    public function __construct(ResponseInterface $response) {

        $this->save($response);
    } // end: __construct

	/**
	 * Save data
	 *
	 * @author  Florian Preusner
	 * @version 2.1
	 * @since   2015-05
	 *
	 * @param   ResponseInterface $request
	 */
	public function save(ResponseInterface $request) {

		$this->setHeaders($request->getHeaders());
	} // end: save()

	/**
	 * Set response headers
	 *
	 * @author  Florian Preusner
	 * @version 2.1
	 * @since   2015-05
	 *
	 * @param   array $value
	 */
	public function setHeaders(array $value) {

		$this->headers = $value;
	} // end: setHeaders()

	/**
	 * Return response headers
	 *
	 * @author  Florian Preusner
	 * @version 2.1
	 * @since   2015-05
	 *
	 * @return  array
	 */
	public function getHeaders() {

		return $this->headers;
	} // end: getHeaders()
} // end: LogResponse
