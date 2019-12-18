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

	function __construct() {

		$this->endpoint = '/jsonld';

		// PHP 5.3 compatibility as `$this` cannot be used in closures.
		$that = $this;

		add_action( 'rest_api_init', function () use ( $that ) {
			register_rest_route( WL_REST_ROUTE_DEFAULT_NAMESPACE, $that->endpoint, array(
				'methods'  => 'GET',
				'callback' => array( $that, 'context_data' ),
			) );
		} );

	}

	public function context_data( $request ) {

		$entity_uri = urldecode( $request->get_param( 'entity_url' ) );
		$entity_id  = $this->url_to_postid( $entity_uri );
		$jsonld     = Wordlift_Jsonld_Service::get_instance()->get_jsonld( false, $entity_id );

		return $jsonld;

	}

	public function enqueue_scripts() {
		$show_context_cards = true;
		$show_context_cards = apply_filters( 'wl_show_context_cards', $show_context_cards );
		if ( $show_context_cards ) {
			wp_enqueue_script( 'wordlift-cloud' );
			$url_json = wp_json_encode( get_rest_url() . WL_REST_ROUTE_DEFAULT_NAMESPACE . $this->endpoint );
			echo( "<script type='application/javascript'>window.addEventListener( 'load', function() { wordliftCloud.contextCards('a.wl-entity-page-link', $url_json); } );</script>" );
		}
	}

	private function url_to_postid( $url ) {
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
