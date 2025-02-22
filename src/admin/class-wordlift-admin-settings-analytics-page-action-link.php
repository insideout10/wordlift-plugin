<?php
/**
 * Action Link: Settings Page Action Link.
 *
 * Provide a `Settings` action link in the Plugins page under `WordLift`.
 *
 * @since      3.21.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * Define the {@link Wordlift_Admin_Settings_Analytics_Page_Action_Link} used to add an
 * analytics settings page for the plugin.
 *
 * @since      3.21.0
 * @package    Wordlift
 * @subpackage Wordlift/analytics
 */
class Wordlift_Admin_Settings_Analytics_Page_Action_Link {

	/**
	 * The {@link Wordlift_Admin_Settings_Analytics_Page} instance.
	 *
	 * @since  3.11.0
	 * @access private
	 * @var \Wordlift_Admin_Settings_Analytics_Page $settings_page The {@link Wordlift_Admin_Settings_Analytics_Page} instance.
	 */
	private $settings_page;

	/**
	 * Create a {@link Wordlift_Admin_Settings_Analytics_Page_Action_Link} instance.
	 *
	 * @since  3.11.0
	 *
	 * @param \Wordlift_Admin_Settings_Analytics_Page $settings_page The {@link Wordlift_Admin_Settings_Analytics_Page} instance.
	 */
	public function __construct( $settings_page ) {

		$this->settings_page = $settings_page;
	}

	/**
	 * Create a link to the WordLift settings page.
	 *
	 * @since 3.11.0
	 *
	 * @param array $links An array of links.
	 *
	 * @return array An array of links including those added by the plugin.
	 */
	public function action_links( $links ) {

		// Get the menu slug from the page, then prepare the path, hence the url.
		$menu_slug = $this->settings_page->get_menu_slug();
		$path      = "admin.php?page=$menu_slug";
		$url       = get_admin_url( null, $path );

		// Add our own link to the list of links.
		return array_merge(
			$links,
			array(
				sprintf(
					'<a href="%1$s">%2$s</a>',
					esc_url( $url ),
					esc_html__( 'Analytics Settings', 'wordlift' )
				),
			)
		);
	}
}
