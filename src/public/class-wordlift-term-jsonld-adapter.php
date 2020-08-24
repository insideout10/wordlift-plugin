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

	private static $instance;

	/**
	 * Wordlift_Term_JsonLd_Adapter constructor.
	 *
	 * @param \Wordlift_Entity_Uri_Service $entity_uri_service The {@link Wordlift_Entity_Uri_Service} instance.
	 * @param \Wordlift_Jsonld_Service $jsonld_service The {@link Wordlift_Jsonld_Service} instance.
	 *
	 * @since 3.20.0
	 *
	 */
	public function __construct( $entity_uri_service, $jsonld_service ) {

		add_action( 'wp_head', array( $this, 'wp_head' ) );

		$this->entity_uri_service = $entity_uri_service;
		$this->jsonld_service     = $jsonld_service;

		self::$instance = $this;
	}

	public static function get_instance() {

		return self::$instance;
	}

	/**
	 * Adds carousel json ld data to term page if the conditions match
	 *
	 * @return array|boolean
	 */
	public function get_carousel_jsonld( $id = null ) {
		$posts       = $this->get_posts( $id );
		$post_jsonld = array();
		if ( ! is_array( $posts ) || count( $posts ) < 2 ) {
			// Bail out if no posts are present.
			return false;
		}

		if ( ! is_null( $id ) ) {
			$term                       = get_term( $id );
			$post_jsonld['description'] = $term->description;
			$thumbnail_id               = get_term_meta( $id, 'thumbnail_id', true );
			if ( ! empty( $thumbnail_id ) ) {
				$post_jsonld['image'] = wp_get_attachment_url( $thumbnail_id );
			}
		}

		// More than 2 items are present, so construct the post_jsonld data
		$post_jsonld['@context']        = 'https://schema.org';
		$post_jsonld['@type']           = 'ItemList';
		$post_jsonld['url']             = $this->get_term_url( $id );
		$post_jsonld['itemListElement'] = array();
		$position                       = 1;

		foreach ( $posts as $post_id ) {
			$result = array(
				'@type'    => 'ListItem',
				'position' => $position,
				/**
				 * We can't use `item` here unless we change the URL for the item to point to the current page.
				 *
				 * See https://developers.google.com/search/docs/data-types/carousel
				 */
				'url'      => get_permalink( $post_id )
			);
			array_push( $post_jsonld['itemListElement'], $result );
			$position += 1;
		}

		return $post_jsonld;
	}

	private function get_posts( $id ) {
		global $wp_query;

		if ( ! is_null( $wp_query->posts ) ) {
			return array_map( function ( $post ) {
				return $post->ID;
			}, $wp_query->posts );
		}

		if ( is_null( $id ) ) {
			return null;
		}

		$term = get_term( $id );

		return get_objects_in_term( $id, $term->taxonomy );
	}

	/**
	 * Hook to `wp_head` to print the JSON-LD.
	 *
	 * @since 3.20.0
	 */
	public function wp_head() {
		$query_object = get_queried_object();

		// Check if it is a term page.
		if ( ! $query_object instanceof WP_Term ) {
			return;
		}

		$term_id = $query_object->term_id;

		$jsonld  = $this->get( $term_id );

		// Bail out if the JSON-LD is empty.
		if ( empty( $jsonld ) ) {
			return;
		}

		$jsonld_string = wp_json_encode( $jsonld );

		echo "<script type=\"application/ld+json\">$jsonld_string</script>";

	}

	public function get( $id ) {

		/**
		 * Support for carousel rich snippet, get jsonld data present
		 * for all the posts shown in the term page, and add the jsonld data
		 * to list
		 *
		 * see here: https://developers.google.com/search/docs/data-types/carousel
		 *
		 * @since 3.26.0
		 */
		$carousel_data = $this->get_carousel_jsonld( $id );
		$jsonld_array  = array();
		if ( $carousel_data ) {
			$jsonld_array[] = $carousel_data;
		}
		$entities_jsonld_array = $this->get_entity_jsonld( $id );

		$result = array(
			'jsonld'     => array_merge( $jsonld_array, $entities_jsonld_array ),
			'references' => array()
		);

		/**
		 * @since 3.26.3
		 * Filter: wl_term_jsonld_array
		 * @var $id int Term id
		 * @var $jsonld_array array An array containing jsonld for term and entities.
		 */
		$arr = apply_filters( 'wl_term_jsonld_array', $result, $id );

		return $arr['jsonld'];
	}

	private function get_term_url( $id ) {

		if ( is_null( $id ) ) {
			return $_SERVER['REQUEST_URI'];
		}

		$maybe_url = get_term_meta( $id, Wordlift_Url_Property_Service::META_KEY, true );
		if ( ! empty( $maybe_url ) ) {
			return $maybe_url;
		}

		return get_term_link( $id );
	}

	/**
	 * Return jsonld for entities bound to terms.
	 *
	 * @param $id
	 *
	 * @return array
	 */
	private function get_entity_jsonld( $id ) {
		// The `_wl_entity_id` are URIs.
		$entity_ids         = get_term_meta( $id, '_wl_entity_id' );
		$entity_uri_service = $this->entity_uri_service;

		$local_entity_ids = array_filter( $entity_ids, function ( $uri ) use ( $entity_uri_service ) {
			return $entity_uri_service->is_internal( $uri );
		} );

		// Bail out if there are no entities.
		if ( empty( $local_entity_ids ) ) {
			return array();
		}

		$post   = $this->entity_uri_service->get_entity( array_shift( $local_entity_ids ) );
		$jsonld = $this->jsonld_service->get_jsonld( false, $post->ID );
		// Reset the `url` to the term page.
		$jsonld[0]['url'] = get_term_link( $id );

		return $jsonld;
	}

}
