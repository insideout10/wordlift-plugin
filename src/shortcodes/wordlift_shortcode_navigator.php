<?php
///**
// * Shortcode to print the in-post navigator
// */
//function wordlift_register_shortcode_navigator() {
//	add_shortcode( 'wl_navigator', 'wordlift_shortcode_navigator' );
//}

function wl_shortcode_navigator_ajax( $http_raw_data = NULL ) {

	// Post ID must be defined
	if ( ! isset( $_GET['post_id'] ) ) {
		wp_die( 'No post_id given' );

		return;
	}

	$current_post_id = $_GET['post_id'];
	$current_post    = get_post( $current_post_id );

	// Post ID has to match an existing item
	if ( NULL === $current_post ) {
		wp_die( 'No valid post_id given' );

		return;
	}

	// prepare structures to memorize other related posts
	$results          = array();
	$blacklist_ids    = array( $current_post_id );
	$related_entities = array();

	// Get the related entities, ordering them by WHO, WHAT, WHERE, WHEN 
	// TODO Replace with a single query if it is possible
	// We select in inverse order to give priority to less used entities 
	foreach (
		array(
			WL_WHEN_RELATION,
			WL_WHERE_RELATION,
			WL_WHAT_RELATION,
			WL_WHO_RELATION
		) as $predicate
	) {

		$related_entities = array_merge( $related_entities, wl_core_get_related_entities( $current_post_id, array(
			'predicate' => $predicate,
			'status'    => 'publish'
		) ) );

	}

	foreach ( $related_entities as $related_entity ) {

		// take the id of posts referencing the entity
		$referencing_posts = wl_core_get_related_posts( $related_entity->ID, array(
			'status' => 'publish'
		) );

		// loop over them and take the first one which is not already in the $related_posts
		foreach ( $referencing_posts as $referencing_post ) {

			if ( ! in_array( $referencing_post->ID, $blacklist_ids ) ) {

				$blacklist_ids[]   = $referencing_post->ID;
				$serialized_entity = wl_serialize_entity( $related_entity );
				$thumbnail         = wp_get_attachment_url( get_post_thumbnail_id( $referencing_post->ID, 'thumbnail' ) );

				if ( $thumbnail ) {

					$results[] = array(
						'post'   => array(
							'permalink' => get_post_permalink( $referencing_post->ID ),
							'title'     => $referencing_post->post_title,
							'thumbnail' => $thumbnail
						),
						'entity' => array(
							'label'     => $serialized_entity['label'],
							'mainType'  => $serialized_entity['mainType'],
							'permalink' => get_post_permalink( $related_entity->ID )
						)
					);

					// Be sure no more than 1 post for entity is returned
					break;
				}
			}
		}
	}

	// Return first 4 results in json accordingly to 4 columns layout
	wl_core_send_json(
		array_slice( array_reverse( $results ), 0, 4 )
	);
}

///**
// * Execute the [wl_navigator] shortcode.
// *
// * @return string HTML of the navigator.
// */
//function wordlift_shortcode_navigator( $atts ) {
//
//	// Extract attributes and set default values.
//	$shortcode_atts = shortcode_atts( array(
//		'title'          => __( 'Related articles', 'wordlift' ),
//		'with_carousel'  => TRUE,
//		'squared_thumbs' => FALSE
//	), $atts );
//
//	foreach (
//		array(
//			'with_carousel',
//			'squared_thumbs'
//		) as $att
//	) {
//
//		// See http://wordpress.stackexchange.com/questions/119294/pass-boolean-value-in-shortcode
//		$shortcode_atts[ $att ] = filter_var(
//			$shortcode_atts[ $att ], FILTER_VALIDATE_BOOLEAN
//		);
//	}
//
//	// avoid building the widget when there is a list of posts.
//	if ( ! is_single() ) {
//		return '';
//	}
//
//	$current_post = get_post();
//
//	wp_enqueue_style( 'wordlift-ui', dirname( plugin_dir_url( __FILE__ ) ) . '/css/wordlift-ui.min.css' );
//
//	$navigator_id = uniqid( 'wl-navigator-widget-' );
//
//	wp_localize_script( 'wordlift-ui', 'wl_navigator_params', array(
//			'ajax_url' => admin_url( 'admin-ajax.php' ),
//			'action'   => 'wl_navigator',
//			'post_id'  => $current_post->ID,
//			'attrs'    => $shortcode_atts
//		)
//	);
//
//	return <<<EOF
//            <div id="$navigator_id" class="wl-navigator-widget"></div>
//EOF;
//}

//add_action( 'init', 'wordlift_register_shortcode_navigator' );
add_action( 'wp_ajax_wl_navigator', 'wl_shortcode_navigator_ajax' );
add_action( 'wp_ajax_nopriv_wl_navigator', 'wl_shortcode_navigator_ajax' );

