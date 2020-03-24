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

		$this->endpoint = '/context-card';

		// PHP 5.3 compatibility as `$this` cannot be used in closures.
		$that = $this;

		add_action( 'rest_api_init', function () use ( $that ) {
			register_rest_route( WL_REST_ROUTE_DEFAULT_NAMESPACE, $that->endpoint, array(
				'methods'  => 'GET',
				'callback' => array( $that, 'context_data' ),
			) );
		} );

	}

	function format_response( $jsonld, $publisher = true ) {

		$response = array();

		if(!isset($jsonld) || empty($jsonld) || empty($jsonld[0])){
			return null;
		}

		if( isset($jsonld[0]['description']) && !empty($jsonld[0]['description']) ){
			if( isset($jsonld[0]['name']) && !empty($jsonld[0]['name']) ) {
				$title = $jsonld[0]['name'];
				$pos = strpos($jsonld[0]['description'], $title);
				$response['description'] = $jsonld[0]['description'];
				if ($pos !== false) {
					$response['description'] = substr_replace($response['description'], "<strong>$title</strong>", $pos, strlen($title));
				}
			} else {
				$response['description'] = $jsonld[0]['description'];
			}
		}

		if( isset($jsonld[0]['name']) && !empty($jsonld[0]['name']) ){
			$response['title'] = $jsonld[0]['name'];
		}

		if( isset($jsonld[0]['url']) && !empty($jsonld[0]['url']) ){
			$response['url'] = $jsonld[0]['url'];
		}

		if( isset($jsonld[0]['image']) &&
		    isset($jsonld[0]['image'][0]['url']) &&
		    isset($jsonld[0]['image'][0]['width']) &&
		    isset($jsonld[0]['image'][0]['height'])
		){
			$response['image'] = array(
				'url' => $jsonld[0]['image'][0]['url'],
				'width' => $jsonld[0]['image'][0]['width'],
				'height' => $jsonld[0]['image'][0]['height'],
			);
		}

		if($publisher){
			$publisher_id       = Wordlift_Configuration_Service::get_instance()->get_publisher_id();
			$publisher_jsonld   = Wordlift_Jsonld_Service::get_instance()->get_jsonld( false, $publisher_id );
			$response['publisher'] = $this->format_response( $publisher_jsonld, false );
		}

		return $response;
	}

	public function context_data( $request ) {

		$entity_uri    = urldecode( $request->get_param( 'entity_url' ) );
		$entity_sameas = $request->get_param( 'sameas' );

		if ( !empty( $entity_uri ) ) {
			return $this->context_data_by_entity_uri( $entity_uri );
		}
		if ( !empty( $entity_sameas ) ) {
			return $this->context_data_by_sameas( $entity_sameas );
		}

	}

	private function context_data_by_entity_uri( $entity_uri ) {

		$entity_id = $this->url_to_postid( $entity_uri );
		$jsonld = Wordlift_Jsonld_Service::get_instance()->get_jsonld( false, $entity_id );

		return $this->format_response( $jsonld );

	}

	private function context_data_by_sameas( $entity_sameas ) {

		// Look for an entity.
		foreach ( $entity_sameas as $id ) {
			$post = Wordlift_Entity_Service::get_instance()
			                               ->get_entity_post_by_uri( urldecode($id) );

			if ( null !== $post ) {
				$jsonld    = Wordlift_Jsonld_Service::get_instance()->get_jsonld( false, $post->ID );

				return $this->format_response( $jsonld );
			}
		}

	}


	public function enqueue_scripts() {
		$show_context_cards = true;
		$show_context_cards = apply_filters( 'wl_show_context_cards', $show_context_cards );
		if ( $show_context_cards ) {
			wp_enqueue_script( 'wordlift-cloud' );
			wp_localize_script( 'wordlift-cloud', 'wlCloudContextCards', array(
				'selector'  => 'a.wl-entity-page-link',
				'baseUrl'   => get_rest_url() . WL_REST_ROUTE_DEFAULT_NAMESPACE . $this->endpoint
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
