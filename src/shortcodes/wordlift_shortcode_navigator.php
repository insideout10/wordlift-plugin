<?php
/**
 * Shortcode to print the in-post navigator
 */
function wordlift_register_shortcode_navigator() {
	add_shortcode( 'wl_navigator', 'wordlift_shortcode_navigator' );
}

/**
 * Get list of posts that will populate the navigator.
 *
 * @param int $post_id Id of the post.
 *
 * @return Array List of tuples organized in this way:
 *      Array(
 *          [0] => Array(
 *              [0] => related post Object
 *              [1] => related entity Object (main post and the post above are related via this entity)
 *          )
 *          [1] => another tuple
 * [2] => ...
 *      )
 */
function wordlift_shortcode_navigator_populate( $post_id ) {

	// prepare structures to memorize other related posts
	$related_posts_ids = array();
	$related_posts     = array();

	// get the related entities, ordered by WHO-WHAT-WHERE-WHEN (as established the 29/7/2015 12:45 in the grottino)
	// TODO: should be a single query
	$related_entities = wl_core_get_related_entities( $post_id, array(
		'predicate' => WL_WHO_RELATION,
		'status'    => 'publish'
	) );
	$related_entities = array_merge( $related_entities, wl_core_get_related_entities( $post_id, array(
		'predicate' => WL_WHAT_RELATION,
		'status'    => 'publish'
	) ) );
	$related_entities = array_merge( $related_entities, wl_core_get_related_entities( $post_id, array(
		'predicate' => WL_WHERE_RELATION,
		'status'    => 'publish'
	) ) );
	$related_entities = array_merge( $related_entities, wl_core_get_related_entities( $post_id, array(
		'predicate' => WL_WHEN_RELATION,
		'status'    => 'publish'
	) ) );

	foreach ( $related_entities as $rel_entity ) {

		wl_write_log( "Looking for posts related to entity $rel_entity->ID" );

		// take the id of posts referencing the entity
		$referencing_posts = wl_core_get_related_posts( $rel_entity->ID, array(
			'status' => 'publish'
		) );

		// loop over them and take the first one which is not already in the $related_posts
		foreach ( $referencing_posts as $referencing_post ) {

			if ( ! in_array( $referencing_post->ID, $related_posts_ids ) && $referencing_post->ID != $post_id ) {
				$related_posts_ids[] = $referencing_post->ID;
				$related_posts[]     = array( $referencing_post, $rel_entity );
			}
		}
	}

	wl_write_log( "Related posts for $post_id" );
	wl_write_log( $related_posts );

	return $related_posts;
}

/**
 * Extract image URL from the output of *get_the_post_thumbnail*.
 *
 * @param string $img Output of *get_the_post_thumbnail*.
 *
 * @return string Url of the image.
 */
function wl_get_the_post_thumbnail_src( $img ) {
	return ( preg_match( '~\bsrc="([^"]++)"~', $img, $matches ) ) ? $matches[1] : '';
}

/**
 * Execute the [wl_navigator] shortcode.
 *
 * @return string HTML of the navigator.
 */
function wordlift_shortcode_navigator() {

	// avoid building the widget when there is a list of posts.
	if ( ! is_single() ) {
		return;
	}

	// include slick on page
	wp_enqueue_script( 'slick', dirname( plugin_dir_url( __FILE__ ) ) . '/public/js/slick.min.js' );
	wp_enqueue_style( 'slick', dirname( plugin_dir_url( __FILE__ ) ) . '/public/css/slick.css' );
	wp_enqueue_style( 'slick-theme', dirname( plugin_dir_url( __FILE__ ) ) . '/public/css/slick-theme.css' );
	wp_enqueue_style( 'slick-theme-wordlift', dirname( plugin_dir_url( __FILE__ ) ) . '/public/css/slick-theme-wordlift.css' );


	// get posts that will populate the navigator (criteria may vary, see function *wordlift_shortcode_navigator_populate*)
	// The result array will contains tuples (post_object, entity_object)
	$related_posts_and_entities = wordlift_shortcode_navigator_populate( get_the_ID() );

	// build the HTML
	$counter = 0;
    $navigator_css_id = uniqid( 'wl-navigator-widget-' );
	$content = "<div class='wl-navigator-widget' id='$navigator_css_id'>";

	foreach ( $related_posts_and_entities as $related_post_entity ) {

		$related_post   = $related_post_entity[0];
		$related_entity = $related_post_entity[1];

		$thumb = wl_get_the_post_thumbnail_src( get_the_post_thumbnail( $related_post->ID, 'medium' ) );
		if ( empty( $thumb ) ) {
			$thumb = WL_DEFAULT_THUMBNAIL_PATH;
		}

		$context_link = get_permalink( $related_entity->ID );
		$context_name = $related_entity->post_title;

		$counter += 1;

		// build card HTML
        $permalink = get_permalink( $related_post->ID );
        $content .= <<<EOF
            <div class="wl-navigator-card">
                <div class="wl-navigator-lens" style="background-image:url( $thumb )">
                    <span class="wl-navigator-trigger">
                        <a href="$permalink">$related_post->post_title</a>
                    </span>
                </div>
                <div class="wl-navigator-context">
                    <a href="$context_link">$context_name</a>
                </div>
            </div>
EOF;
        
	}
    
	$content .= '</div>';
	// how many cards
	$num_cards_on_front = count( $related_posts_and_entities );

	if ( $num_cards_on_front > 3 ) {
		$num_cards_on_front = 3;
	}
	// add js
	$content .= "<script>
        ( jQuery( function($){ 
            $(document).ready(function(){
                // Launch navigator
                $('#$navigator_css_id').slick({
                    dots: false,
                    arrows: true,
                    infinite: true,
                    slidesToShow: $num_cards_on_front,
                    slidesToScroll: 1
                });
            });
        } ) );
    </script>";

	return $content;
}

add_action( 'init', 'wordlift_register_shortcode_navigator' );