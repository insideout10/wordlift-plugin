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

    $post_ids = get_posts(array(
        'numberposts' => 10,
        'fields' => 'ids', //only get post IDs
        'orderby' => 'post_date',
        'order' => 'DESC'
    ));

    $entities = array();
    foreach ($post_ids as $id) {
        $new_entities = wl_get_related_entities($id);

        foreach ($new_entities as $new) {
            $entities[] = $new;
        }
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
 * Get the posts that reference the specified entity.
 *
 * @used-by wl_ajax_related_entities
 *
 * @param int $entity_id The post ID of the entity.
 * @return array An array of post IDs.
 */
function wl_get_entity_related_posts($entity_id)
{
    $result = array();
    $entity = get_post($entity_id);

    if ($entity->post_type == 'entity') {

        foreach (get_posts() as $post) {
            $post_id = $post->ID;
            // Get the related array (single _must_ be true, refer to http://codex.wordpress.org/Function_Reference/get_post_meta)
            $related = wl_get_related_entities($post_id);
            $i = array_search($entity_id, $related);
            if ($i !== false) {
                $result[] = $post_id;
            }

        }
    }
    return $result;
}

/**
 * Recursive function used to retrieve related content (both posts and entities)
 *
 * @uses wl_get_entity_related_posts to get the list of posts that reference an entity.
 *
 * @param int $id The entity ID.
 * @param $depth
 * @param $related
 * @return
 */
function wl_ajax_related_entities($id, $depth, $related = null)
{

    if ($related == null) {
        // TODO: can this actually work? Fix.
        $related->entities = array($id);
        $related->relations = array();
    }

    //get related content
    $rel = wl_get_entity_related_posts($id);
    $rel += wl_get_related_entities($id); //...should use array_merge instead of +=
    $rel += wl_get_related_post_ids($id);
    /*echo($id);
    print_r($rel);
    echo("<br>");*/

    //list of entities ($rel) should be ordered by interest factors
    shuffle($rel);

    foreach ($rel as $e) {

        $related->relations[] = array($id, $e);

        if (!in_array($e, $related->entities)) {
            //found new related entity!
            $related->entities[] = $e;
            //$related->relations[] = array($id, $e);

            //end condition 1: obtained enough related entities
            if (sizeof($related->entities) >= $depth) {
                return $related;
            } else {
                //recursive call
                $new_results = wl_ajax_related_entities($e, $depth, $related);
                $related->entities += $new_results->entities;
                $related->relations += $new_results->relations;
            }
        }
    }

    //end condition 2: no more entities to search for
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
function wl_ajax_related_entities_to_json($data)
{

    for ($i = 0; $i < sizeof($data->entities); $i++) {
        $id = $data->entities[$i];
        $post = get_post($id);
        $entity = new stdClass();
        $entity->uri = wl_get_entity_uri($id);
        $entity->url = get_permalink($id);
        $entity->label = $post->post_title;
        $entity->type = $post->post_type;
        $entity->class = $post->post_class;

        $data->entities[$i] = $entity;
    }

    for ($i = 0; $i < sizeof($data->relations); $i++) {
        $relation = new stdClass();
        $relation->s = wl_get_entity_uri($data->relations[$i][0]);
        $relation->p = "dcterms:relates"; //dcterms:references o dcterms:relates
        $relation->o = wl_get_entity_uri($data->relations[$i][1]);

        $data->relations[$i] = $relation;
    }

    /*
    echo "<pre>";
    print_r($data);
    print_r( json_encode($data) );
    echo "</pre>";
    */

    return json_encode($data);
}


if (is_admin()) {
    add_action('wp_ajax_wl_ajax_chord_widget', 'wl_ajax_chord_widget');
    add_action('wp_ajax_nopriv_wl_ajax_chord_widget', 'wl_ajax_chord_widget');
}

/**
 * @uses wl_ajax_related_entities_to_json
 */
function wl_ajax_chord_widget()
{
    ob_clean();
    $result = wl_ajax_related_entities($_REQUEST['post_id'], $_REQUEST['depth']);
    $result = wl_ajax_related_entities_to_json($result);
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

    // TODO: what should we do when the post has no related entities?

    //extract attributes and set default values
    extract(shortcode_atts(array(
        'width' => '100%',
        'height' => '500px',
        'main_color' => 'f2d',
        'depth' => 7,
        'global' => false
    ), $atts));

    // TODO: what is this $global variable? Fix.
    if ($global) {
        $post_id = wl_get_most_connected_entity();
        $widget_id = 'wl_chord_widget_global';
        // TODO: $height is not used anywhere. Remove.
        $height = '200px';
    } else {
        $post_id = get_the_ID();
        $widget_id = 'wl_chord_widget_' . $post_id;
    }

    //adding javascript code
    wp_enqueue_script('d3', plugins_url('bower_components/d3/d3.min.js', __FILE__));

    // TODO: Why are we loading the same JavaScript many times? Fix.
    wp_enqueue_script($widget_id, plugins_url('js-client/wordlift_shortcode_chord.js', __FILE__));

    // TODO: $depth and $main_color do not exist. Check.
    wp_localize_script($widget_id, 'wl_chord_params', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'action' => 'wl_ajax_chord_widget',
            'post_id' => $post_id,
            'widget_id' => $widget_id,
            'depth' => $depth,
            'main_color' => $main_color
        )
    );


    // TODO: the HTML output is small, return it as a string, don't echo it out.
    // Returning html template.
    ob_start();

    // DEBUGGING
    /*$result = wl_ajax_related_entities($post_id, 100);
    echo "<pre>";
    print_r( $result );
    echo "</pre>";*/

    // TODO: there's no need to put the HTML in an external file, it's so small it can be embedded here. Fix.
    // TODO: in the HTML code there are static CSS rules. Move them to the CSS file.
    include('wordlift_shortcode_chord_template.php');
    return ob_get_clean();
}

/**
 * Registers the *wl-chord* shortcode.
 */
function wl_shortcode_chord_register()
{
    add_shortcode('wl-chord', 'wl_shortcode_chord');
}
add_action('init', 'wl_shortcode_chord_register');




/*
//add wp-color-picker
function wl_enqueue_chord_dialog_tools( $hook_suffix ) {

	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-slider');

	// first check that $hook_suffix is appropriate for your admin page
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'wl_chord_dialog', plugins_url('js-client/wordlift_chord_tinymce_dialog.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
}
add_action( 'admin_enqueue_scripts', 'wl_enqueue_chord_dialog_tools' );
*/
