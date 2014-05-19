<?php

/**
 * Retrieve timeline events and output them in JSON.
 *
 * @uses AAAAAAAAAAAAAAAAAAAAa
 * @uses AAAAAAAAAAAAAAAAAAAAA
 */
function wl_shortcode_timeline_ajax()
{
    /*ob_clean();
    $result = wl_shortcode_chord_get_relations( $_REQUEST['post_id'], $_REQUEST['depth'] );
    $result = wl_shortcode_chord_relations_to_json( $result );
    echo $result;
    die();*/
}

add_action('wp_ajax_wl_timeline', 'wl_shortcode_timeline_ajax');
add_action('wp_ajax_nopriv_wl_timeline', 'wl_shortcode_timeline_ajax');


/**
 * Sets-up the widget. This is called by WordPress when the shortcode is inserted in the body.
 *
 * @uses AAAAAAAAAAAAAAAAAAAAAAAAAAAAAA
 * 
 * @param array $atts An array of parameters set by the editor to customize the shortcode behaviour.
 * @return string
 */
function wl_shortcode_timeline( $atts ) {

    //extract attributes and set default values
    $timeline_atts = shortcode_atts(array(
        'width'      => '100%',
        'height'     => '500px',
        'main_color' => '000'
    ), $atts);
	
	// Add wordlift-ui script.
	wp_enqueue_script( 'wordlift-ui', plugins_url('js/wordlift.ui.js', __FILE__) );
	
	// Add timeline library.
	wp_enqueue_script('timelinejs', plugins_url('bower_components/NUKnightLab-TimelineJS/build/js/storyjs-embed.js', __FILE__));
	
	/*
    // TODO: separate global script parameters from single instances.
    wp_localize_script($widget_id, 'wl_chord_params', array(
            'ajax_url'   => admin_url('admin-ajax.php'), // global setting
            'action'     => 'wl_chord',   // global setting
            // local settings.
            'post_id'    => $post_id,
            'widget_id'  => $widget_id,
            'depth'      => $chord_atts['depth'],
            'main_color' => $chord_atts['main_color']
        )
    );
	 *
	 */

    // Escaping atts.
    $esc_class  = esc_attr('wl-timeline-widget');
    $esc_id     = esc_attr('wl-timeline-11121221231231');
	$esc_width  = esc_attr($timeline_atts['width']);
	$esc_height = esc_attr($timeline_atts['height']);

    $esc_post_id 	= esc_attr(13132412);//$post_id);
    $esc_depth		= esc_attr(2);//$timeline_atts['depth']);
    $esc_main_color = esc_attr('#aaa');//$timeline_atts['main_color']);
    
	// Building template.
    // TODO: in the HTML code there are static CSS rules. Move them to the CSS file.
    return <<<EOF
<div class="$esc_class" 
	id="$esc_id"
	data-post_id="$esc_post_id"
    data-depth="$esc_depth"
    data-main_color="$esc_main_color"
	style="width:$esc_width;
        height:$esc_height;
        background-color:$esc_main_color;
        margin-top:10px;
        margin-bottom:10px">
</div>
EOF;
}

/**
 * Registers the *wl-timeline* shortcode.
 */
function wl_shortcode_timeline_register()
{
    add_shortcode('wl-timeline', 'wl_shortcode_timeline');
}
add_action('init', 'wl_shortcode_timeline_register');
