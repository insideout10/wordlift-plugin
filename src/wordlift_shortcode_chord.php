<?php

function wl_ajax_related_entities($id, $related, $depth) {
	
	//get related entities for this entity
	$rel = wl_get_related_entities($id);
	$rel += wl_get_related_post_ids($id);
	
	foreach ($rel as $e) {
		if( ! in_array($e, $related) ) {
			//found new related entity
			$related[] = $e;
			
			//end condition 1: obtained enough related entities
			if ( sizeof($related) >= $depth ){
				return $related;
			} else {
				//recursive call
				$related += wl_ajax_related_entities($e, $related, $depth);
			}
		}
	}
	
	//end condition 2: no more entities to search for
	return $related;
}

//set up widget
function wordlift_chord_widget_func($atts){
	
	//adding javascript code
	wp_enqueue_script( 'd3', wordlift_get_url('/bower_components/d3/d3.min.js') );
	wp_enqueue_script( 'wordlift_shortcode_chord', wordlift_get_url('/js-client/wordlift_shortcode_chord.js') );

	//extract attributes and set default values
	extract( shortcode_atts( array(
								'width' => '200px',
								'height' =>	'100px',			
								'main_color' =>	'blue'
							), $atts));
	
	//returning html tamplate
	ob_start();
	$post_id = get_the_ID();
	$result = wl_ajax_related_entities($post_id, array(), 100);
	print_r($result);
	foreach($result as $r){
		$p = get_post($r);
		echo("<br>" . $p->post_title);
	}
	
    include('wordlift_shortcode_chord_template.php');
    return ob_get_clean();	
}

//set up shortcode
function wordlift_register_shortcode_chord_widget(){
	add_shortcode('wordlift-chord-widget', 'wordlift_chord_widget_func');
}

//hook to init to set up shortcode and widget
add_action( 'init', 'wordlift_register_shortcode_chord_widget');

?>
