<?php
/**
 * Shortcode to print the in-post navigator
 */
function wordlift_register_shortcode_navigator() {
	add_shortcode( 'wl_navigator', 'wordlift_shortcode_navigator' );
}

function wl_shortcode_navigator_ajax( $http_raw_data = null ) {
	
	// Post ID must be defined
	if ( ! isset( $_GET[ 'post_id' ] ) ) {
		wp_die( 'No post_id given' );
		return;
	}

	$current_post_id = $_GET[ 'post_id' ];
	$current_post = get_post( $current_post_id );	
	
	// Post ID has to match an existing item
	if ( null === $current_post ) {
		wp_die( 'No valid post_id given' );
		return;
	}

	// prepare structures to memorize other related posts
	$results     = array();
	$blacklist_ids = array( $current_post_id );
	$related_entities = array();

	// Get the related entities, ordering them by WHO, WHAT, WHERE, WHEN 
	// TODO Replace with a single query if it is possible
	foreach ( array( 
		WL_WHO_RELATION,
		WL_WHAT_RELATION,
		WL_WHERE_RELATION,
		WL_WHEN_RELATION ) as $predicate ) {

		$related_entities = array_merge( $related_entities, wl_core_get_related_entities( $current_post_id, array(
			'predicate' => $predicate,
			'status'    => 'publish'
		) ) );
	
	}

	foreach ( $related_entities as $related_entity ) {

		// take the id of posts referencing the entity
		$referencing_post_ids = wl_core_get_related_post_ids( $related_entity->ID, array(
			'status' => 'publish'
		) );

		// loop over them and take the first one which is not already in the $related_posts
		foreach ( $referencing_post_ids as $referencing_post_id ) {

			if ( ! in_array( $referencing_post_id, $blacklist_ids ) ) {
				
				$blacklist_ids[] = $referencing_post_id;
				$serialized_entity = wl_serialize_entity( $related_entity );
				$thumbnail           = wp_get_attachment_url( get_post_thumbnail_id( $referencing_post_id, 'thumbnail' ) );		 
			
				$results[]     = array( 
					array( 
						'permalink' => get_post_permalink( $referencing_post_id ),
						'title'		=> get_the_title( $referencing_post_id ),
						'thumbnail'	=>  ( $thumbnail ) ?
							$thumbnail : 
							WL_DEFAULT_THUMBNAIL_PATH
					), 
					array(
						'label' 	=> $serialized_entity[ 'label' ],
						'mainType' 	=> $serialized_entity[ 'mainType' ],
						'permalink'	=> get_post_permalink( $related_entity->ID )
					) 
				);
			}
		}
	}

	// Return results in json
	wl_core_send_json( $results );
}

/**
 * Execute the [wl_navigator] shortcode.
 *
 * @return string HTML of the navigator.
 */
function wordlift_shortcode_navigator() {

	// avoid building the widget when there is a list of posts.
	if ( ! is_single() ) {
		return '';
	}

	wp_enqueue_script( 'angularjs', dirname( plugin_dir_url( __FILE__ ) ) . '/bower_components/angular/angular.min.js' );
	wp_enqueue_script( 'wordlift-ui', dirname( plugin_dir_url( __FILE__ ) ) . '/js/wordlift-ui.min.js', array( 'jquery' ) );
	wp_enqueue_style( 'wordlift-ui', dirname( plugin_dir_url( __FILE__ ) ) . '/css/wordlift-ui.min.css' );	

	$navigator_id = uniqid( 'wl-navigator-widget-' );

	return <<<EOF
            <div id="$navigator_id" class="wl-navigator-widget"></div>
EOF;
}

add_action( 'init', 'wordlift_register_shortcode_navigator' );
add_action( 'wp_ajax_wl_navigator', 'wl_shortcode_navigator_ajax' );
add_action( 'wp_ajax_nopriv_wl_navigator', 'wl_shortcode_navigator_ajax' );

