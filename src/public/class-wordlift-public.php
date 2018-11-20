<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://wordlift.io
 * @since      1.0.0
 *
 * @package    Wordlift
 * @subpackage Wordlift/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wordlift
 * @subpackage Wordlift/public
 * @author     WordLift <hello@wordlift.io>
 */
class Wordlift_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since 3.19.3 Register the `wordlift-ui` css.
	 * @since 3.19.2 The call to this function is commented out in `class-wordlift.php` because `wordlift-public.css`
	 *               is empty.
	 * @since 1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * An instance of this class should be passed to the run() function
		 * defined in Wordlift_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wordlift_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		/**
		 * Add the `wordlift-font-awesome` unless some 3rd party sets the flag to false.
		 *
		 * @since 3.19.3
		 *
		 * @param bool $include Whether to include or not font-awesome (default true).
		 */
		$deps = apply_filters( 'wl_include_font_awesome', true )
			? array( 'wordlift-font-awesome' )
			: array();
		wp_register_style( 'wordlift-font-awesome', plugin_dir_url( dirname( __FILE__ ) ) . 'css/wordlift-font-awesome' . ( ! defined( 'SCRIPT_DEBUG' ) || ! SCRIPT_DEBUG ? '.min' : '' ) . '.css', array(), $this->version, 'all' );
		wp_register_style( 'wordlift-ui', plugin_dir_url( dirname( __FILE__ ) ) . 'css/wordlift-ui' . ( ! defined( 'SCRIPT_DEBUG' ) || ! SCRIPT_DEBUG ? '.min' : '' ) . '.css', $deps, $this->version, 'all' );

		// You need to re-enable the enqueue_styles in `class-wordlift.php` to make this effective.
		//
		// @see https://github.com/insideout10/wordlift-plugin/issues/821
		//
		// wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wordlift-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wordlift_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wordlift_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		$settings = self::get_settings();

		// Note that we switched the js to be loaded in footer, since it is loading
		// the json-ld representation.
		wp_enqueue_script( $this->plugin_name, self::get_public_js_url(), array(), $this->version, true );
		wp_localize_script( $this->plugin_name, 'wlSettings', $settings );

		/*
		 * Add WordLift's version.
		 *
		 * @since 3.19.4
		 *
		 * @see https://github.com/insideout10/wordlift-plugin/issues/843.
		 */
		wp_localize_script( $this->plugin_name, 'wordlift', array(
			'version' => $this->version,
		) );

	}

	/**
	 * Get the settings array.
	 *
	 * @since 3.19.1
	 *
	 * @return array An array with the settings.
	 */
	public static function get_settings() {

		// Prepare a settings array for client-side functions.
		$settings = array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'apiUrl'  => get_home_url( null, 'wl-api/' ),
		);

		// If we're in a single page, then print out the post id.
		if ( is_singular() ) {
			$settings['postId'] = get_the_ID();
		}

		// Add flag that we are on home/blog page.
		if ( is_home() || is_front_page() ) {
			$settings['isHome'] = true;
		}

		// By default only enable JSON-LD on supported entity pages (includes
		// `page`, `post` and `entity` by default) and on the home page.
		//
		// @see https://github.com/insideout10/wordlift-plugin/issues/733
		$jsonld_enabled = is_home() || is_front_page() || Wordlift_Entity_Type_Service::is_valid_entity_post_type( get_post_type() );

		// Add the JSON-LD enabled flag, when set to false, the JSON-lD won't
		// be loaded.
		//
		// @see https://github.com/insideout10/wordlift-plugin/issues/642.
		$settings['jsonld_enabled'] = apply_filters( 'wl_jsonld_enabled', $jsonld_enabled );

		return $settings;
	}

	/**
	 * Get the public JavaScript URL.
	 *
	 * Using this function is encouraged, since the public JavaScript is also used by the {@link Wordlift_WpRocket_Adapter}
	 * in order to avoid breaking optimizations.
	 *
	 * @since 3.19.4
	 *
	 * @see https://github.com/insideout10/wordlift-plugin/issues/842.
	 *
	 * @return string The URL to the public JavaScript.
	 */
	public static function get_public_js_url() {

		return plugin_dir_url( dirname( __FILE__ ) ) . 'js/dist/bundle.js';
	}

}
