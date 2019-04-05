<?php
/**
 * Ajax Adapter: Locate.
 *
 * Provides an Ajax end-ppint (both admin and public) to locate an entity given its URI.
 *
 * @see https://github.com/insideout10/wordlift-plugin/issues/907
 *
 * @since 3.20.1
 */

/**
 * Define the {@link Wordlift_Locate_Ajax_Adapter} class.
 *
 * @since 3.20.1
 */
class Wordlift_Locate_Ajax_Adapter {

	/**
	 * The {@link Wordlift_Entity_Uri_Service} instance.
	 *
	 * @since 3.20.1
	 * @access private
	 * @var Wordlift_Entity_Uri_Service $entity_uri_service
	 */
	private $entity_uri_service;

	/**
	 * Create a {@link Wordlift_Locate_Ajax_Adapter} instance.
	 *
	 * @param Wordlift_Entity_Uri_Service $entity_uri_service The {@link Wordlift_Entity_Uri_Service} instance.
	 *
	 * @since 3.20.1
	 */
	public function __construct( $entity_uri_service ) {

		$this->entity_uri_service = $entity_uri_service;

		add_action( 'wp_ajax_wl_locate', array( $this, 'locate' ) );
		add_action( 'wp_ajax_nopriv_wl_locate', array( $this, 'locate' ) );

	}

	/**
	 * Locate the URI provided via the `u` parameter and redirect to it, or `wp_die` in case the provided
	 * parameter is not a valid URL or it is not found.
	 *
	 * @see https://github.com/insideout10/wordlift-plugin/issues/907
	 *
	 * @since 3.20.1
	 */
	public function locate() {

		// Check if the `u` parameter has been provided and it's a URL.
		if ( ! ( $uri = filter_input( INPUT_GET, "u", FILTER_VALIDATE_URL ) ) ) {

			wp_die( __( 'Invalid URL.', 'wordlift' ), __( 'Invalid URL.', 'wordlift' ), array(
				'response'  => 400,
				'back_link' => true,
			) );

		}

		// Check that the URI matches an entity.
		if ( ! ( $entity = $this->entity_uri_service->get_entity( $uri ) ) ) {

			wp_die( __( 'Entity not found.', 'wordlift' ), __( 'Entity not found.', 'wordlift' ), array(
				'response'  => 404,
				'back_link' => true,
			) );

		}
		wp_redirect( get_permalink( $entity ) );
		exit;

	}

}
