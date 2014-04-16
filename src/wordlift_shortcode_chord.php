<?php

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
