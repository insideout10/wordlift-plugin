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
	 * Allowed targets are 'edit', 'lod', 'permalink'
	 *
	 * @since 3.2.0
	 */
	public function ajax_redirect() {

		// Get the entity uri of the current entity.
		$entity_uri = ( isset( $_GET['uri'] ) ? $_GET['uri'] : null );
		// Get the entity id
		if ( $entity_id = $this->entity_service->get_entity_post_by_uri( $entity_uri ) ) {
			// Get the target
			$target = ( isset( $_GET['to'] ) ? $_GET['to'] : '' );
			// Get the current target method
			$method = "redirect_to_{$target}";
			// Check if the target is valid
			if ( method_exists( $this, $method ) ) {
				// Prepare arguments 
				$args = array( 
					'uri' => $entity_uri,
					'id'  => $entity_id,	 
					);
				// Retrieve redirect url
				$redirect_url = call_user_func( array( $this, $method ), $args );
				// Perform the redirect
				wp_safe_redirect( $redirect_url );
			} else {
				// Unsupported target: exit with an error
				wp_die( "Unsupported target {$target}" );
			}	
		}
		// Unexisting entity: exit with an error
		wp_die( "Does not exist an entity with uri {$entity_uri}" );
	}

	/**
	 * Get entity edit link.
	 *
	 * @since 3.2.0
	 *
	 * @param int $entity_id A post entity id.
	 *
	 * @return string Edit link.
	 */
	public function redirect_to_edit( $args ) {

		return get_edit_post_link( $args[ 'id' ], 'none' );
	}

	/**
	 * Get entity lod link.
	 *
	 * @since 3.2.0
	 *
	 * @param int $entity_id A post entity id.
	 *
	 * @return string Lod link.
	 */
	public function redirect_to_lod( $args ) {

		return self::LOD_ENDPOINT . "/lodview/?IRI=" . urlencode( $args[ 'uri' ] );
	}

	/**
	 * Get entity permalink.
	 *
	 * @since 3.2.0
	 *
	 * @param int $entity_id A post entity id.
	 *
	 * @return string permalink.
	 */
	public function redirect_to_permalink( $args ) {

		return get_permalink( $args[ 'id' ] );
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

		$content[] = self::LOD_HOST;
		return $content;
	}

}
