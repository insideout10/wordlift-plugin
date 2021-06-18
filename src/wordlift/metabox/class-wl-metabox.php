<?php
/**
 * Metaboxes.
 *
 * @since      3.1.0
 * @package    Wordlift
 * @subpackage Wordlift/admin/WL_Metabox
 */
/**
 * Define the {@link WL_Metabox} class.
 *
 * @since      3.1.0
 * @package    Wordlift
 * @subpackage Wordlift/admin/WL_Metabox
 */
class WL_Metabox extends Wl_Abstract_Meta_Box {

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
		if ( apply_filters( 'wl_feature__enable__metabox', true ) ) {

			// Add hooks to print metaboxes and save submitted data.
			add_action( 'add_meta_boxes', array( $this, 'add_main_metabox' ) );
			$that = $this;
			add_action( 'wl_linked_data_save_post', function ( $post_id ) use ( $that ) {
				$that->save_form_data( $post_id, $that::POST );
			} );

			// Enqueue js and css.
			$this->enqueue_scripts_and_styles();

		}

	}


}
