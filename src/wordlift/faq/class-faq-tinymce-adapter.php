<?php
/**
 * This file defines the adapter for tinymce.
 *
 * @since 3.26.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * @package Wordlift\FAQ
 */

namespace Wordlift\FAQ;

use Wordlift;

class Faq_Tinymce_Adapter {

	const FAQ_TINYMCE_PLUGIN_NAME = "wl_faq_tinymce";

	public function register_faq_tinymce_plugin( $plugins ) {
		/**
		 * Registering the tinymce plugin for FAQ here.
		 * @since 3.26.0
		 */
		$version = Wordlift::get_instance()->get_version();
		$plugins[self::FAQ_TINYMCE_PLUGIN_NAME] = plugin_dir_url( dirname( __DIR__ ) ) . 'js/dist/tinymce-faq-plugin.js?ver=' . $version;
		return $plugins;
	}

	public function register_faq_toolbar_button( $buttons ) {
		array_push( $buttons, "wl-faq-toolbar-button" );
		return $buttons;
	}

}
