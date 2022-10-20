<?php

namespace Wordlift\Modules\Food_Kg\Admin;

class Ingredients_Full_Page_Delegate implements Page_Delegate {

	public function render() {
		include WL_FOOD_KG_DIR_PATH . '/includes/admin/partials/ingredients.php';
	}

	public function admin_enqueue_scripts() {
	}

}
