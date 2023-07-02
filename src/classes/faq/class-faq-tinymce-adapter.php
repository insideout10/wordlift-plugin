<?php
/**
 * This file defines the adapter for tinymce.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @package Wordlift\FAQ
 */

namespace Wordlift\Faq;

use Wordlift;

class Faq_Tinymce_Adapter {

	const FAQ_TINYMCE_PLUGIN_NAME = 'wl_faq_tinymce';

	const FAQ_TINYMCE_ADD_BUTTON_ID = 'wl-faq-toolbar-button';

	/**
	 * Add a list of custom tags which is to be used by our highlighting program.
	 *
	 * @param $init_array
	 *
	 * @return array
	 */
	public function register_custom_tags( $init_array ) {
		$opts                                   = '~wl-faq-question,~wl-faq-answer';
		$init_array['custom_elements']         .= ( empty( $init_array['custom_elements'] ) ? '' : ',' ) . $opts;
		$init_array['extended_valid_elements'] .= ( empty( $init_array['extended_valid_elements'] ) ? '' : ',' ) . $opts;

		return $init_array;
	}

	public function register_faq_tinymce_plugin( $plugins ) {
		/**
		 * Registering the tinymce plugin for FAQ here.
		 *
		 * @since 3.26.0
		 */
		$version                                  = Wordlift::get_instance()->get_version();
		$plugins[ self::FAQ_TINYMCE_PLUGIN_NAME ] = plugin_dir_url( dirname( __DIR__ ) ) . 'js/dist/tinymce-faq-plugin.full.js?ver=' . $version;

		return $plugins;
	}

	public function register_faq_toolbar_button( $buttons ) {
		array_push( $buttons, self::FAQ_TINYMCE_ADD_BUTTON_ID );

		return $buttons;
	}

}
