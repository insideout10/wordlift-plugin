<?php
/**
 * Services: AMP Services.
 *
 * The file defines a class for AMP related manipulations.
 *
 * @link       https://wordlift.io
 *
 * @since      3.12.0
 *
 * @package    Wordlift
 * @subpackage Wordlift/public
 */

/**
 * Handles AMP related manipulation in the generated HTML of entity pages
 *
 * @since      3.12.0
 * @package    Wordlift
 * @subpackage Wordlift/public
 */
class Wordlift_AMP_Service {

	/**
	 * @inheritdoc
	 */
	function __construct() {

		// Integrate with automattic's AMP plugin if it is available
		if ( defined( 'AMP__VERSION' ) ) {
			add_action( 'amp_init', array(
				$this,
				'register_entity_cpt_with_amp_plugin',
			) );

			add_action( 'amp_post_template_footer', array( $this, 'amp_post_template_footer' ) );
		}

	}

	/**
	 * Register the `entity` post type with the AMP plugin.
	 *
	 * @since 3.12.0
	 */
	function register_entity_cpt_with_amp_plugin() {

		if ( defined( 'AMP_QUERY_VAR' ) ) {
			add_post_type_support( 'entity', AMP_QUERY_VAR );
		}

	}

	/**
	 * Hook to the `amp_post_template_footer` function to output our **async** script to AMP.
	 *
	 * We're **asynchronous** as requested by the AMP specs:
	 *
	 * ```
	 * Among the biggest optimizations is the fact that it makes everything that comes from external resources
	 * asynchronous, so nothing in the page can block anything from rendering.
	 * ```
	 *
	 * See https://www.ampproject.org/learn/overview/
	 *
	 * @since 3.19.1
	 */
	function amp_post_template_footer() {

		// Prepare the JavaScript URL.
		$url = plugin_dir_url( dirname( __FILE__ ) ) . 'js/dist/bundle.js?ver=' . Wordlift::get_instance()->get_version();

		$settings = Wordlift_Public::get_settings();

		// Force the jsonld_enabled setting to be 1 or 0 as this is what the script expects and wp_json_encode
		// may return `true` / `false`.
		$settings['jsonld_enabled'] = $settings['jsonld_enabled'] ? '1' : '0';

		$settings_as_string = wp_json_encode( $settings );

		echo "<script>window.wlSettings = $settings_as_string;</script>";
		echo "<script async src='$url'></script>";

	}

}
