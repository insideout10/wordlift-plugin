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
	 * @param $jsonld array JsonLd Array.
	 *
	 * @return array
	 */
	public function get_carousel_jsonld( $jsonld, $id = null ) {
		$posts = $this->get_posts( $id );

		if ( ! is_array( $posts ) ) {
			// Bail out if no posts are present.
			return $jsonld;
		}

		$entities = array();

		if ( count( $posts ) < 2 ) {
			return $jsonld;
		}

		// More than 2 items are present, so construct the jsonld data
		$jsonld['@context']        = 'https://schema.org';
		$jsonld['@type']           = 'ItemList';
		$jsonld['url']             = $this->get_term_url( $id );
		$jsonld['itemListElement'] = array();
		$position                  = 1;

		foreach ( $posts as $post_id ) {
			$post_jsonld = $this->jsonld_service->get_jsonld( false, $post_id );

			array_push( $jsonld['itemListElement'], array(
				'@type'    => 'ListItem',
				'position' => $position,
				/**
				 * We can't use `item` here unless we change the URL for the item to point to the current page.
				 *
				 * See https://developers.google.com/search/docs/data-types/carousel
				 */
				// 'item'     => array_shift( $post_jsonld )
				'url'      => $post_jsonld[0]['url'],
			) );

			$entities = array_merge( $entities, $post_jsonld );
			$position += 1;
		}

		return array(
			'post_jsonld' => $jsonld,
			'entities'    => array()
		);
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
		// Bail out if it's not a category page.
		if ( ! is_tax() && ! is_category() ) {
			return;
		}

		$query_object = get_queried_object();
		$term_id      = $query_object->term_id;
		$jsonld       = $this->get( $term_id );

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
		$carousel_data = $this->get_carousel_jsonld( array(), $id );

		// If the carousel jsonld returns empty array, then fallback to previous jsonld generation.
		if ( isset( $carousel_data['entities'] )
		     && isset( $carousel_data['post_jsonld'] )
		     && $carousel_data['post_jsonld'] !== array() ) {

			$entities    = $carousel_data['entities'];
			$post_jsonld = $carousel_data['post_jsonld'];

			$jsonld = array( $post_jsonld );
			$jsonld = array_merge( $jsonld, $entities );
		} else {
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

			$post   = $this->entity_uri_service->get_entity( $local_entity_ids[0] );
			$jsonld = $this->jsonld_service->get_jsonld( false, $post->ID );
			// Reset the `url` to the term page.
			$jsonld[0]['url'] = get_term_link( $id );
		}

		return $jsonld;
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

}
