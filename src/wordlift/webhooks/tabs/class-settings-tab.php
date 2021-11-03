<?php
/**
 * @since 3.31.0
 * @author
 * Added for feature request 1496 (Webhooks)
 */
namespace Wordlift\Webhooks\Tabs;

class Settings_Tab {

	public function init() {

		add_filter( 'wl_admin_page_tabs', function ( $tabs ) {
			$tabs[] = array(
				'slug'  => 'webhooksobject-settings',
				'title' => __( 'Webhooks Settings', 'wordlift' )
			);
			return $tabs;
		} );

		// Registering the option group during setup
        add_action('admin_init', array( &$this, 'wl_admin_register_setting' ) );
	}

    // Call back function to register the option group
    public function wl_admin_register_setting() {
        add_option( 'wl_webhook_url', '');
        $args = array(
                'type' => 'string',
                'sanitize_callback' => array( $this, 'sanitize_callback' ),
                'default' => NULL,
                );
        register_setting(
                'wl_webhooks_settings',
                'wl_webhook_url',
                $args );
    }

    // Callback function to process the data optioned from Webhook admin settings page
    function sanitize_callback($url) {
        $url = filter_var( $url, FILTER_SANITIZE_URL );
        if ( filter_var( $url, FILTER_VALIDATE_URL ) ) {
            if( get_option( 'wl_webhook_url' ) ) {
                $url =  array_merge( get_option( 'wl_webhook_url' ), array( $url ) );
            }
            else {
                $url = array( $url );
            }
        } else {
            add_settings_error( 'wl_webhook_error', esc_attr( 'settings_updated' ), __( 'Please enter a valid url' ), 'error' );
            $url = get_option( 'wl_webhook_url' );
        }
        // Code to set the autoload to no as by default it is set to yes
        global $wpdb;
        $wpdb->update( 'wp_options', array( 'autoload' => 'no'),array('option_name'=>'wl_webhook_url'));

        //return the processed data
        return $url;
    }

}
