<?php
/**
 * @since 3.34.0
 */

namespace Wordlift\Webhooks;

class Webhooks_Settings {

	public function init() {

		add_filter(
			'wl_admin_page_tabs',
			function ( $tabs ) {
				$tabs[] = array(
					'slug'  => 'webhooksobject-settings',
					'title' => __( 'Webhooks Settings', 'wordlift' ),
				);

				return $tabs;
			}
		);

		// Registering the option group during setup
		add_action( 'admin_init', array( &$this, 'wl_admin_register_setting' ) ); // phpcs:ignore MediaWiki.Usage.ReferenceThis.Found
	}

	/**
	 * Call back function to register the option group
	 */
	public function wl_admin_register_setting() {
		register_setting(
			'wl_settings__webhooks',
			Webhooks_Loader::URLS_OPTION_NAME,
			array(
				$this,
				'sanitize_callback',
			)
		);
		add_settings_section(
			'wl_settings__webhooks__general',
			__( 'Webhooks Settings', 'wordlift' ),
			function () {
				esc_html_e( 'Set one or more URLs that should be called when data is changed.', 'wordlift' );
			},
			'wl_settings__webhooks'
		);
		add_settings_field(
			'wl_settings__webhooks__general__urls',
			__( 'URLs:', 'wordlift' ),
			function () {
				?>
			<textarea id="wl_settings__webhooks__general__urls"
					  name="wl_webhooks_urls"><?php echo esc_html( get_option( Webhooks_Loader::URLS_OPTION_NAME, '' ) ); ?></textarea>
				<?php
			},
			'wl_settings__webhooks',
			'wl_settings__webhooks__general'
		);
	}

	/**
	 * Callback function to process the data optioned from Webhook admin settings page
	 *
	 * @param string $values
	 *
	 * @return string
	 */
	public function sanitize_callback( $values ) {

		return implode(
			"\n",
			array_unique(
				array_filter(
					explode( "\n", str_replace( array( "\r\n", "\r" ), "\n", $values ) ),
					function ( $value ) {
						return filter_var( $value, FILTER_VALIDATE_URL ) && preg_match( '@^https?://.*$@', $value );
					}
				)
			)
		);
	}
}
