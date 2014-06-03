<?php

/**
 * Get entity with more relations (used for the global chord).
 *
 * @used-by wl_chord_widget_func
 *
 * @return mixed
 */
function wl_shortcode_chord_most_referenced_entity_id()
{
    // TODO: this is used to get the post to display for the *global chord*.
    // Does it actually make sense to have a global chord that selects one post?
    // It would be better to show the last X posts and the referenced entities.

    // Get the last 20 posts by post date.
    // For each post get the entities they reference.
    $post_ids = get_posts( array(
        'numberposts' => 20,
        'fields'      => 'ids', //only get post IDs
        'orderby'     => 'post_date',
        'order'       => 'DESC'
    ) );
	
	if( empty( $post_ids ) ){
		return null;
	}

    $entities = array();
    foreach ( $post_ids as $id ) {
        $entities = array_merge( $entities, wl_get_referenced_entity_ids( $id ) );
    }

    $famous_entities = array_count_values($entities);
    arsort($famous_entities);
    if (sizeof($famous_entities) >= 1) {
        return key($famous_entities);
    } else {
        return $post_ids[0];
    }

}

/**
 * Recursive function used to retrieve related content starting from a post ID.
 *
 * @uses wl_get_referencing_posts to get the list of posts that reference an entity.
 *
 * @param int $post_id The entity post ID.
 * @param int $depth Max number of entities in output.
 * @param array $related An existing array of related entities.
 * @return array
 */
function wl_shortcode_chord_get_relations( $post_id, $depth = 3, $related = null ) {

    if ( $depth <= 0 ) {
        return $related;
    }

    wl_write_log( "wl_shortcode_chord_get_relations [ post id :: $post_id ][ depth :: $depth ][ related? :: " . ( is_null( $related ) ? 'yes' : 'no' ) . " ]" );

	// Create a related array which will hold entities and relations.
    if ( is_null( $related ) ) {
        $related = array(
            'entities'  => array( $post_id ),
            'relations' => array()
        );
    }

    // Get the post IDs that reference this entity.
    $related_ids = array_map( function( $post ) {
        return $post->ID;
    }, wl_get_referencing_posts( $post_id ) );

    // Get the post IDs referenced by this entity/post.
    $related_ids = array_merge( $related_ids, wl_get_referenced_entity_ids( $post_id ) );
    $related_ids = array_unique( $related_ids );
	
    // TODO: List of entities ($rel) should be ordered by interest factors.
    shuffle( $related_ids );

    // Now we have all the related IDs.
    foreach ( $related_ids as $related_id ) {

        // TODO: does it make sense to set an array post ID > related ID? The *wl_shortcode_chord_relations_to_json*
        // method is going anyway to *refactor* the data structure. So here the structure may be optimized in terms
        // of readability and performance.
        $related['relations'][] = array( $post_id, $related_id );

        if ( !in_array( $related_id, $related['entities'] ) ) {
            //Found new related entity!
            $related['entities'][] = $related_id;

            $related = wl_shortcode_chord_get_relations( $related_id, ( $depth - 1 ), $related );
//            // TODO: depth is actually a limit here, meaning that no more than $depth posts are gathered.
//            // Depth should be changed to be the *distance* between the initial post and the others.
//            // Eventually we might add a *limit* parameter instead of depth.
//            if ( sizeof( $related['entities'] ) >= $depth ) {
//                return $related;
//            } else {
//                // Recursive call
//                $related = wl_shortcode_chord_get_relations( $related_id, $depth, $related );
//            }
        }
    }

    // End condition 2: no more entities to search for.
    return $related;
}

/**
 * Optimize and convert retrieved content to JSON.
 *
 * @used-by wl_shortcode_chord_ajax
 *
 * @param $data
 * @return mixed|string|void
 */
function wl_shortcode_chord_relations_to_json( $data )
{

    // Refactor the entities array in order to provide entities relevant data (uri, url, label, type, css_class).
    array_walk( $data['entities'], function ( &$item ) {
        $post = get_post( $item );

        // Skip non-existing posts.
        if ( is_null( $post ) ) {
            wl_write_log( "wl_shortcode_chord_relations_to_json : post not found [ post id :: $item ]" );
            return $item;
        }

        // Get the entity taxonomy bound to this post (if there's no taxonomy, no stylesheet will be set).
        $term = wl_entity_get_type( $item );

        wl_write_log( "wl_shortcode_chord_relations_to_json [ post id :: $post->ID ][ term :: " . var_export( $term, true ) . " ]" );

        $entity = array(
            'uri'   => wl_get_entity_uri( $item ),
            'url'   => get_permalink( $item ),
            'label' => $post->post_title,
            'type'  => $post->post_type,
            'css_class' => ( isset( $term['css_class'] ) ? $term['css_class'] : '' )
        );

        $item = $entity;
    } );

    // Refactor the relations.
    array_walk( $data['relations'], function ( &$item ) {
        $relation = array(
            's' => wl_get_entity_uri( $item[0] ),
            'o' => wl_get_entity_uri( $item[1] )
        );

        $item = $relation;
    } );

    // Return the JSON representation.
    return json_encode($data);
}

/**
 * Retrieve related entities and output them in JSON.
 *
 * @uses wl_shortcode_chord_get_relations
 * @uses wl_shortcode_chord_relations_to_json
 */
function wl_shortcode_chord_ajax()
{

    $post_id = $_REQUEST['post_id'];
    $depth   = $_REQUEST['depth'];

    ob_clean();
    header( 'Content-Type: application/json' );

    $result = wl_shortcode_chord_get_relations( $post_id, $depth );
    $result = wl_shortcode_chord_relations_to_json( $result );

    wl_write_log( "wl_shortcode_chord_relations_to_json : sending output" );

    echo $result;

    wp_die();
}

add_action('wp_ajax_wl_chord', 'wl_shortcode_chord_ajax');
add_action('wp_ajax_nopriv_wl_chord', 'wl_shortcode_chord_ajax');


/**
 * Sets-up the widget. This is called by WordPress when the shortcode is inserted in the body.
 *
 * @uses wl_shortcode_chord_most_referenced_entity_id to get the most connected entity.
 *
 * @param array $atts An array of parameters set by the editor to customize the shortcode behaviour.
 * @return string
 */
function wl_shortcode_chord( $atts ) {

    //extract attributes and set default values
    $chord_atts = shortcode_atts(array(
        'width'      => '100%',
        'height'     => '500px',
        'main_color' => '000',
        'depth'      => 5,
        'global'     => false
    ), $atts);

    if ($chord_atts['global']) {
        $post_id = wl_shortcode_chord_most_referenced_entity_id();
		if($post_id == null){
			return "WordLift Chord: no entities found.";
		}
        $widget_id = 'wl_chord_global';
        $chord_atts['height'] = '200px';
    } else {
        $post_id = get_the_ID();
        $widget_id = 'wl_chord_' . $post_id;
    }
	
	//adding javascript code
    wp_enqueue_script('d3', plugins_url('bower_components/d3/d3.min.js', __FILE__));
    wp_enqueue_script( 'wordlift-ui', plugins_url('js/wordlift.ui.js', __FILE__) );
    wp_localize_script( 'wordlift-ui', 'wl_chord_params', array(
            'ajax_url'   => admin_url('admin-ajax.php'),
            'action'     => 'wl_chord'
        )
    );

    // Escaping atts.
    $esc_class  = esc_attr('wl-chord');
    $esc_id     = esc_attr($widget_id);
	$esc_width  = esc_attr($chord_atts['width']);
	$esc_height = esc_attr($chord_atts['height']);

    $esc_post_id 	= esc_attr($post_id);
    $esc_depth		= esc_attr($chord_atts['depth']);
    $esc_main_color = esc_attr($chord_atts['main_color']);
    
	// Building template.
    // TODO: in the HTML code there are static CSS rules. Move them to the CSS file.
    return <<<EOF
<div class="$esc_class" 
	id="$esc_id"
	data-post-id="$esc_post_id"
    data-depth="$esc_depth"
    data-main-color="$esc_main_color"
	style="width:$esc_width;
        height:$esc_height;
        background-color:white;
        margin-top:10px;
        margin-bottom:10px">
</div>
EOF;

}

/**
 * Registers the *wl-chord* shortcode.
 */
function wl_shortcode_chord_register()
{
    add_shortcode('wl-chord', 'wl_shortcode_chord');
}
add_action('init', 'wl_shortcode_chord_register');
