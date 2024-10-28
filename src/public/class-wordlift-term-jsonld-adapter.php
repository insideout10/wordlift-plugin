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

use Wordlift\Jsonld\Jsonld_Context_Enum;
use Wordlift\Jsonld\Post_Reference;
use Wordlift\Jsonld\Term_Reference;
use Wordlift\Relation\Relations;

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
	 * Instance.
	 *
	 * @var Wordlift_Term_JsonLd_Adapter
	 */
	private static $instance;

	/**
	 * @var Wordlift_Post_Converter
	 */
	private $post_id_to_jsonld_converter;

	/**
	 * Wordlift_Term_JsonLd_Adapter constructor.
	 *
	 * @param \Wordlift_Entity_Uri_Service $entity_uri_service The {@link Wordlift_Entity_Uri_Service} instance.
	 * @param \Wordlift_Post_Converter     $post_id_to_jsonld_converter The {@link Wordlift_Post_Converter} instance.
	 *
	 * @since 3.20.0
	 */
	public function __construct( $entity_uri_service, $post_id_to_jsonld_converter ) {

		add_action( 'wp_head', array( $this, 'wp_head' ) );

		$this->entity_uri_service          = $entity_uri_service;
		$this->post_id_to_jsonld_converter = $post_id_to_jsonld_converter;

		self::$instance = $this;
	}

	/**
	 * Get instance.
	 *
	 * @return Wordlift_Term_JsonLd_Adapter|static
	 */
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

		if ( $id !== null ) {
			$term                       = get_term( $id );
			$post_jsonld['description'] = wp_strip_all_tags( strip_shortcodes( $term->description ) );
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
				'url'      => apply_filters( 'wl_carousel_post_list_item_url', get_permalink( $post_id ), $post_id ),
			);
			array_push( $post_jsonld['itemListElement'], $result );
			++ $position;
		}

		return $post_jsonld;
	}

	/**
	 * Get posts.
	 *
	 * @param $id
	 *
	 * @return array|int[]|string[]|WP_Error|null
	 */
	private function get_posts( $id ) {
		global $wp_query;

		if ( is_array( $wp_query->posts ) ) {
			return array_map(
				function ( $post ) {
					return $post->ID;
				},
				$wp_query->posts
			);
		}

		if ( $id === null ) {
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

		// Bail out if `wl_jsonld_enabled` isn't enabled.
		if ( ! apply_filters( 'wl_jsonld_enabled', true ) ) {
			return;
		}

		$term_id = $query_object->term_id;

		$jsonld = $this->get( $term_id, Jsonld_Context_Enum::PAGE );

		// Bail out if the JSON-LD is empty.
		if ( empty( $jsonld ) ) {
			return;
		}

		$jsonld_string = wp_json_encode( $jsonld );

		$jsonld_term_html_output = '<script type="application/ld+json" id="wl-jsonld-term">' . $jsonld_string . '</script>';
		$jsonld_term_html_output = apply_filters( 'wl_jsonld_term_html_output', $jsonld_term_html_output, $term_id );

		echo $jsonld_term_html_output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- It's an application/ld+json output.

	}

	/**
	 * Get.
	 *
	 * @param $id
	 * @param $context
	 * @param $is_recursive_call
	 *
	 * @return array
	 */
	public function get( $id, $context, $is_recursive_call = false ) {
		/**
		 * Support for carousel rich snippet, get jsonld data present
		 * for all the posts shown in the term page, and add the jsonld data
		 * to list
		 *
		 * see here: https://developers.google.com/search/docs/data-types/carousel
		 *
		 * @since 3.26.0
		 */
		$jsonld_array = array();

		if ( Jsonld_Context_Enum::PAGE === $context ) {
			$carousel_data = $this->get_carousel_jsonld( $id );
			if ( $carousel_data ) {
				$jsonld_array[] = $carousel_data;
			}
		}

		$entities_jsonld_array = $this->get_entity_jsonld( $id, $context );

		$result = array(
			'jsonld'     => array_merge( $jsonld_array, $entities_jsonld_array ),
			'references' => array(),
		);

		/**
		 * @since 3.26.3
		 * Filter: wl_term_jsonld_array
		 * @var $id int Term id
		 * @var $jsonld_array array An array containing jsonld for term and entities.
		 * @var $context int A context for the JSON-LD generation, valid values in Jsonld_Context_Enum
		 */
		$arr = apply_filters( 'wl_term_jsonld_array', $result, $id, $context );

		$references = array();

		// Don't expand nested references, it will lead to an infinite loop.
		if ( ! $is_recursive_call ) {
			/**
			 * @since 3.32.0
			 * Expand the references returned by this filter.
			 */
			$references = $this->expand_references( $arr['references'] );
		}

		$jsonld_array = array_merge( $arr['jsonld'], $references );

		return $jsonld_array;
	}

	/**
	 * Get term url.
	 *
	 * @param $id
	 *
	 * @return array|false|int|mixed|string|WP_Error|WP_Term|null
	 */
	private function get_term_url( $id ) {
		if ( null === $id ) {
			return isset( $_SERVER['REQUEST_URI'] ) ? filter_var( wp_unslash( $_SERVER['REQUEST_URI'] ), FILTER_SANITIZE_URL ) : '';
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
	 * @param int $term_id Term ID.
	 * @param int $context A context for the JSON-LD generation, valid values in Jsonld_Context_Enum.
	 *
	 * @return array
	 */
	// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
	private function get_entity_jsonld( $term_id, $context ) {

		// The `_wl_entity_id` are URIs.
		$entity_ids         = get_term_meta( $term_id, '_wl_entity_id' );
		$entity_uri_service = $this->entity_uri_service;

		$wordlift_jsonld_service = Wordlift_Jsonld_Service::get_instance();

		$local_entity_ids = array_filter(
			$entity_ids,
			function ( $uri ) use ( $entity_uri_service ) {
				return $entity_uri_service->is_internal( $uri );
			}
		);

		// Bail out if there are no entities.
		if ( empty( $local_entity_ids ) ) {
			return array();
		}

		$post            = $this->entity_uri_service->get_entity( array_shift( $local_entity_ids ) );
		$entities_jsonld = $wordlift_jsonld_service->get_jsonld( false, $post->ID );
		// Reset the `url` to the term page.
		$entities_jsonld[0]['url'] = get_term_link( $term_id );

		return $entities_jsonld;
	}

	/**
	 * @param $references
	 *
	 * @return array
	 */
	private function expand_references( $references ) {
		if ( ! is_array( $references ) ) {
			return array();
		}
		$references_jsonld = array();
		// Expand the references.
		foreach ( $references as $reference ) {
			if ( $reference instanceof Term_Reference ) {
				// Second level references won't be expanded.
				$references_jsonld[] = current( $this->get( $reference->get_id(), Jsonld_Context_Enum::UNKNOWN, true ) );
			} elseif ( is_numeric( $reference ) ) {
				$ref_2               = array();
				$ref_info_2          = array();
				$references_jsonld[] = $this->post_id_to_jsonld_converter->convert( $reference, $ref_2, $ref_info_2, new Relations() );
			} elseif ( $reference instanceof Post_Reference ) {
				$ref_2               = array();
				$ref_info_2          = array();
				$references_jsonld[] = $this->post_id_to_jsonld_converter->convert( $reference->get_id(), $ref_2, $ref_info_2, new Relations() );
			}
		}

		return $references_jsonld;
	}

}
