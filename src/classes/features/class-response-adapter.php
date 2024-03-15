<?php

namespace Wordlift\Features;

class Response_Adapter {
	const WL_FEATURES = '_wl_features';
	const WL_1        = 'wl1';

	public function __construct() {

		// Filter responses from the API calls to update the enabled features.
		add_filter( 'wl_api_service__response', array( $this, 'response' ), 10, 1 );

		// Initialize from `$_ENV`: this is currently required for Unit Tests, since `tests/bootstrap.php` loads WordLift
		// before it can actually query the enabled features via HTTP (mock), which would prevent files from being included.
		// $this->init_from_env();

		// Register the `wl_features__enable__{feature-name}` filters.
		$this->register_filters();

		// Hook to the updates to the features setting to refresh the features' filters.
		add_action( 'update_option_' . self::WL_FEATURES, array( $this, 'register_filters' ), 10, 0 );

	}

	public function response( $response ) {

		$headers = wp_remote_retrieve_headers( $response );

		// Bail out if the `wl1` header isn't defined.
		if ( ! isset( $headers[ self::WL_1 ] ) ) {
			return $response;
		}
		$wl1_as_base64_string = $headers[ self::WL_1 ];
		// phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_decode
		$wl1 = json_decode( base64_decode( $wl1_as_base64_string ), true );

		$updated_features = $wl1['features'];

		$existing_features = get_option( self::WL_FEATURES, array() );

		// Loop through updated features.
		foreach ( $updated_features as $feature_slug => $new_value ) {

			// We cant pass false because that indicates if the feature is active or not, null is used to represent the features which are
			// not set before.
			$old_value = array_key_exists( $feature_slug, $existing_features ) ? $existing_features[ $feature_slug ] : null;

			if ( $old_value !== $new_value ) {
				/**
				 * @param $feature_slug string The feature slug.
				 * @param $old_value null | boolean Null represents the feature flag was not set before.
				 * @param $new_value boolean True or false.
				 *
				 * @since 3.32.1
				 * Hook : `wl_feature__change__{feature_slug}`
				 * Action hook to be fired when there is a change in feature state.
				 */
				do_action( "wl_feature__change__$feature_slug", $new_value, $old_value, $feature_slug );
			}
		}

		if ( isset( $updated_features ) ) {

			if ( update_option( self::WL_FEATURES, (array) $updated_features, true ) ) {
				$this->register_filters();
			}
		}

		return $response;
	}

	/**
	 * Registers the feature filters.
	 */
	public function register_filters() {

		foreach ( (array) get_option( self::WL_FEATURES, array() ) as $name => $enabled ) {
			// Remove previous filters.
			remove_filter( "wl_feature__enable__{$name}", '__return_true' );
			remove_filter( "wl_feature__enable__{$name}", '__return_false' );

			$callback = ( $enabled ? '__return_true' : '__return_false' );
			add_filter( "wl_feature__enable__{$name}", $callback );
		}

	}

	private function init_from_env() {
		$features = array_reduce(
			array_filter(
				array_keys( $_ENV ),
				function ( $key ) {
					return preg_match( '~^WL_FEATURES__.*~', $key );
				}
			),
			function ( $features, $env ) {
				$name              = strtolower( str_replace( '_', '-', substr( $env, strlen( 'WL_FEATURES__' ) ) ) );
				$value             = wp_validate_boolean( getenv( $env ) );
				$features[ $name ] = $value;

				return $features;
			},
			array()
		);

		update_option( self::WL_FEATURES, (array) $features, true );
	}

}
