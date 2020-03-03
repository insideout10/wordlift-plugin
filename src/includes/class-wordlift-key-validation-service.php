<?php
/**
 * Wordlift_Key_Validation_Service class
 *
 * The {@link Wordlift_Key_Validation_Service} class provides WordLift's key validation services.
 *
 * @link    https://wordlift.io
 *
 * @package Wordlift
 * @since   3.9.0
 */

/**
 * Define the {@link Wordlift_Key_Validation_Service} class.
 *
 * @since 3.9.0
 */
class Wordlift_Key_Validation_Service {

	/**
	 * A {@link Wordlift_Log_Service} instance.
	 *
	 * @since  3.14.0
	 * @access private
	 * @var \Wordlift_Log_Service $log A {@link Wordlift_Log_Service} instance.
	 */
	private $log;

	/**
	 * The {@link Wordlift_Configuration_Service} instance.
	 *
	 * @since  3.14.0
	 * @access private
	 * @var \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 */
	private $configuration_service;

	/**
	 * Create a {@link Wordlift_Key_Validation_Service} instance.
	 *
	 * @since 3.14.0
	 *
	 * @param \Wordlift_Configuration_Service $configuration_service The {@link Wordlift_Configuration_Service} instance.
	 */
	public function __construct( $configuration_service ) {

		$this->log = Wordlift_Log_Service::get_logger( 'Wordlift_Key_Validation_Service' );

		$this->configuration_service = $configuration_service;
        add_action( 'admin_init', array( $this, 'wl_load_plugin' ) );
        add_action( 'admin_notices', array( $this, 'wl_key_update_notice' ) );

	}

	/**
	 * Validate the provided key.
	 *
	 * @since 3.9.0
	 *
	 * @param string $key WordLift's key to validate.
	 *
	 * @return bool True if the key is valid, otherwise false.
	 */
	public function is_valid( $key ) {

		$this->log->debug( 'Validating key...' );

		// Request the account info as a way to validate the key

        $args = array_merge_recursive(
                    unserialize( WL_REDLINK_API_HTTP_OPTIONS ),
                            array(
                            'headers' => array( 
                                'Content-Type'    => 'application/json; charset=utf-8',
                                'X-Authorization' =>  $key )
                            ) 
                );

        $response = wp_remote_get( $this->configuration_service->get_accounts_info_by_key( $key ), $args );

        return $response;
	}

	/**
	 * This function is hooked to the `wl_validate_key` AJAX call.
	 *
	 * @since 3.9.0
	 */
	public function validate_key() {

		// Ensure we don't have garbage before us.
		ob_clean();

		// Check if we have a key.
		if ( ! isset( $_POST['key'] ) ) {
			wp_send_json_error( 'The key parameter is required.' );
		}
        $response = $this->is_valid( $_POST['key'] );
        $res_body = json_decode( wp_remote_retrieve_body( $response ), true );
        $url = $res_body['url'];
        
        //Set a response with valid set to true and messgae according to the key validity with url match
        if ( ! is_wp_error( $response ) && 200 === (int) $response['response']['code'] && $url == site_url() ) {
            $is_valid = true;
            $message = " ";           
        }

        if ( ! is_wp_error( $response ) && 200 === (int) $response['response']['code'] && $url != site_url() ) {
            $is_valid = false;
            $message = __( "The key is already used on another site, please contact us at hello@wordlift.io to move the key to another site.", 'wordlift' );
            Wordlift_Configuration_Service::get_instance()->set_key( '' );         
        }
        
        if ( is_wp_error( $response ) || 500 === (int) $response['response']['code'] ) {
            $is_valid = false;
            $message = "";
        }

		// Set a response with valid set to true or false according to the key validity with message.
		wp_send_json_success( array( 'valid' => $is_valid, 'message' => $message ) );

	}

    /**
     * This function is hooked `admin_init` to check _wl_blog_url.
     *
     */
    public function wl_load_plugin()
    {
        $wl_blog_url = get_option( '_wl_blog_url' );
        if ( !$wl_blog_url ) {
           update_option( '_wl_blog_url', site_url(), true );
        }
        if ( $wl_blog_url != site_url() ) {
            Wordlift_Configuration_Service::get_instance()->set_key( '' );  
            set_transient( 'wl-key-error-msg' , __( "Your web site URL has changed. To avoid data corruption, WordLift's key has been removed. Please provide a new key in WordLift Settings. If you believe this to be an error, please contact us at hello@wordlift.io", 'wordlift' ), 10 );
        }
    }

    /**
     * This function is hooked to the `admin_notices` to show admin notification.
     *
     */
    public function wl_key_update_notice() {
        if ( get_transient( 'wl-key-error-msg' ) ) {
        ?>
            <div class="updated notice is-dismissible error">
                <p><?php _e( get_transient('wl-key-error-msg'), 'wordlift' ); ?></p>
            </div>
        <?php
        }
    }
}
