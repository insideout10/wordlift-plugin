<?php

/**
 * The wl_timeline shortcode displays an interactive timeline of events bound to the current post.
 *
 * @since 3.1.0
 */
class Wordlift_Timeline_Shortcode extends Wordlift_Shortcode {

	const SHORTCODE = 'wl_timeline';

	/**
	 * The Log service.
	 *
	 * @since 3.1.0
	 * @access private
	 * @var \Wordlift_Log_Service $log_service The Log service.
	 */
	private $log_service;

	/**
	 * Create a Wordlift_Timeline_Shortcode instance.
	 *
	 * @since 3.1.0
	 */
	public function __construct() {
		parent::__construct();

		$this->log_service = Wordlift_Log_Service::get_logger( 'Wordlift_Timeline_Shortcode' );

	}

	/**
	 * Renders the Timeline.
	 *
	 * @since 3.1.0
	 * @return string The rendered HTML.
	 */
	public function render( $atts ) {

		//extract attributes and set default values
		$timeline_atts = shortcode_atts( array(
			'width'  => '100%',
			'height' => '600px',
			'global' => false
		), $atts );

		// Add timeline library.
		wp_enqueue_script( 'timelinejs-storyjs-embed', dirname( plugin_dir_url( __FILE__ ) ) . '/bower_components/TimelineJS.build/build/js/storyjs-embed.js' );
		wp_enqueue_script( 'timelinejs', dirname( plugin_dir_url( __FILE__ ) ) . '/bower_components/TimelineJS.build/build/js/timeline-min.js' );

		// Enqueue the scripts for the timeline.
		$this->enqueue_scripts();

		wp_localize_script( 'wordlift-ui', 'wl_timeline_params', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ), // TODO: this parameter is already provided by WP
			'action'   => 'wl_timeline'
		) );

		// Get the current post id or set null if global is set to true.
		$post_id = ( $timeline_atts['global'] ? null : get_the_ID() );

		// Generate a unique ID for this timeline.
		$element_id = uniqid( 'wl-timeline-' );

		// Escaping atts.
		$esc_width    = esc_attr( $timeline_atts['width'] );
		$esc_height   = esc_attr( $timeline_atts['height'] );
		$data_post_id = ( isset( $post_id ) ? "data-post-id='$post_id'" : '' );

		if ( WP_DEBUG ) {
			$this->log_service->trace( "Creating a timeline widget [ element id :: $element_id ][ post id :: $post_id ]" );
		}

		// Building template.
		// TODO: in the HTML code there are static CSS rules. Move them to the CSS file.
		return <<<EOF
<div class="wl-timeline" id="$element_id" $data_post_id
	style="width:$esc_width; height:$esc_height; margin-top:10px; margin-bottom:10px">
</div>
EOF;

	}

}
