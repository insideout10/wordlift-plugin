<?php
/**
 * Taxonomy Term JSON-LD Adapter.
 *
 * The {@link Wordlift_Term_JsonLd_Adapter} intercepts calls to terms' pages and loads the related JSON-LD in page.
 *
 * @since 3.20.0
 * @package Wordlift
 * @subpackage Wordlift/public
 */

/**
 * Define the {@link Wordlift_Term_JsonLd_Adapter} class.
 *
 * @since 3.20.0
 */
class Wordlift_Term_JsonLd_Adapter {

	/**
	 * The {@link Wordlift_Entity_Uri_Service} instance.
	 *
	 * @since 3.20.0
	 * @access private
	 * @var \Wordlift_Entity_Uri_Service $entity_uri_service The {@link Wordlift_Entity_Uri_Service} instance.
	 */
	private $entity_uri_service;

	/**
	 * The {@link Wordlift_Jsonld_Service} instance.
	 *
	 * @since 3.20.0
	 * @access private
	 * @var \Wordlift_Jsonld_Service $jsonld_service The {@link Wordlift_Jsonld_Service} instance.
	 */
	private $jsonld_service;

	/**
	 * Wordlift_Term_JsonLd_Adapter constructor.
	 *
	 * @since 3.20.0
	 *
	 * @param \Wordlift_Entity_Uri_Service $entity_uri_service The {@link Wordlift_Entity_Uri_Service} instance.
	 * @param \Wordlift_Jsonld_Service     $jsonld_service The {@link Wordlift_Jsonld_Service} instance.
	 */
	public function __construct( $entity_uri_service, $jsonld_service ) {

		add_action( 'wp_head', array( $this, 'wp_head' ) );

		$this->entity_uri_service = $entity_uri_service;
		$this->jsonld_service     = $jsonld_service;

	}

	/**
	 * Hook to `wp_head` to print the JSON-LD.
	 *
	 * @since 3.20.0
	 */
	public function wp_head() {

		// Bail out if it's not a category page.
		if ( ! is_tax() && ! is_category() ) {
			return;
		}

		$term    = get_queried_object();
		$term_id = $term->term_id;

		// The `_wl_entity_id` are URIs.
		$entity_ids         = get_term_meta( $term_id, '_wl_entity_id' );
		$entity_uri_service = $this->entity_uri_service;

		$local_entity_ids = array_filter( $entity_ids, function ( $uri ) use ( $entity_uri_service ) {
			return $entity_uri_service->is_internal( $uri );
		} );

		// Bail out if there are no entities.
		if ( empty( $local_entity_ids ) ) {
			return;
		}

		$post   = $this->entity_uri_service->get_entity( $local_entity_ids[0] );
		$jsonld = $this->jsonld_service->get_jsonld( false, $post->ID );
		// Reset the `url` to the term page.
		$jsonld[0]['url'] = get_term_link( $term_id );
		$jsonld_string    = wp_json_encode( $jsonld );

		echo "<script type=\"application/ld+json\">$jsonld_string</script>";

	}

}
