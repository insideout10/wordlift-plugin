<?php
/**
 * SEO Ultimate adapter for the Prefixes module.
 *
 * @since 3.0.0
 */

if ( class_exists( 'SU_Module' ) ) {

	class SU_Configuration extends SU_Module {
		static function get_module_title() {
			return __( 'WordLift Configuration', 'wordlift' );
		}

		static function get_menu_title() {
			return __( 'WordLift Configuration', 'wordlift' );
		}

		/**
		 * This method is called to display the admin page inside of SEO Ultimate.
		 *
		 * @since 3.0.0
		 *
		 * @uses ::wl_configuration_admin_menu_callback to display the admin page.
		 */
		function admin_page_contents() {

			if ($this->should_show_sdf_theme_promo()) {
				echo "\n\n<div class='row'>\n";
				echo "\n\n<div class='col-sm-8 col-md-9'>\n";
			}

			wl_configuration_admin_menu_callback( false );

			if ($this->should_show_sdf_theme_promo()) {
				echo "\n\n</div>\n";
				echo "\n\n<div class='col-sm-4 col-md-3'>\n";
				$this->promo_sdf_banners();
				echo "\n\n</div>\n";
				echo "\n\n</div>\n";
			}

		}

		function add_help_tabs( $screen ) {

			// TODO: write some help text here.
			$screen->add_help_tab( array(
				'id'      => 'wl-configuration-overview'
			,
				'title'   => __( 'Overview', 'seo-ultimate' )
			,
				'content' => __( "
<ul>
	<li><strong>What it does:</strong> Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</li>
	<li><strong>Why it helps:</strong> Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</li>
	<li><strong>How to use it:</strong> Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</li>
</ul>

<p>If there are no 404 errors in the log, this is good and means there's no action required on your part.</p>
", 'seo-ultimate' )
			) );

		}
	}

}
