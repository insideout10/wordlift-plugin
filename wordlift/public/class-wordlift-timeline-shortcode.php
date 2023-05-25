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
		'af',
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

		$this->register_block_type();

	}

	public function get_timelinejs_default_options() {
		return array(
			'debug'                            => defined( 'WP_DEBUG' ) && WP_DEBUG,
			'height'                           => null,
			'width'                            => null,
			'is_embed'                         => false,
			'hash_bookmark'                    => false,
			'default_bg_color'                 => 'white',
			'scale_factor'                     => 2,
			'initial_zoom'                     => null,
			'zoom_sequence'                    => '[0.5, 1, 2, 3, 5, 8, 13, 21, 34, 55, 89]',
			'timenav_position'                 => 'bottom',
			'optimal_tick_width'               => 100,
			'base_class'                       => 'tl-timeline',
			'timenav_height'                   => 150,
			'timenav_height_percentage'        => null,
			'timenav_mobile_height_percentage' => 40,
			'timenav_height_min'               => 150,
			'marker_height_min'                => 30,
			'marker_width_min'                 => 100,
			'start_at_slide'                   => 0,
			'start_at_end'                     => false,
			'menubar_height'                   => 0,
			'use_bc'                           => false,
			'duration'                         => 1000,
			'ease'                             => 'TL.Ease.easeInOutQuint',
			'slide_default_fade'               => '0%',
			'language'                         => $this->get_locale(),
			'ga_property_id'                   => null,
			'track_events'                     => "['back_to_start','nav_next','nav_previous','zoom_in','zoom_out']",
		);
	}

	/**
	 * Renders the Timeline.
	 *
	 * @param array $atts An array of shortcode attributes.
	 *
	 * @return string The rendered HTML.
	 * @since 3.1.0
	 */
	public function render( $atts ) {

		// extract attributes and set default values
		$settings = shortcode_atts(
			array_merge(
				$this->get_timelinejs_default_options(),
				array(
					'global'            => false,
					'display_images_as' => 'media',
					'excerpt_length'    => 55,
				)
			),
			$atts
		);

		// Load the TimelineJS stylesheets and scripts.
		wp_enqueue_style( 'timelinejs', dirname( plugin_dir_url( __FILE__ ) ) . '/timelinejs/css/timeline.css', array(), WORDLIFT_VERSION );
		wp_enqueue_script( 'timelinejs', dirname( plugin_dir_url( __FILE__ ) ) . '/timelinejs/js/timeline' . ( ! defined( 'SCRIPT_DEBUG' ) || ! SCRIPT_DEBUG ? '-min' : '' ) . '.js', array(), WORDLIFT_VERSION, false );

		// Enqueue the scripts for the timeline.
		$this->enqueue_scripts();

		// Provide the script with options.
		wp_localize_script(
			'timelinejs',
			'wl_timeline_params',
			array(
				'ajax_url'          => admin_url( 'admin-ajax.php' ),
				// TODO: this parameter is already provided by WP
				'action'            => 'wl_timeline',
				'wl_timeline_nonce' => wp_create_nonce( 'wl_timeline' ),
				// These settings apply to our wl_timeline AJAX endpoint.
				'display_images_as' => $settings['display_images_as'],
				'excerpt_length'    => $settings['excerpt_length'],
				// These settings apply to the timeline javascript client.
				'settings'          => array_filter(
					$settings,
					function ( $value ) {
						// Do not set NULL values.
						return ( null !== $value );
					}
				),
			)
		);

		// Get the current post id or set null if global is set to true.
		$post_id = ( $settings['global'] ? null : get_the_ID() );

		// Escaping atts.
		$style        = sprintf( 'style="%s%s"', isset( $settings['width'] ) ? "width:{$settings['width']};" : '', isset( $settings['height'] ) ? "height:{$settings['height']};" : '' );
		$data_post_id = ( isset( $post_id ) ? "data-post-id='$post_id'" : '' );

		// Generate a unique ID for this timeline.
		$element_id = uniqid( 'wl-timeline-' );

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			$this->log_service->trace( "Creating a timeline widget [ element id :: $element_id ][ post id :: $post_id ]" );
		}

		// Building template.
		return sprintf( '<div class="wl-timeline-container" %s><div class="wl-timeline" id="%s" %s></div></div>', $style, $element_id, $data_post_id );
	}

	private function register_block_type() {

		$scope = $this;

		add_action(
			'init',
			function () use ( $scope ) {
				if ( ! function_exists( 'register_block_type' ) ) {
					// Gutenberg is not active.
					return;
				}

				register_block_type(
					'wordlift/timeline',
					array(
						'editor_script'   => 'wl-block-editor',
						'render_callback' => function ( $attributes ) use ( $scope ) {
							$attr_code          = '';
							$timelinejs_options = json_decode( $attributes['timelinejs_options'], true );
							unset( $attributes['timelinejs_options'] );
							$attributes_all = array_merge( $attributes, $timelinejs_options );
							foreach ( $attributes_all as $key => $value ) {
								if ( $value && strpos( $value, '[' ) === false && strpos( $value, ']' ) === false ) {
									$attr_code .= $key . '="' . $value . '" ';
								}
							}

							return '[' . $scope::SHORTCODE . ' ' . $attr_code . ']';
						},
						'attributes'      => array(
							'display_images_as'  => array(
								'type'    => 'string',
								'default' => 'media',
							),
							'excerpt_length'     => array(
								'type'    => 'number',
								'default' => 55,
							),
							'global'             => array(
								'type'    => 'bool',
								'default' => false,
							),
							'timelinejs_options' => array(
								'type'    => 'string', // https://timeline.knightlab.com/docs/options.html
								'default' => wp_json_encode( $scope->get_timelinejs_default_options(), JSON_PRETTY_PRINT ),
							),
							'preview'            => array(
								'type'    => 'boolean',
								'default' => false,
							),
							'preview_src'        => array(
								'type'    => 'string',
								'default' => WP_CONTENT_URL . '/plugins/wordlift/images/block-previews/timeline.png',
							),
						),
					)
				);
			}
		);
	}

	/**
	 * Return the locale for the TimelineJS according to WP's configured locale and
	 * support TimelineJS locales. If WP's locale is not supported, english is used.
	 *
	 * @return string The locale (2 letters code).
	 * @since 3.7.0
	 */
	private function get_locale() {

		// Get the first 2 letters.
		$locale = substr( get_locale(), 0, 2 );

		// Check that the specified locale is supported otherwise use English.
		return in_array( $locale, self::$supported_locales, true ) ? $locale : 'en';
	}

}
