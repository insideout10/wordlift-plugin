<?php

require_once ABSPATH . 'wp-admin/includes/upgrade.php';

/**
 * @since 3.36.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Wordlift_Install_3_36_0 extends Wordlift_Install {

	/**
	 * {@inheritdoc}
	 */
	protected static $version = '3.36.1';

	public function install() {

		$is_woocommerce_extension_installed = defined( 'WL_WOO_VERSION' );

		if ( $is_woocommerce_extension_installed ) {
			// phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
			do_action( 'wl_install_and_activate_advanced-custom-fields-for-schema-org' );
		}
	}

}
