<?php
/**
 * Taxonomy JSON LD adapter, intercepts the call to taxonomy page
 * and loads related json ld for the taxonomy.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @package Wordlift\Jsonld
 */
class Wordlift_Taxonomy_JsonLd_Adapter {
	/**
	 * The {@link Wordlift_Entity_Uri_Service} instance.
	 *
	 * @since 3.26.0
	 * @access private
	 * @var \Wordlift_Entity_Uri_Service $entity_uri_service The {@link Wordlift_Entity_Uri_Service} instance.
	 */
	private $entity_uri_service;

	/**
	 * The {@link Wordlift_Jsonld_Service} instance.
	 *
	 * @since 3.26.0
	 * @access private
	 * @var \Wordlift_Jsonld_Service $jsonld_service The {@link Wordlift_Jsonld_Service} instance.
	 */
	private $jsonld_service;

	/**
	 * Wordlift_Term_JsonLd_Adapter constructor.
	 *
	 * @since 3.26.0
	 *
	 * @param \Wordlift_Entity_Uri_Service $entity_uri_service The {@link Wordlift_Entity_Uri_Service} instance.
	 * @param \Wordlift_Jsonld_Service     $jsonld_service The {@link Wordlift_Jsonld_Service} instance.
	 */
	public function __construct( $entity_uri_service, $jsonld_service ) {

		add_action( 'wp_head', array( $this, 'wp_head' ) );
		$this->entity_uri_service = $entity_uri_service;
		$this->jsonld_service     = $jsonld_service;

	}
	public function wp_head() {

	}

}