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
			add_action( 'amp_init', array( $this, 'register_entity_cpt_with_amp_plugin' ) );
		}
	}

	function register_entity_cpt_with_amp_plugin() {
		add_post_type_support( 'entity', AMP_QUERY_VAR );
	}
}
