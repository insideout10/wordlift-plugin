<?php

/**
 * The `wl_faceted_search` shortcode.
 *
 * @since 3.5.5
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

		// Extract attributes and set default values.
		$shortcode_atts = shortcode_atts( array(
			'title'          => __( 'Related articles', 'wordlift' ),
			'show_facets'    => TRUE,
			'with_carousel'  => TRUE,
			'squared_thumbs' => FALSE

		), $atts );

		foreach (
			array(
				'show_facets',
				'with_carousel',
				'squared_thumbs'
			) as $att
		) {

			// See http://wordpress.stackexchange.com/questions/119294/pass-boolean-value-in-shortcode
			$shortcode_atts[ $att ] = filter_var(
				$shortcode_atts[ $att ], FILTER_VALIDATE_BOOLEAN
			);
		}


		// If the current post is not an entity and has no related entities
		// than the shortcode cannot be rendered
		// TODO Add an alert visibile only for connected admin users
		$current_post = get_post();

		$entity_ids = ( Wordlift_Entity_Service::TYPE_NAME === $current_post->post_type ) ?
			$current_post->ID :
			wl_core_get_related_entity_ids( $current_post->ID );

		if ( 0 === count( $entity_ids ) ) {
			return '';
		}

		$div_id = 'wordlift-faceted-entity-search-widget';

		wp_enqueue_style( 'wordlift-faceted-search', dirname( plugin_dir_url( __FILE__ ) ) . '/css/wordlift-faceted-entity-search-widget.min.css' );
//	wp_enqueue_script( 'angularjs', 'https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.3.11/angular.min.js' );
//	wp_enqueue_script( 'angularjs-touch', 'https://cdnjs.cloudflare.com/ajax/libs/angular.js/1.3.11/angular-touch.min.js' );

		$this->enqueue_scripts();

		wp_enqueue_script( 'wordlift-faceted-search', dirname( plugin_dir_url( __FILE__ ) ) . '/js/wordlift-faceted-entity-search-widget.min.js', array( 'angularjs-touch' ) );

		wp_localize_script(
			'wordlift-faceted-search',
			'wl_faceted_search_params', array(
				'ajax_url'             => admin_url( 'admin-ajax.php' ),
				'action'               => 'wl_faceted_search',
				'post_id'              => $current_post->ID,
				'entity_ids'           => $entity_ids,
				'div_id'               => $div_id,
				'defaultThumbnailPath' => WL_DEFAULT_THUMBNAIL_PATH,
				'attrs'                => $shortcode_atts
			)
		);

		return '<div id="' . $div_id . '" style="width:100%"></div>';
	}

}