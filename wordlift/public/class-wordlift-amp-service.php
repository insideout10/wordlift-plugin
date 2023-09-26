<?php
/**
 * Services: AMP Services.
 *
 * The file defines a class for AMP related manipulations.
 *
 * @link       https://wordlift.io
 *
 * @since      3.12.0
 *
 * @package    Wordlift
 * @subpackage Wordlift/public
 */

/**
 * Handles AMP related manipulation in the generated HTML of entity pages
 *
 * @since      3.12.0
 * @package    Wordlift
 * @subpackage Wordlift/public
 */
class Wordlift_AMP_Service {

	/**
	 * The {@link \Wordlift_Jsonld_Service} instance.
	 *
	 * @since 3.19.1
	 * @access private
	 * @var \Wordlift_Jsonld_Service $jsonld_service The {@link \Wordlift_Jsonld_Service} instance.
	 */
	private $jsonld_service;

	/**
	 * Create a {@link Wordlift_AMP_Service} instance.
	 *
	 * @since 3.19.1
	 *
	 * @param \Wordlift_Jsonld_Service $jsonld_service
	 */
	public function __construct( $jsonld_service ) {

		$this->jsonld_service = $jsonld_service;

		add_action( 'amp_init', array( $this, 'register_entity_cpt_with_amp_plugin' ) );
		add_filter( 'amp_post_template_metadata', array( $this, 'amp_post_template_metadata' ), 99, 2 );

	}

	/**
	 * Register the `entity` post type with the AMP plugin.
	 *
	 * @since 3.12.0
	 */
	public function register_entity_cpt_with_amp_plugin() {

		if ( ! defined( 'AMP_QUERY_VAR' ) ) {
			return;
		}

		foreach ( Wordlift_Entity_Service::valid_entity_post_types() as $post_type ) {
			// Do not change anything for posts and pages.
			if ( 'post' === $post_type || 'page' === $post_type ) {
				continue;
			}
			add_post_type_support( $post_type, AMP_QUERY_VAR );
		}

	}

	/**
	 * Filters Schema.org metadata for a post.
	 *
	 * @since 3.19.1
	 *
	 * @param array   $metadata Metadata.
	 * @param WP_Post $post Post.
	 *
	 * @return array Return WordLift's generated JSON-LD.
	 */
	public function amp_post_template_metadata( $metadata, $post ) {

		return $this->jsonld_service->get_jsonld( false, $post->ID );
	}

	/**
	 * Check if current page is amp endpoint.
	 *
	 * @since 3.20.0
	 *
	 * @return bool
	 */
	public static function is_amp_endpoint() {
		return function_exists( 'is_amp_endpoint' ) && is_amp_endpoint();
	}

}
