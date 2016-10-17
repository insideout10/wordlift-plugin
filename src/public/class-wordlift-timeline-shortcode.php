<?php

/**
 * The wl_timeline shortcode displays an interactive timeline of events bound to the current post.
 *
 * @since 3.1.0
 */
class Wordlift_Timeline_Shortcode extends Wordlift_Shortcode {

	const SHORTCODE = 'wl_timeline';

	/**
	 * The list of locales supported by TimelineJS (correspond to the list of
	 * files in the locale subfolder).
	 *
	 * @since 3.7.0
	 * @var array An array of two-letters language codes.
	 */
	private static $supported_locales = array(
		'ur',
		'uk',
		'tr',
		'tl',
		'th',
		'te',
		'ta',
		'sv',
		'sr',
		'sl',
		'sk',
		'si',
		'ru',
		'ro',
		'rm',
		'pt',
		'pl',
		'no',
		'nl',
		'ne',
		'ms',
		'lv',
		'lt',
		'lb',
		'ko',
		'ka',
		'ja',
		'iw',
		'it',
		'is',
		'id',
		'hy',
		'hu',
		'hr',
		'hi',
		'he',
		'gl',
		'ga',
		'fy',
		'fr',
		'fo',
		'fi',
		'fa',
		'eu',
		'et',
		'es',
		'eo',
		'en',
		'el',
		'de',
		'da',
		'cz',
		'ca',
		'bg',
		'be',
		'ar',
		'af'
	);

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
			'debug'                            => defined( 'WP_DEBUG' ) && WP_DEBUG,
			'height'                           => NULL,
			'width'                            => NULL,
			'is_embed'                         => FALSE,
			'hash_bookmark'                    => FALSE,
			'default_bg_color'                 => 'white',
			'scale_factor'                     => 2,
			'initial_zoom'                     => NULL,
			'zoom_sequence'                    => '[0.5, 1, 2, 3, 5, 8, 13, 21, 34, 55, 89]',
			'timenav_position'                 => 'bottom',
			'optimal_tick_width'               => 100,
			'base_class'                       => 'tl-timeline',
			'timenav_height'                   => 150,
			'timenav_height_percentage'        => NULL,
			'timenav_mobile_height_percentage' => 40,
			'timenav_height_min'               => 150,
			'marker_height_min'                => 30,
			'marker_width_min'                 => 100,
			'marker_padding'                   => 5,
			'start_at_slide'                   => 0,
			'start_at_end'                     => FALSE,
			'menubar_height'                   => 0,
			'use_bc'                           => FALSE,
			'duration'                         => 1000,
			'ease'                             => 'TL.Ease.easeInOutQuint',
			'slide_default_fade'               => '0%',
			'language'                         => $this->get_locale(),
			'ga_property_id'                   => NULL,
			'track_events'                     => "['back_to_start','nav_next','nav_previous','zoom_in','zoom_out']",
			'global'                           => FALSE
		), $atts );

		// Load the TimelineJS stylesheets and scripts.
		wp_enqueue_style( 'timelinejs', dirname( plugin_dir_url( __FILE__ ) ) . '/timelinejs/css/timeline.css' );
		wp_enqueue_script( 'timelinejs', dirname( plugin_dir_url( __FILE__ ) ) . '/timelinejs/js/timeline' . ( ! defined( 'SCRIPT_DEBUG' ) || ! SCRIPT_DEBUG ? '-min' : '' ) . '.js' );

		// Enqueue the scripts for the timeline.
		$this->enqueue_scripts();

		// Provide the script with options.
		wp_localize_script( 'timelinejs', 'wl_timeline_params', array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			// TODO: this parameter is already provided by WP
			'action'   => 'wl_timeline',
			// Enable debug in the client TimelineJS script.
			'debug'    => ! defined( 'SCRIPT_DEBUG' ) || ! SCRIPT_DEBUG ? 'false' : 'true',
			'language' => $this->get_locale(),
			'width'    => $timeline_atts['width'],
			'height'   => $timeline_atts['height'],
		) );

		// Get the current post id or set null if global is set to true.
		$post_id = ( $timeline_atts['global'] ? NULL : get_the_ID() );

		// Escaping atts.
		$esc_width    = esc_attr( $timeline_atts['width'] );
		$esc_height   = esc_attr( $timeline_atts['height'] );
		$data_post_id = ( isset( $post_id ) ? "data-post-id='$post_id'" : '' );

		// Generate a unique ID for this timeline.
		$element_id = uniqid( 'wl-timeline-' );

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

	/**
	 * Return the locale for the TimelineJS according to WP's configured locale and
	 * support TimelineJS locales. If WP's locale is not supported, english is used.
	 *
	 * @since 3.7.0
	 * @return string The locale (2 letters code).
	 */
	private function get_locale() {

		// Get the first 2 letters.
		$locale = substr( get_locale(), 0, 2 );

		// Check that the specified locale is supported otherwise use English.
		return in_array( $locale, self::$supported_locales ) ? $locale : 'en';
	}

}
