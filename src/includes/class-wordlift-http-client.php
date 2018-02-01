<?php
/**
 * Services: HTTP Client.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * Define the {@link Wordlift_HTTP_Client} class.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/includes
 */
class Wordlift_HTTP_Client {
	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.18.0
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * The {@link Wordlift_Configuration_Service} instance.
	 *
	 * @since  3.18.0
	 * @access private
	 * @var \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 */
	public $configuration_service;

	/**
	 * Wordlift_HTTP_Client constructor.
	 *
	 * @since 3.18.0
	 *
	 * @param \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 */
	public function __construct( $configuration_service ) {

		$this->log = Wordlift_Log_Service::get_logger( get_class() );

		$this->configuration_service = $configuration_service;

	}

	/**
	 * Make request to the server.
	 *
	 * @since 3.18.0
	 *
	 * @param string $url    The request url.
	 * @param string $method The request method.
	 * @param array  $params Request params.
	 *
	 * @return array The API response.
	 */
	public function do_request( $url, $method = 'GET', $params = array() ) {
		// Add the WL key to keywords url.
		$new_url = add_query_arg(
			array(
				'key' => $this->configuration_service->get_key(),
			),
			$url
		);

		// Get the HTTP options.
		$args = array_merge_recursive(
			unserialize( WL_REDLINK_API_HTTP_OPTIONS ),
			array(
				'method'      => $method,
				'headers'     => array(
					'Accept'       => 'application/json',
					'Content-type' => 'application/json; charset=UTF-8',
				),
				'httpversion' => '1.1',
				'body'        => json_encode( $params ),
			)
		);

		// Post the parameter.
		return wp_remote_request( $new_url, $args );
	}
}
