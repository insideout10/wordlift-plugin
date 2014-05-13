<?php

/**
 * Get entity with more relations (used for the global chord).
 *
 * @used-by wl_chord_widget_func
 *
 * @return mixed
 */
function wl_get_most_connected_entity()
{

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
 * Recursive function used to retrieve related content (both posts and entities)
 *
 * @uses wl_get_referencing_posts to get the list of posts that reference an entity.
 *
 * @param int $post_id The entity post ID.
 * @param int $depth Max number of entities in output.
 * @param array $related An existing array of related entities.
 * @return array
 */
function wl_shortcode_chord_get_relations( $post_id, $depth, $related = null ) {

    write_log( "wl_shortcode_chord_get_relations [ post id :: $post_id ][ depth :: $depth ][ related? :: " . ( is_null( $related ) ? 'yes' : 'no' ) . " ]" );

	// Create a related array which will hold entities and relations.
    if ( is_null($related) ) {
        $related = array(
            'entities'  => array( $post_id ),
            'relations' => array()
        );
    }

    // Get the post IDs that reference this entity.
    $related_ids = array_map( function( $post ) {
        return $post->ID;
    }, wl_get_referencing_posts( $post_id ) );

    $related_ids = array_merge( $related_ids, wl_get_referenced_entity_ids( $post_id ) );
    // TODO: this gets posts related to a post. Do we really need this?
    // $rel = array_merge( $rel, wl_get_related_post_ids($id) );

//    write_log( "[ is array :: " . ( is_array( $related_ids ) ? 'yes' : $related_ids ) . " ]" );
    $related_ids = array_unique( $related_ids );
	
    // TODO: List of entities ($rel) should be ordered by interest factors.
    shuffle( $related_ids );

    // Now we have all the related IDs.
    foreach ( $related_ids as $related_id ) {

        $related['relations'][] = array( $post_id, $related_id );

        if ( !in_array( $related_id, $related['entities'] ) ) {
            //Found new related entity!
            $related['entities'][] = $related_id;

            //End condition 1: obtained enough related entities.
            if (sizeof($related['entities']) >= $depth) {
                return $related;
            } else {
                // Recursive call
                $related = wl_shortcode_chord_get_relations( $related_id, $depth, $related );
            }
        }
    }

    // End condition 2: no more entities to search for.
    return $related;
}

/**
 * Optimize and convert retrieved content to JSON.
 *
 * @used-by wl_ajax_chord_widget
 *
 * @param $data
 * @return mixed|string|void
 */
function wl_shortcode_chord_relations_to_json($data)
{

    for ($i = 0; $i < sizeof($data['entities']); $i++) {
        $id   = $data['entities'][$i];
        $post = get_post( $id );

        // Skip non-existing posts.
        if ( is_null( $post ) ) {
            write_log( "wl_shortcode_chord_relations_to_json : post not found [ post id :: $id ]" );
            continue;
        }

        write_log( "wl_shortcode_chord_relations_to_json [ post id :: $post->ID ]" );

        $entity = array(
            'uri'   => wl_get_entity_uri($id),
            'url'   => get_permalink($id),
            'label' => $post->post_title,
            'type'  => $post->post_type
        );
//        $entity = new stdClass();
//        $entity->uri = wl_get_entity_uri($id);
//        $entity->url = get_permalink($id);
//        $entity->label = $post->post_title;
//        $entity->type = $post->post_type;
        // TODO: what is post_class?
//        $entity->class = $post->post_class;

        $data['entities'][$i] = $entity;
    }

    for ($i = 0; $i < sizeof($data['relations']); $i++) {
        $relation = array(
            's' => wl_get_entity_uri($data['relations'][$i][0]),
            'p' => 'dcterms:relates', //dcterms:references o dcterms:relates
            'o' => wl_get_entity_uri($data['relations'][$i][1])
        );

        $data['relations'][$i] = $relation;
    }

    
    /*echo "<pre>";
    print_r($data);
    print_r( json_encode($data) );
    echo "</pre>"; */
    
    return json_encode($data);
}


if (is_admin()) {
    add_action('wp_ajax_wl_ajax_chord_widget', 'wl_ajax_chord_widget');
    add_action('wp_ajax_nopriv_wl_ajax_chord_widget', 'wl_ajax_chord_widget');
}

/**
 * 
 * Retrieve related entities and output them in JSON.
 *
 * @uses wl_shortcode_chord_get_relations
 * @uses wl_shortcode_chord_relations_to_json
 */
function wl_ajax_chord_widget()
{
    ob_clean();
    $result = wl_shortcode_chord_get_relations( $_REQUEST['post_id'], $_REQUEST['depth'] );
    $result = wl_shortcode_chord_relations_to_json( $result );
    echo $result;
    die();
}

/**
 * Sets-up the widget. This is called by WordPress when the shortcode is inserted in the body.
 *
 * @uses wl_get_most_connected_entity to get the most connected entity.
 *
 * @param array $atts An array of parameters set by the editor to customize the shortcode behaviour.
 * @return string
 */
function wl_shortcode_chord($atts)
{
    //extract attributes and set default values
    $chord_atts = shortcode_atts(array(
        'width' => '100%',
        'height' => '500px',
        'main_color' => 'f2d',
        'depth' => 7,
        'global' => false
    ), $atts);

    if ($chord_atts['global']) {
        $post_id = wl_get_most_connected_entity();
		if($post_id == null){
			return "WordLift Chord: no entities found.";
		}
        $widget_id = 'wl_chord_widget_global';
        $chord_atts['height'] = '200px';
    } else {
        $post_id = get_the_ID();
        $widget_id = 'wl_chord_widget_' . $post_id;
    }
	
	//adding javascript code
    wp_enqueue_script('d3', plugins_url('bower_components/d3/d3.min.js', __FILE__));

    // TODO: Why are we loading the same JavaScript many times? Fix.
    wp_enqueue_script($widget_id, plugins_url('js-client/wordlift_shortcode_chord.js', __FILE__));

    wp_localize_script($widget_id, 'wl_chord_params', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'action' => 'wl_ajax_chord_widget',
            'post_id' => $post_id,
            'widget_id' => $widget_id,
            'depth' => $chord_atts['depth'],
            'main_color' => $chord_atts['main_color']
        )
    );

    // DEBUGGING
    /*$result = wl_shortcode_chord_get_relations($post_id, 100);
    echo "<pre>";
    print_r( $result );
    echo "</pre>";*/

    // Escaping atts.
    $esc_id = esc_attr($widget_id);
	$esc_width = esc_attr($chord_atts['width']);
	$esc_height = esc_attr($chord_atts['height']);
    
	// Building template.
    // TODO: in the HTML code there are static CSS rules. Move them to the CSS file.
    $chord_template = "<!-- container for the widget -->
						<div id='$esc_id' style='width:$esc_width;
								height:$esc_height;
								background-color:white;
								margin-top:10px;
								margin-bottom:10px'>
						</div>";
    
    return $chord_template;
}

/**
 * Registers the *wl-chord* shortcode.
 */
function wl_shortcode_chord_register()
{
    add_shortcode('wl-chord', 'wl_shortcode_chord');
}
add_action('init', 'wl_shortcode_chord_register');
