<?php
/**
 * Define the Wordlift_Jsonld_Service class to support JSON-LD.
 *
 * @since   3.8.0
 * @package Wordlift
 */

/**
 * This class exports an entity using JSON-LD.
 *
 * @since 3.8.0
 */
class Wordlift_Jsonld_Service {

	/**
	 * A {@link Wordlift_Entity_Service} instance.
	 *
	 * @since  3.8.0
	 * @access private
	 * @var Wordlift_Entity_Service $entity_service A {@link Wordlift_Entity_Service} instance.
	 */
	private $entity_service;

	/**
	 * A {@link Wordlift_Entity_To_Jsonld_Converter} instance.
	 *
	 * @since  3.8.0
	 * @access private
	 * @var \Wordlift_Entity_Post_To_Jsonld_Converter A {@link Wordlift_Entity_To_Jsonld_Converter} instance.
	 */
	private $entity_to_jsonld_converter;

	/**
	 * Create a JSON-LD service.
	 *
	 * @since 3.8.0
	 *
	 * @param \Wordlift_Entity_Service                  $entity_service             A {@link Wordlift_Entity_Service} instance.
	 * @param \Wordlift_Entity_Post_To_Jsonld_Converter $entity_to_jsonld_converter A {@link Wordlift_Entity_Post_To_Jsonld_Converter} instance.
	 */
	public function __construct( $entity_service, $entity_to_jsonld_converter ) {

		$this->entity_service             = $entity_service;
		$this->entity_to_jsonld_converter = $entity_to_jsonld_converter;

		add_action( 'wp_footer', array( $this, 'wp_footer' ), PHP_INT_MAX );
	}

	/**
	 * Hook to WP's wp_footer action and load the JSON-LD data.
	 *
	 * @since 3.8.0
	 */
	public function wp_footer() {

		// We only care about singular pages.
		if ( ! is_singular() ) {
			return;
		}

		// Get the entities related to the current post (and that are published).
		$post_id = get_the_ID();
		$posts   = $this->entity_service->is_entity( $post_id )
			? array( get_the_ID() )
			: array_unique( wl_core_get_related_entity_ids( $post_id, array(
				'status' => 'publish',
			) ) );

		// Build the URL to load the JSON-LD asynchronously.
		$url            = admin_url( 'admin-ajax.php?action=wl_jsonld' );
		$entity_service = $this->entity_service;
		$data           = implode( '&', array_map( function ( $item ) use ( $entity_service ) {
			return 'uri[]=' . rawurldecode( $entity_service->get_uri( $item ) );
		}, $posts ) );

		// Print the Javascript code.
		echo <<<EOF
<script type="text/javascript"><!--
(function($) { $( window ).on( 'load', function() { $.post('$url','$data').done(function(data) {
	$('head').append( '<script type="application/ld+json">'+JSON.stringify(data)+'</s' + 'cript>' );
}); }); })(jQuery);
// --></script>
EOF;
	}

	/**
	 * Process calls to the AJAX 'wl_jsonld' endpoint.
	 *
	 * @since 3.8.0
	 */
	public function get() {

		// If no URI has been provided return an empty array.
		if ( ! isset( $_REQUEST['uri'] ) ) {
			wp_send_json( array() );
		}

		// Get an array of URIs to parse.
		$uris = is_array( $_REQUEST['uri'] ) ? $_REQUEST['uri'] : array( $_REQUEST['uri'] );

		// An array of references which is captured when converting an URI to a
		// json which we gather to further expand our json-ld.
		$references = array();

		// Set a reference to the entity_to_jsonld_converter to use in the closures.
		$entity_to_jsonld_converter = $this->entity_to_jsonld_converter;

		// Convert each URI to a JSON-LD array, while gathering referenced entities.
		// in the references array.
		$jsonld = array_merge(
			array_map( function ( $item ) use ( $entity_to_jsonld_converter, &$references ) {

				return $entity_to_jsonld_converter->convert( $item, $references );
			}, $uris ),
			// Convert each URI in the references array to JSON-LD. We don't output
			// entities already output above (hence the array_diff).
			array_map( function ( $item ) use ( $entity_to_jsonld_converter, &$references ) {

				return $entity_to_jsonld_converter->convert( $item, $references );
			}, array_diff( $references, $uris ) )
		);

		// Finally send the JSON-LD.
		wp_send_json( $jsonld );

	}

}
