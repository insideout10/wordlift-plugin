<?php

namespace Wordlift\Metabox;

use Wordlift\Object_Type_Enum;

/**
 * Metaboxes.
 *
 * @since      3.1.0
 * @package    Wordlift
 * @subpackage Wordlift/admin/WL_Metabox
 */

/**
 * Define the {@link Wl_Metabox} class.
 *
 * @since      3.1.0
 * @package    Wordlift
 * @subpackage Wordlift/admin/WL_Metabox
 */
class Wl_Metabox extends Wl_Abstract_Metabox {

	/**
	 * WL_Metabox constructor.
	 *
	 * @since 3.1.0
	 */
	public function __construct() {
		parent::__construct();
		/**
		 * Filter: wl_feature__enable__metabox.
		 *
		 * @param bool whether the metabox should be shown, defaults to true.
		 *
		 * @return bool
		 * @since 3.28.1
		 */
		if ( apply_filters( 'wl_feature__enable__metabox', true ) && ! apply_filters( 'wl_feature__enable__pods-integration', false ) ) { //phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores

			// Add hooks to print metaboxes and save submitted data.
			add_action( 'add_meta_boxes', array( $this, 'add_main_metabox' ) );
			add_action( 'wl_linked_data_save_post', array( $this, 'save_form' ) );

			// Enqueue js and css.
			$this->enqueue_scripts_and_styles();

		}

	}

	public function save_form( $post_id ) {
		$this->save_form_data( $post_id, Object_Type_Enum::POST );
	}

}
