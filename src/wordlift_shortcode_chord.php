<?php

//get entity with more relations (used for the global chord)
function wl_get_most_connected_entity() {
	$post_ids = get_posts(array(
		'numberposts'   => 10,	
		'fields'        => 'ids', 		//only get post IDs
		'orderby' 		=> 'post_date',
		'order' 		=> 'DESC'
	));
	
	$entities = array();
	foreach($post_ids as $id){
		$new_entities = wl_get_related_entities($id);
		foreach($new_entities as $new){
			$entities[] = $new;
		}
	}
	
	$famous_entities = array_count_values($entities);
	if(sizeof($famous_entities) >= 1){
		return key($famous_entities);	
	} else {
		return $post_ids[0];
	}

}

//get posts related to an entity
function wl_get_entity_related_posts($entity_id){
	$result  = array();
	$e =  get_post($entity_id);
	if($e->post_type == 'entity'){
		foreach( get_posts() as $post) {
			$post_id = $post->ID;
			// Get the related array (single _must_ be true, refer to http://codex.wordpress.org/Function_Reference/get_post_meta)
			$related = wl_get_related_entities($post_id);
			$i = array_search($entity_id, $related);
			if( $i !== false ){
				$result[] = $post_id;
			}
		}
	}
	return $result;
}

//recursive function used to retrieve related content (both posts and entities)
function wl_ajax_related_entities($id, $depth, $related=null) {
	
	if($related == null) {
		$related->entities = array($id);
		$related->relations = array();
	}
	
	//get related content
	$rel = wl_get_entity_related_posts($id);
	$rel += wl_get_related_entities($id);	//...should use array_merge instead of +=
	$rel += wl_get_related_post_ids($id);
	/*echo($id);
	print_r($rel);
	echo("<br>");*/
	
	//list of entities ($rel) should be ordered by interest factors
	shuffle($rel);
	
	foreach ($rel as $e) {
	
		$related->relations[] = array($id, $e);
		
		if( ! in_array($e, $related->entities) ) {
			//found new related entity!
			$related->entities[] = $e;
			//$related->relations[] = array($id, $e);
			
			//end condition 1: obtained enough related entities
			if ( sizeof( $related->entities ) >= $depth ) {
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

//optimize and convert retrieved content to JSON
function wl_ajax_related_entities_to_json( $data ) {

	for($i=0; $i<sizeof($data->entities); $i++) {
		$id = $data->entities[$i];
		$post = get_post($id);
		$entity = new stdClass();
		$entity->uri = wl_get_entity_uri( $id );
		$entity->url = get_permalink($id);
		$entity->label = $post->post_title;
		$entity->type = $post->post_type;
		$entity->class = $post->post_class;
		
		$data->entities[$i] = $entity;
	}
	
	for($i=0; $i<sizeof($data->relations); $i++) {
		$relation = new stdClass();
		$relation->s = wl_get_entity_uri( $data->relations[$i][0] );
		$relation->p = "dcterms:relates";		//dcterms:references o dcterms:relates
		$relation->o = wl_get_entity_uri( $data->relations[$i][1] );
		
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



if ( is_admin() ) {
    add_action( 'wp_ajax_wl_ajax_chord_widget', 'wl_ajax_chord_widget' );
    add_action( 'wp_ajax_nopriv_wl_ajax_chord_widget', 'wl_ajax_chord_widget' );
}

function wl_ajax_chord_widget() {
    ob_clean();  
	$result = wl_ajax_related_entities($_REQUEST['post_id'], $_REQUEST['depth']);
	$result = wl_ajax_related_entities_to_json( $result );
    echo $result;
    die();
}

//set up widget
function wl_chord_widget_func($atts){
	
	//extract attributes and set default values
	extract( shortcode_atts( array(
								'width' => '100%',
								'height' =>	'500px',			
								'main_color' =>	'f2d',
								'depth' => 7,
								'global' => false
							), $atts));
	
	if($global){
		$post_id = wl_get_most_connected_entity();
		$widget_id = 'wl_chord_widget_global';
		$height = '200px';
	} else {
		$post_id = get_the_ID();
		$widget_id = 'wl_chord_widget_' . $post_id;
	}
	
	//adding javascript code
	wp_enqueue_script( 'd3', plugins_url('bower_components/d3/d3.min.js', __FILE__) );
	wp_enqueue_script( $widget_id, plugins_url('js-client/wordlift_shortcode_chord.js', __FILE__) );
	wp_localize_script($widget_id, 'wl_chord_params', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'action' => 'wl_ajax_chord_widget',
			'post_id' => $post_id,
			'widget_id' => $widget_id,
			'depth' => $depth,
			'main_color' => $main_color
		)
	);

	
	//returning html tamplate
	ob_start();
	
	// DEBUGGING
	/*$result = wl_ajax_related_entities($post_id, 100);
	echo "<pre>";
	print_r( $result );
	echo "</pre>";*/
	
    include('wordlift_shortcode_chord_template.php');
    return ob_get_clean();	
}

//set up shortcode
function wl_register_shortcode_chord_widget(){
	add_shortcode('wl-chord-widget', 'wl_chord_widget_func');
}

//hook to init to set up shortcode and widget
add_action( 'init', 'wl_register_shortcode_chord_widget');








////////////////////
// TinyMCE button //
////////////////////
function wl_chord_button() {
   // Only add hooks when the current user has permissions AND is in Rich Text editor mode
   if ( ( current_user_can('edit_posts') || current_user_can('edit_pages') ) && get_user_option('rich_editing') ) {
     add_filter("mce_external_plugins", "wl_chord_button_register_tinymce_javascript");
     add_filter('mce_buttons', 'wl_chord_button_register_button');
     add_action('admin_footer', 'wl_inject_chord_dialog');
   }
}

//not very clean...
function wl_inject_chord_dialog() {
	wp_enqueue_style('wp-jquery-ui-css', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery.ui.all.css');
	wp_enqueue_style( 'wp-color-picker' );
	
	wp_enqueue_script('jquery');
	wp_enqueue_script('jquery-ui-core');
	wp_enqueue_script('jquery-ui-slider');
	wp_enqueue_script('wp-color-picker');
	wp_enqueue_script( 'wl_chord_dialog', plugins_url('js-client/wordlift_chord_tinymce_dialog.js', __FILE__) );
    
    include('wordlift_shortcode_chord_tinymce_dialog.php');
}

function wl_chord_button_register_button($buttons) {
   array_push($buttons, "wl_chord");
   return $buttons;
}
 
// Load the TinyMCE plugin : editor_plugin.js (wp2.5)
function wl_chord_button_register_tinymce_javascript($plugin_array) {
      
   $plugin_array['wl_chord'] = plugins_url('js-client/wordlift_chord_tinymce_plugin.js', __FILE__);
   
   return $plugin_array;
}

// init process for button control
add_action('init', 'wl_chord_button');



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



?>
