<?php

namespace Wordlift\Modules\Food_Kg\Admin;

use Wordlift\Content\Wordpress\Wordpress_Content_Id;
use Wordlift\Content\Wordpress\Wordpress_Content_Service;

class Main_Ingredient_Modal_Page_Delegate implements Page_Delegate {
	public function render() {
		$post_id = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT );

		$content_service = Wordpress_Content_Service::get_instance();
		$content_id      = Wordpress_Content_Id::create_post( $post_id );

		// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		$value = $content_service->get_about_jsonld( $content_id );

		include WL_FOOD_KG_DIR_PATH . '/includes/admin/partials/jsonld.php';
	}

	public function admin_enqueue_scripts() {
		wp_enqueue_style( 'wl-food-kg-jsonld', plugin_dir_url( __FILE__ ) . '/partials/jsonld.css', array(), WORDLIFT_VERSION );

		// Enqueue code editor and settings for manipulating HTML.
		$settings = wp_enqueue_code_editor(
			array(
				'type'      => 'application/ld+json',
				'minHeight' => '100%',
			)
		);

		wp_add_inline_script(
			'code-editor',
			sprintf(
				'jQuery( function() { wp.codeEditor.initialize( "jsonld", %s ); } );',
				wp_json_encode( $settings )
			)
		);
	}
}
