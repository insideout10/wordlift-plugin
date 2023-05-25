<?php
/**
 * This file is included only if WordPress is set in DEBUG mode and provides
 * debugging features.
 *
 * @since 3.7.2
 */

/**
 * Define the Wordlift_Debug_Service class.
 *
 * @since 3.7.2
 */
class Wordlift_Debug_Service {

	/**
	 * The {@link Wordlift_Entity_Service} instance.
	 *
	 * @since  3.7.2
	 * @access private
	 * @var Wordlift_Entity_Service $entity_service The {@link Wordlift_Entity_Service} instance.
	 */
	private $entity_service;

	/**
	 * A {@link Wordlift_Uri_Service} instance.
	 *
	 * @since  3.10.0
	 * @access private
	 * @var \Wordlift_Uri_Service $uri_service A {@link Wordlift_Uri_Service} instance.
	 */
	private $uri_service;

	/**
	 * Wordlift_Debug_Service constructor.
	 *
	 * @since 3.7.2
	 *
	 * @param Wordlift_Entity_Service $entity_service The {@link Wordlift_Entity_Service} instance.
	 * @param \Wordlift_Uri_Service   $uri_service    A {@link Wordlift_Uri_Service} instance.
	 */
	public function __construct( $entity_service, $uri_service ) {

		$this->entity_service = $entity_service;
		$this->uri_service    = $uri_service;

		add_action( 'wp_ajax_wl_dump_uri', array( $this, 'dump_uri' ) );

	}

	public function dump_uri() {

		if ( ! isset( $_GET['id'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			wp_send_json_error( 'id not set' );
		}

		$post_id = (int) $_GET['id']; //phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$post = get_post( $post_id );

		$uri       = $this->entity_service->get_uri( $post_id );
		$build_uri = $this->uri_service->build_uri( $post->post_title, $post->post_type );

		wp_send_json_success(
			array(
				'uri'               => $uri,
				'post_title'        => sprintf( '%s (%s)', $post->post_title, mb_detect_encoding( $post->post_title ) ),
				'post_title_ascii'  => mb_convert_encoding( $post->post_title, 'ASCII' ),
				'build_uri'         => $build_uri,
				'build_uri_convert' => mb_convert_encoding( $build_uri, 'ASCII' ),
			)
		);

	}

}
