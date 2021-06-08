<?php
/**
 * Provides functions and AJAX endpoints to support redirects needed by the client-side layer.
 *
 * @since 3.2.0
 * @since 3.20.1 use `filter_input` to access the $_GET variables.
 *
 * @package Wordlift
 * @subpackage Wordlift/includes
 */
class Wordlift_Redirect_Service {

	const LOD_ENDPOINT = 'http://www.lodview.it';
	const LOD_HOST = 'www.lodview.it';

	/**
	 * The Log service.
	 *
	 * @since 3.2.0
	 * @access private
	 * @var \Wordlift_Log_Service $log_service The Log service.
	 */
	private $log_service;

	/**
	 * The Entity URI service.
	 *
	 * @since 3.2.0
	 * @access private
	 * @var Wordlift_Entity_Uri_Service $entity_uri_service The Entity service.
	 */
	private $entity_uri_service;

	/**
	 * A singleton instance of the Redirect service (useful for unit tests).
	 *
	 * @since 3.2.0
	 * @access private
	 * @var \Wordlift_Redirect_Service $instance The singleton instance.
	 */
	private static $instance;

	/**
	 * Create a Wordlift_Redirect_Service instance.
	 *
	 * @param Wordlift_Entity_Uri_Service $entity_uri_service The Entity service.
	 *
	 * @since 3.2.0
	 *
	 */
	public function __construct( $entity_uri_service ) {

		$this->log_service = Wordlift_Log_Service::get_logger( 'Wordlift_Redirect_Service' );

		$this->entity_uri_service = $entity_uri_service;

		self::$instance = $this;

	}

	/**
	 * Get the singleton instance of the Wordlift_Redirect_Service
	 *
	 * @return \Wordlift_Redirect_Service The singleton instance of the Wordlift_Redirect_Service.
	 * @since 3.2.0
	 *
	 */
	public static function get_instance() {

		return self::$instance;
	}

	/**
	 * Perform redirect depending on entity uri and target
	 *
	 * @since 3.2.0
	 */
	public function ajax_redirect() {

		// Check the `uri` parameter.
		if ( ! ( $entity_uri = filter_input( INPUT_GET, 'uri', FILTER_VALIDATE_URL ) ) ) {
			wp_die( __( 'Invalid URI.', 'wordlift' ), __( 'Invalid URI.', 'wordlift' ), array(
				'response'  => 400,
				'back_link' => true,
			) );
		}

		// Check the `to` parameter.
		if ( ! ( $target = filter_input( INPUT_GET, 'to' ) ) ) {
			wp_die( __( 'Invalid `to` parameter.', 'wordlift' ), __( 'Invalid `to` parameter.', 'wordlift' ), array(
				'response'  => 400,
				'back_link' => true,
			) );
		}

		// Check if the entity exists.
		if ( ! ( $entity = $this->entity_uri_service->get_entity( $entity_uri ) ) ) {
			wp_die( __( 'Entity not found.', 'wordlift' ), __( 'Entity not found.', 'wordlift' ), array(
				'response'  => 404,
				'back_link' => true,
			) );
		}

		switch ( $target ) {
			case 'edit':
				$redirect_url = get_edit_post_link( $entity, 'none' );
				break;
			case 'lod':
				$redirect_url = self::LOD_ENDPOINT . '/lodview/?IRI=' . urlencode( $entity_uri );
				break;
			case 'permalink':
				$redirect_url = get_permalink( $entity );
				break;
			default:
				wp_die( 'Unsupported redirect target.' );
		}

		// Perform the redirect
		wp_safe_redirect( $redirect_url );
		exit;
	}

	/**
	 * Register custom allowed hosts.
	 * @see https://developer.wordpress.org/reference/functions/wp_safe_redirect/
	 *
	 * @since 3.2.0
	 *
	 * @param int $entity_id A post entity id.
	 *
	 * @return array permalink.
	 */
	public function allowed_redirect_hosts( $content ) {

		return array_merge( $content, array( self::LOD_HOST ) );
	}

}
