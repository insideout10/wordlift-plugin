<?php

/**
 * Provides functions and AJAX endpoints to support redirects needed by the client-side layer.
 *
 * @since 3.2.0
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
	 * The Entity service.
	 *
	 * @since 3.2.0
	 * @access private
	 * @var \Wordlift_Entity_Service $entity_service The Entity service.
	 */
	private $entity_service;

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
	 * @since 3.2.0
	 *
	 * @param \Wordlift_Entity_Service $entity_service The Entity service.
	 */
	public function __construct( $entity_service ) {

		$this->log_service = Wordlift_Log_Service::get_logger( 'Wordlift_Redirect_Service' );

		$this->entity_service = $entity_service;

		self::$instance = $this;

	}

	/**
	 * Get the singleton instance of the Wordlift_Redirect_Service
	 *
	 * @since 3.2.0
	 *
	 * @return \Wordlift_Redirect_Service The singleton instance of the Wordlift_Redirect_Service.
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

		if ( !isset( $_GET['uri'] ) ) {
			wp_die( 'Entity uri missing' );	
		}
		if ( !isset( $_GET['to'] ) ) {
			wp_die( 'Redirect target missing' );	
		}
		// Get the entity uri
		$entity_uri = $_GET['uri'];
		// Get the redirect target
		$target = $_GET['to'];
		
		if ( null === ( $entity_id = $this->entity_service->get_entity_post_by_uri( $entity_uri ) ) ) {
    		wp_die( 'Entity not found' );
		}

		switch ( $target ) {
			case 'edit':
				$redirect_url = get_edit_post_link( $entity_id, 'none' );
				break;
			case 'lod':
				$redirect_url = self::LOD_ENDPOINT . '/lodview/?IRI=' . urlencode( $entity_uri );
				break;
			case 'permalink':
				$redirect_url = get_permalink( $entity_id );
				break;
			default:
 				wp_die( 'Unsupported redirect target' );
		}

		// Perform the redirect
		wp_safe_redirect( $redirect_url );
	}

	/**
	 * Register custom allowed hosts.
	 * @see https://developer.wordpress.org/reference/functions/wp_safe_redirect/
	 *
	 * @since 3.2.0
	 *
	 * @param int $entity_id A post entity id.
	 *
	 * @return string permalink.
	 */
	public function allowed_redirect_hosts( $content ) {

		return array_merge( $content, array( self::LOD_HOST ) );
	}

}
