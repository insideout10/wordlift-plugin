<?php

namespace Wordlift\Modules\Food_Kg;

class Page {

	public function register_hooks() {
		add_action( 'admin_init', [ $this, 'admin_init' ] );
	}

	public function admin_init() {
		add_submenu_page( 'wl_admin_menu', __( 'Ingredients', 'wordlift' ), __( 'Ingredients', 'wordlift' ),
			'manage_options', 'ingredients' );
	}

}
