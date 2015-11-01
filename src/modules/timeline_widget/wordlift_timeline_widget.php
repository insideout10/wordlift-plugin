<?php
/**
 * This file provides the Timeline Widget.
 */

/**
 * Process the *wl_timeline* shortcode, producing HTML code to embed the control.
 *
 * @since 3.0.0
 *
 * @param array $atts The configuration attributes.
 * @param string|null $content The shortcode inner content, or null if not existing.
 *
 * @return string The widget HTML code.
 */
function wordlift_timeline_widget_shortcode( $atts, $content = null ) {

//	// Extract attributes and set default values.
//	$params = shortcode_atts( array(
//		'width'     => '100%',
//		'height'    => '300px',
//		'latitude'  => 0.0,
//		'longitude' => 0.0,
//		'zoom'      => 5
//
//	), $atts );

	// Add TimelineJS css and library.
	wp_enqueue_script( 'timeline', plugin_dir_url( __FILE__ ) . 'bower_components/TimelineJS.build/build/css/timeline.css' );
	wp_enqueue_script( 'timeline', plugin_dir_url( __FILE__ ) . 'bower_components/TimelineJS.build/build/js/storyjs-embed.js', array( 'jquery' ) );
	wp_enqueue_style( 'wordlift-timeline-widget', plugin_dir_url( __FILE__ ) . 'modules/timeline_widget/css/wordlift_timeline_widget.css' );

	// Generate a unique ID for the element.
	$element_id = uniqid( 'wl-timeline-' );
	$url        = admin_url( 'admin-ajax.php?action=wl_sparql&slug=events-in&format=json' );
	$iframe_url = admin_url( 'admin-ajax.php?action=wl_redirect' );

	ob_start(); // Collect the buffer.
	wordlift_timeline_widget_html( $element_id, $url, $iframe_url );

	// Return the accumulated buffer.
	return ob_get_clean();

}

add_shortcode( 'wl_timeline', 'wordlift_timeline_widget_shortcode' );


/**
 * Produces the HTML code for the timeline widget.
 *
 * @since 3.0.0
 *
 * @return string The HTML code.
 */
function wordlift_timeline_widget_html( $element_id, $url, $iframe_url, $width = '100%', $height = '400', $language = 'en' ) {

	$element_id_a = esc_attr( $element_id );
	$element_id_j = json_encode( $element_id );
	$width_j      = json_encode( $width );
	$height_j     = json_encode( $height );
	$language_j   = json_encode( $language );
	$url_j        = json_encode( $url );

	?>
	<div id="<?php echo $element_id_a; ?>"></div>

	<script type="text/javascript">
		jQuery( function ( $ ) {

			$.ajax( <?php echo( $url_j ); ?>, {
				success: function ( data, status, xhr ) {

					$.each( data, function ( index, value ) {
						value.text = '<iframe src="<?php echo esc_attr( $iframe_url ); ?>&url=' +
							encodeURIComponent( value.url ) + '"></iframe>';
					} );

					createStoryJS( {
						type: 'timeline',
						width:      <?php echo( $width_j ); ?>,
						height:     <?php echo( $height_j ); ?>,
						source: {
							"timeline": {
								"headline": "Timeline",
								"type": "default",
//										        "text":"<p>Intro body text goes here, some HTML is ok</p>",
//										        "asset": {
//										            "media":"http://yourdomain_or_socialmedialink_goes_here.jpg",
//										            "credit":"Credit Name Goes Here",
//										            "caption":"Caption text goes here"
//										        },
								"date": data
//										        "era": [
//										            {
//										                "startDate":"2011,12,10",
//										                "endDate":"2011,12,11",
//										                "headline":"Headline Goes Here",
//										                "text":"<p>Body text goes here, some HTML is OK</p>",
//										                "tag":"This is Optional"
//										            }
//
//										        ]
							}
						},
						embed_id:   <?php echo( $element_id_j ); ?>,
						lang:       <?php echo( $language_j ); ?>
					} );

				}
			} );

		} );
	</script>
	<?php

}