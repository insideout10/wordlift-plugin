<?php
/**
 * Shortcodes: Faceted Search Shortcode.
 *
 * @since      3.0.0
 * @package    Wordlift
 * @subpackage Wordlift/public
 */

/**
 * Define the {@link Wordlift_Faceted_Search_Shortcode} class which provides the
 * `wl_faceted_search` implementation.
 *
 * @since      3.20.0
 * @package    Wordlift
 * @subpackage Wordlift/public
 */
class Wordlift_Faceted_Search_Shortcode extends Wordlift_Shortcode {

	/**
	 * {@inheritdoc}
	 */
	const SHORTCODE = 'wl_faceted_search';

	/**
	 * {@inheritdoc}
	 */
	public function render( $atts ) {

		if( Wordlift_AMP_Service::is_amp_endpoint() ) {
			return $this->amp_shortcode( $atts );
		} else {
			return $this->web_shortcode( $atts );
		}

	}

	/**
	 * Shared function used by web_shortcode and amp_shortcode
	 * Bootstrap logic for attributes extraction and boolean filtering
	 * 
	 * @since      3.20.0
	 * 
	 * @param array $atts Shortcode attributes.
	 * @return array $shortcode_atts
	 */
	private function make_shortcode_atts( $atts ) {

		// Extract attributes and set default values.
		$shortcode_atts = shortcode_atts( array(
			'title'          => __( 'Related articles', 'wordlift' ),
			'show_facets'    => true,
			'with_carousel'  => true,
			'squared_thumbs' => false,
			'limit'          => 20,
		), $atts );

		foreach (
			array(
				'show_facets',
				'with_carousel',
				'squared_thumbs',
			) as $att
		) {

			// See http://wordpress.stackexchange.com/questions/119294/pass-boolean-value-in-shortcode
			$shortcode_atts[ $att ] = filter_var(
				$shortcode_atts[ $att ], FILTER_VALIDATE_BOOLEAN
			);
		}

		return $shortcode_atts;

	}

	/**
	 * Function in charge of diplaying the [wl-faceted-search] in web mode.
	 * 
	 * @since 3.20.0
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string Shortcode HTML for web
	 */
	private function web_shortcode( $atts ) {

		// attributes extraction and boolean filtering
		$shortcode_atts = $this->make_shortcode_atts( $atts );

		// If the current post is not an entity and has no related entities
		// than the shortcode cannot be rendered
		// TODO Add an alert visibile only for connected admin users.
		$current_post = get_post();

		$entity_service = Wordlift_Entity_Service::get_instance();
		$entity_ids     = $entity_service->is_entity( $current_post->ID ) ?
			array( $current_post->ID ) :
			wl_core_get_related_entity_ids( $current_post->ID );

		// Bail if there are no entity ids.
		if ( 0 === count( $entity_ids ) ) {
			return '';
		}

		$div_id = 'wordlift-faceted-entity-search-widget';

		// Enqueue common shortcode scripts.
		$this->enqueue_scripts();
		
		// Enqueue shortcode specific scripts and styles.
		$deps = apply_filters( 'wl_include_font_awesome', true )
			? array( 'wordlift-font-awesome' )
			: array();
		wp_enqueue_style( 'wordlift-faceted-search', dirname( plugin_dir_url( __FILE__ ) ) . '/css/wordlift-faceted-entity-search-widget.min.css', $deps, Wordlift::get_instance()->get_version() );
		wp_enqueue_script( 'wordlift-faceted-search', dirname( plugin_dir_url( __FILE__ ) ) . '/js/wordlift-faceted-entity-search-widget.min.js' );

		wp_localize_script(
			'wordlift-faceted-search',
			'wl_faceted_search_params', array(
				'ajax_url'             => admin_url( 'admin-ajax.php' ),
				'action'               => 'wl_faceted_search',
				'post_id'              => $current_post->ID,
				'entity_ids'           => $entity_ids,
				'limit'                => apply_filters( 'wl_faceted_search_limit', $shortcode_atts['limit'] ),
				'div_id'               => $div_id,
				'defaultThumbnailPath' => WL_DEFAULT_THUMBNAIL_PATH,
				'attrs'                => $shortcode_atts,
				'l10n'                 => array(
					'what'  => _x( 'What', 'Faceted Search Widget', 'wordlift' ),
					'who'   => _x( 'Who', 'Faceted Search Widget', 'wordlift' ),
					'where' => _x( 'Where', 'Faceted Search Widget', 'wordlift' ),
					'when'  => _x( 'When', 'Faceted Search Widget', 'wordlift' ),
				),
			)
		);

		return '<div id="' . $div_id . '" style="width:100%"></div>';

	}

	/**
	 * Function in charge of diplaying the [wl-faceted-search] in amp mode.
	 * 
	 * @since 3.20.0
	 *
	 * @param array $atts Shortcode attributes.
	 * @return string Shortcode HTML for amp
	 */	
	private function amp_shortcode( $atts ) {

		// attributes extraction and boolean filtering
		$shortcode_atts = $this->make_shortcode_atts( $atts );

		// If the current post is not an entity and has no related entities
		// than the shortcode cannot be rendered
		// TODO Add an alert visibile only for connected admin users.
		$current_post = get_post();

		$entity_service = Wordlift_Entity_Service::get_instance();
		$entity_ids     = $entity_service->is_entity( $current_post->ID ) ?
			array( $current_post->ID ) :
			wl_core_get_related_entity_ids( $current_post->ID );

		// Bail if there are no entity ids.
		if ( 0 === count( $entity_ids ) ) {
			return '';
		}
		
		$wp_json_base = get_rest_url() . WL_REST_ROUTE_DEFAULT_NAMESPACE;
		$query = array(
			'post_id'	=> $current_post->ID,
			'limit'		=> apply_filters( 'wl_faceted_search_limit', $shortcode_atts['limit'] ),
		);

		if ( strpos($wp_json_base, 'wp-json/' . WL_REST_ROUTE_DEFAULT_NAMESPACE) ){
			$delimiter = '?';
		} else {
			$delimiter = '&';
		}

		// Use a protocol-relative URL as amp-list spec says that URL's protocol must be HTTPS.
		// This is a hackish way, but this works for http and https URLs
		$wp_json_url = str_replace(array('http:', 'https:'), '', $wp_json_base) . '/faceted-search' . $delimiter . http_build_query($query);

		add_action( 'amp_post_template_css', array(
			$this,
			'amp_post_template_css',
		) );

		return '
		<amp-list width="auto"
			height="300"
			layout="fixed-height"
			src="'.$wp_json_url.'">
			<template type="amp-mustache">  
				<amp-carousel 
					height="300"
					layout="fixed-height"
					type="carousel">
				{{#values}}
					<div style="height: 300px; width: 300px;">
					<amp-img src="{{images}}"
						height="200"
						width="auto"
						layout="fixed-height"
						alt="{{label}}"></amp-img>
					<div style="white-space:normal"><a href="{{id}}">{{label}}</a></div> 
					</div>	
				{{/values}}
				</amp-carousel>
			</template>
		</amp-list>';

	}

	/**
	 * Customize the CSS when in AMP.
	 * Should echo (not return) CSS code
	 * 
	 * @since 3.20.0
	 */
	public function amp_post_template_css(){
		echo file_get_contents(dirname( plugin_dir_url( __FILE__ ) ) . '/css/wordlift-amp-custom.min.css');
	}

}
