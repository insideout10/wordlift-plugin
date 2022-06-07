<?php

namespace Wordlift\Modules\Acf4so;

class Notices {

	/**
	 * @var Plugin
	 */
	private $acf4so_plugin;

	function __construct( Plugin $plugin ) {
		$this->acf4so_plugin = $plugin;
	}

	public function register_hooks() {
		add_action( 'admin_notices', [ $this, 'admin_notices' ] );
	}

	public function admin_notices() {

		$is_package_type_supported = $this->is_package_type_supported();

		$is_woocommerce_plugin_installed = defined( 'WL_WOO_VERSION' );

		if ( ! $is_package_type_supported && ! $is_woocommerce_plugin_installed ) {
			// Dont display notice.
			return;
		}

		if ( ! $this->acf4so_plugin->is_plugin_installed() ) {
			?>
            <div class="notice notice-error is-dismissible">
                <p><?php esc_html_e( "WordLift detected that Advanced Custom Fields for Schema.org is not installed and, you're loosing out on full Schema.org support. Click here to install and reactivate.", 'wordlift' ); ?></p>
            </div>
			<?php
			// Dont display notice.
			return;
		}

		if ( ! $this->acf4so_plugin->is_plugin_activated() ) {
			?>
            <div class="notice notice-error is-dismissible">
                <p><?php esc_html_e( "WordLift detected that Advanced Custom Fields for Schema.org is deactivated and, you're loosing out on full Schema.org support. Click here to reactivate.", 'wordlift' ); ?></p>
            </div>
			<?php
			// Dont display notice.
			return;
		}

		/**
		 * 1. When package type is supported and acf4so not installed or activated then the notice should appear.
		 * 2. When woocommerce plugin installed and acf4so not installed or activated then the notice should appear.
		 */


	}

	/**
	 * @return bool
	 */
	private function is_package_type_supported() {
		return apply_filters( 'wl_feature__enable__entity-types-professional', false ) ||
		       apply_filters( 'wl_feature__enable__entity-types-business', false );
	}

}