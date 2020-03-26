<?php
/**
 * Context Cards Service
 *
 * @since      3.22.0
 * @package    Wordlift
 * @subpackage Wordlift/public
 */

class Wordlift_Context_Cards_Service {

	/**
	 * @var string
	 */
	private $endpoint;
	private $jsonld_endpoint;

	function __construct() {

		$this->endpoint        = '/context-card';
		$this->jsonld_endpoint = '/jsonld';

		// PHP 5.3 compatibility as `$this` cannot be used in closures.
		$that = $this;

		add_action( 'rest_api_init', function () use ( $that ) {
			register_rest_route( WL_REST_ROUTE_DEFAULT_NAMESPACE, $that->endpoint, array(
				'methods'  => 'GET',
				'callback' => array( $that, 'context_data' ),
			) );
		} );

		add_action( 'rest_api_init', function () use ( $that ) {
			register_rest_route( WL_REST_ROUTE_DEFAULT_NAMESPACE, $that->jsonld_endpoint, array(
				'methods'  => 'GET',
				'callback' => array( $that, 'context_data_jsonld' ),
			) );
		} );

	}

	function format_response( $jsonld, $publisher = true ) {

		$response = array();

		if ( ! isset( $jsonld ) || empty( $jsonld ) || empty( $jsonld[0] ) ) {
			return null;
		}

		if ( isset( $jsonld[0]['description'] ) && ! empty( $jsonld[0]['description'] ) ) {
			if ( isset( $jsonld[0]['name'] ) && ! empty( $jsonld[0]['name'] ) ) {
				$title                   = $jsonld[0]['name'];
				$pos                     = strpos( $jsonld[0]['description'], $title );
				$response['description'] = $jsonld[0]['description'];
				if ( $pos !== false ) {
					$response['description'] = substr_replace( $response['description'], "<strong>$title</strong>", $pos, strlen( $title ) );
				}
			} else {
				$response['description'] = $jsonld[0]['description'];
			}
		}

		if ( isset( $jsonld[0]['name'] ) && ! empty( $jsonld[0]['name'] ) ) {
			$response['title'] = $jsonld[0]['name'];
		}

		if ( isset( $jsonld[0]['url'] ) && ! empty( $jsonld[0]['url'] ) ) {
			$response['url'] = $jsonld[0]['url'];
		}

		if ( isset( $jsonld[0]['image'] ) &&
		     isset( $jsonld[0]['image'][0]['url'] ) &&
		     isset( $jsonld[0]['image'][0]['width'] ) &&
		     isset( $jsonld[0]['image'][0]['height'] )
		) {
			$response['image'] = array(
				'url'    => $jsonld[0]['image'][0]['url'],
				'width'  => $jsonld[0]['image'][0]['width'],
				'height' => $jsonld[0]['image'][0]['height'],
			);
		}

		if ( $publisher ) {
			$publisher_id          = Wordlift_Configuration_Service::get_instance()->get_publisher_id();
			$publisher_jsonld      = Wordlift_Jsonld_Service::get_instance()->get_jsonld( false, $publisher_id );
			$response['publisher'] = $this->format_response( $publisher_jsonld, false );
		}

		return $response;
	}

	function format_as_jsonld( $jsonld, $publisher = true ) {

		$response = array();

		if ( ! isset( $jsonld ) || empty( $jsonld ) || empty( $jsonld[0] ) ) {
			return null;
		}

		$publisher_jsonld = Wordlift_Jsonld_Service::get_instance()->get_jsonld( true );

		$response['@context']  = "http://schema.org";
		$response['@type']     = "WebSite";
		$response['publisher'] = $publisher_jsonld;
		$response['mentions']  = $jsonld;

		return $response;

	}

	public function context_data( $request ) {

		$entity_uri = urldecode( $request->get_param( 'entity_url' ) );
		$entity_id  = Wordlift_Context_Cards_Service::url_to_postid( $entity_uri );
		$jsonld     = Wordlift_Jsonld_Service::get_instance()->get_jsonld( false, $entity_id );

		return $this->format_response( $jsonld );

	}

	public function context_data_jsonld( $request ) {

		$ids = $request->get_param( 'id' );

		$cached_entity_uri_service    = Wordlift_Cached_Entity_Uri_Service::get_instance();
		$cached_entity_uri_service->preload_uris( array_map('urldecode', $ids) );

		// Look for an entity.
		foreach ( $ids as $id ) {
			$post = $cached_entity_uri_service->get_entity( urldecode( $id ) );

			if ( null !== $post ) {
				$jsonld = Wordlift_Jsonld_Service::get_instance()->get_jsonld( false, $post->ID );

				return $this->format_as_jsonld( $jsonld );
			}
		}

	}

	public function enqueue_scripts() {
		$show_context_cards = apply_filters( 'wl_show_context_cards', true );
		$base_url = apply_filters( 'wl_context_cards_base_url', get_rest_url() . WL_REST_ROUTE_DEFAULT_NAMESPACE . $this->endpoint );
		if ( $show_context_cards ) {
			wp_enqueue_script( 'wordlift-cloud' );
			wp_localize_script( 'wordlift-cloud', 'wlCloudContextCards', array(
				'selector' => 'a.wl-entity-page-link',
				'baseUrl'  => $base_url
			) );
		}
	}

	static function url_to_postid( $url ) {
		// Try with url_to_postid
		$post_id = url_to_postid( $url );
		if ( $post_id == 0 ) {
			// Try with get_page_by_path
			$post = get_page_by_path( basename( untrailingslashit( $url ) ), OBJECT, 'entity' );
			if ( $post ) {
				$post_id = $post->ID;
			}
		}

		return $post_id;
	}

}
