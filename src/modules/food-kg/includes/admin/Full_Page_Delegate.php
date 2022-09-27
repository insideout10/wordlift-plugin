<?php

namespace Wordlift\Modules\Food_Kg\Admin;

class Full_Page_Delegate implements Page_Delegate {

	public function render() {
		include WL_FOOD_KG_DIR_PATH . '/includes/admin/partials/main_ingredient.php';
	}

	public function admin_enqueue_scripts() {
		wp_enqueue_style( 'thickbox' );
		wp_enqueue_script( 'plugin-install' );

		wp_enqueue_style( 'wl-ingredients', plugin_dir_url( __FILE__ ) . '/assets/css/ingredients.css', array(), WORDLIFT_VERSION );
	}

}
