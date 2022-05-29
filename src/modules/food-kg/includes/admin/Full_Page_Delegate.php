<?php

namespace Wordlift\Modules\Food_Kg\Admin;

class Full_Page_Delegate implements Page_Delegate {

	function render() {
		include WL_FOOD_KG_DIR_PATH . '/includes/admin/partials/ingredients.php';
	}

	function admin_enqueue_scripts() {
		wp_enqueue_style( 'thickbox' );
		wp_enqueue_script( 'plugin-install' );
	}

}
