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
	$results_ids = array();
	$results     = array();

	// get the related entities, ordered by WHO-WHAT-WHERE-WHEN 
	// TODO Replace with a single query 
	$related_entities = wl_core_get_related_entities( $current_post_id, array(
		'predicate' => WL_WHO_RELATION,
		'status'    => 'publish'
	) );
	$related_entities = array_merge( $related_entities, wl_core_get_related_entities( $current_post_id, array(
		'predicate' => WL_WHAT_RELATION,
		'status'    => 'publish'
	) ) );
	$related_entities = array_merge( $related_entities, wl_core_get_related_entities( $current_post_id, array(
		'predicate' => WL_WHERE_RELATION,
		'status'    => 'publish'
	) ) );
	$related_entities = array_merge( $related_entities, wl_core_get_related_entities( $current_post_id, array(
		'predicate' => WL_WHEN_RELATION,
		'status'    => 'publish'
	) ) );

	foreach ( $related_entities as $rel_entity ) {

		wl_write_log( "Looking for posts related to entity $rel_entity->ID" );

		// take the id of posts referencing the entity
		// TODO return just posts ids
		$referencing_posts = wl_core_get_related_posts( $rel_entity->ID, array(
			'status' => 'publish'
		) );

		// loop over them and take the first one which is not already in the $related_posts
		foreach ( $referencing_posts as $referencing_post ) {

			if ( ! in_array( $referencing_post->ID, $related_posts_ids ) && $referencing_post->ID != $current_post_id ) {
				$related_posts_ids[] = $referencing_post->ID;
				// TODO serialize entity and add permalink
				// TODO returns just title, permalink and thumbnail for post
				$related_posts[]     = array( $referencing_post, $rel_entity );
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

