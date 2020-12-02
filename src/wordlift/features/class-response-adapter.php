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

		add_filter( 'wl_api_service__response', array( $this, 'response' ), 10, 1 );

		foreach ( (array) get_option( self::WL_FEATURES, array() ) as $name => $enabled ) {
			$callback = ( $enabled ? '__return_true' : '__return_false' );
			add_filter( "wl_feature__enable__${name}", $callback );
		}

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
			update_option( self::WL_FEATURES, (array) $wl1['features'], true );
		}

		return $response;
	}

}
