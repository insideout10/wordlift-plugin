<?php

namespace Wordlift\Modules\Food_Kg\Admin;

class Page {

	/**
	 * @var Page_Delegate
	 */
	private $delegate;

	/**
	 * @param Page_Delegate $full_page_delegate
	 * @param Page_Delegate $modal_page_delegate
	 */
	public function __construct( $full_page_delegate, $modal_page_delegate ) {
		$this->delegate = isset( $_GET['modal_window'] ) ? $modal_page_delegate : $full_page_delegate; //phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	public function register_hooks() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		if ( isset( $_GET['modal_window'] ) ) { //phpcs:ignore WordPress.Security.NonceVerification.Recommended
			add_action( 'in_admin_header', array( $this, 'remove_notices' ), 99 );
		}
	}

	/**
	 * Remove notices from ingredients modal.
	 */
	public function remove_notices() {
		remove_all_actions( 'admin_notices' );
		remove_all_actions( 'all_admin_notices' );
	}

	public function admin_menu() {
		add_submenu_page(
			'wl_admin_menu',
			__( 'Ingredients', 'wordlift' ),
			__( 'Ingredients', 'wordlift' ),
			'manage_options',
			'wl_ingredients',
			array( $this, 'render' )
		);
	}

	public function render() {
		$this->delegate->render();
	}

	public function admin_enqueue_scripts() {
		// Check that we are on the right screen
		if ( get_current_screen()->id === 'wordlift_page_wl_ingredients' ) {
			$this->delegate->admin_enqueue_scripts();
		}
	}

}
