<?php

function wl_ajax_related_entities($id, $depth, $related=null) {
	
	if($related==null) {
		$related->entities = array();
		$related->relations = array();
	}
	
	//get related entities for this entity
	$rel = wl_get_related_entities($id);
	$rel += wl_get_related_post_ids($id);
	
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

function wl_ajax_related_entities_to_json( $data ) {
	for($i=0; $i<sizeof($data->entities); $i++) {
		$id = $data->entities[$i];
		$post = get_post($id);
		$entity = new stdClass();
		$entity->uri = wl_get_entity_uri( $id );
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

//set up widget
function wl_chord_widget_func($atts){
	
	//adding javascript code
	wp_enqueue_script( 'd3', plugins_url('bower_components/d3/d3.min.js', __FILE__) );
	wp_enqueue_script( 'wl_shortcode_chord', plugins_url('js-client/wordlift_shortcode_chord.js', __FILE__) );
	wp_localize_script('wl_shortcode_chord', 'wl_chord_params', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'post_id' => get_the_ID(),
			'altro' => 'ula badula ma chi te se incula'
		)
	);

	//extract attributes and set default values
	extract( shortcode_atts( array(
								'width' => '200px',
								'height' =>	'100px',			
								'main_color' =>	'blue'
							), $atts));
	
	
	//print ajax-url on the page!!!!
	
	//returning html tamplate
	ob_start();
	$post_id = get_the_ID();
	$result = wl_ajax_related_entities($post_id, 100);
	$result = wl_ajax_related_entities_to_json( $result );
	
    include('wordlift_shortcode_chord_template.php');
    return ob_get_clean();	
}

//set up shortcode
function wl_register_shortcode_chord_widget(){
	add_shortcode('wl-chord-widget', 'wl_chord_widget_func');
}

//hook to init to set up shortcode and widget
add_action( 'init', 'wl_register_shortcode_chord_widget');

?>
