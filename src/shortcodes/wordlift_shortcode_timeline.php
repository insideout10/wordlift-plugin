<?php

/**
 * Retrieve timeline events.
 *
 * @used-by wl_shortcode_timeline_ajax
 */
function wl_shortcode_timeline_get_events( $post_id=null ) {
	
	// Build list of event-entities.
	$entities = null;
	if( is_null($post_id) ) {
		// Global timeline. Here we search for events that are from today on.
		$entities = '';
	} else {
		// Post-specific timeline. Search for event-entities in the post itself.
		
		// Get list of referenced entities.
		$entities = wl_get_referenced_entity_ids($post_id);
	}
	
	$events = array();
	if( is_null($entities) ) {
		// PROOOBLEEEEMSSSSZ
	} else {
		// Only keep the entities that represent an event
		foreach($entities as $e) {
			$candidate = get_post_meta( $e );
			// Is it an event?
			if( isset( $candidate[WL_CUSTOM_FIELD_CAL_DATE_START]) ||
				isset( $candidate[WL_CUSTOM_FIELD_CAL_DATE_END]) ) {
				$events[] = $e;
			}
		}
	}
	
	return $events;
}

/**
 * Convert timeline events to JSON.
 *
 * @used-by wl_shortcode_timeline_ajax
 */
function wl_shortcode_timeline_to_json( $events ) {
	
	// If there are no events, return empty JSON
	if( empty($events) || is_null($events) )
		return json_encode('');
	
	// Model data from:
	// https://github.com/NUKnightLab/TimelineJS/blob/master/examples/example_json.json
	
	$result = new stdClass();
	$timeline = new stdClass();
	
	$timeline->type = 'default';
	$timeline->startDate = date('Y,m,d');	// this param is not working...
	$timeline->date = array();
	
	foreach( $events as $ev ) {
		// Retrieve event info.
		$eventObj = get_post($ev);
		$eventMeta = get_post_meta($ev);
		
		/////////// to be deleted ///////////
		$timeline->debug->eventObj = $eventObj;
		$timeline->debug->eventMeta = $eventMeta;
		//////////////////////
		
		// Build date object for the timeline.
		$dateObj = new stdClass();
		$dateObj->startDate = str_replace('-', ',', $eventMeta[WL_CUSTOM_FIELD_CAL_DATE_START][0]);
		$dateObj->endDate = str_replace('-', ',', $eventMeta[WL_CUSTOM_FIELD_CAL_DATE_END][0]);
		$dateObj->headline = $eventObj->post_title;
		$dateObj->text = $eventObj->post_content;
		$dateObj->asset = new stdClass();
		$dateObj->asset->media = '';
		$dateObj->asset->credit = '';
		$dateObj->asset->caption = '';
		$timeline->date[] = $dateObj;
	}
	
	$result->timeline = $timeline;
	return json_encode( $result );	
}

/**
 * Retrieve timeline events and output them in JSON.
 *
 * @uses wl_shortcode_timeline_get_events
 * @uses wl_shortcode_timeline_to_json
 */
function wl_shortcode_timeline_ajax()
{
	// Get the ID of the post who requested the timeline.
	$post_id;
	if(isset($_REQUEST['post_id']))
		// Build post-specific timeline.
		$post_id = $_REQUEST['post_id'];
	else {
		// Build global timeline.
		$post_id = null;
	}
	
    ob_clean();
    $result = wl_shortcode_timeline_get_events( $post_id );
    $result = wl_shortcode_timeline_to_json( $result );
    echo $result;
    die();
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
        'height'     => '600px',
        'main_color' => '#ddd'
    ), $atts);
	
	// Add timeline library.
	wp_enqueue_script('timelinejs', plugins_url('bower_components/NUKnightLab-TimelineJS/build/js/storyjs-embed.js', __FILE__));
	wp_enqueue_script('timelinejs2', plugins_url('bower_components/NUKnightLab-TimelineJS/build/js/timeline.js', __FILE__));

	// Add wordlift-ui script.
	wp_enqueue_script( 'wordlift-ui', plugins_url('js/wordlift.ui.js', __FILE__) );
	wp_localize_script( 'wordlift-ui', 'wl_timeline_params', array(
            'ajax_url'   => admin_url('admin-ajax.php'),	// Global param
            'action'     => 'wl_timeline'					// Global param
        )
    );
	 
	$post_id = get_the_ID();

    // Escaping atts.
    $esc_class  = esc_attr('wl-timeline');
    $esc_id     = esc_attr('wl-timeline-' . $post_id);
	$esc_width  = esc_attr($timeline_atts['width']);
	$esc_height = esc_attr($timeline_atts['height']);

    $esc_post_id 	= esc_attr($post_id);
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
