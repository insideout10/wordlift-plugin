<?php

namespace Wordlift\Features;

class Response_Adapter {
	const WL_FEATURES = '_wl_features';
	const WL_1 = 'wl1';

	/**
	 * @var \Wordlift_Log_Service
	 */
	private $log;

	function __construct() {

		$this->log = \Wordlift_Log_Service::get_logger( get_class() );

		// Filter responses from the API calls to update the enabled features.
		add_filter( 'wl_api_service__response', array( $this, 'response' ), 10, 1 );

		// Initialize from `$_ENV`: this is currently required for Unit Tests, since `tests/bootstrap.php` loads WordLift
		// before it can actually query the enabled features via HTTP (mock), which would prevent files from being included.
//		$this->init_from_env();

		// Register the `wl_features__enable__{feature-name}` filters.
		$this->register_filters();

		// Hook to updates to the features setting to refresh the features' filters.
		add_action( 'update_option_' . self::WL_FEATURES, array( $this, 'register_filters' ), 10, 0 );

	}

	function response( $response ) {

		$headers = wp_remote_retrieve_headers( $response );

		// Bail out if the `wl1` header isn't defined.
		if ( ! isset( $headers[ self::WL_1 ] ) ) {
			return $response;
		}
		$wl1_as_base64_string = $headers[ self::WL_1 ];
		$wl1                  = json_decode( base64_decode( $wl1_as_base64_string ), true );

		$this->log->debug( "WL1 [ encoded :: $wl1_as_base64_string ] " . var_export( $wl1, true ) );

		// Update the feature flags. There's no need to check here if values differ (thus avoiding a call to db), since
		// WordPress does that in `update_option`.
		if ( isset( $wl1['features'] ) ) {
			if ( update_option( self::WL_FEATURES, (array) $wl1['features'], true ) ) {
				$this->register_filters();
			}
		}

		return $response;
	}

	/**
	 * Registers the feature filters.
	 */
	function register_filters() {

		$this->log->debug( 'Registering feature filters...' );

		foreach ( (array) get_option( self::WL_FEATURES, array() ) as $name => $enabled ) {
			// Remove previous filters.
			remove_filter( "wl_feature__enable__${name}", '__return_true' );
			remove_filter( "wl_feature__enable__${name}", '__return_false' );

			$callback = ( $enabled ? '__return_true' : '__return_false' );
			add_filter( "wl_feature__enable__${name}", $callback );
		}

	}

	private function init_from_env() {
		$features = array_reduce( array_filter( array_keys( $_ENV ), function ( $key ) {
			return preg_match( '~^WL_FEATURES__.*~', $key );
		} ), function ( $features, $env ) {
			$name              = strtolower( str_replace( '_', '-', substr( $env, strlen( 'WL_FEATURES__' ) ) ) );
			$value             = wp_validate_boolean( getenv( $env ) );
			$features[ $name ] = $value;

			return $features;
		}, array() );

		update_option( self::WL_FEATURES, (array) $features, true );

	}

}
