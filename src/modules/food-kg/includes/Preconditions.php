<?php

namespace Wordlift\Modules\Food_Kg;

class Preconditions {

	/**
	 * @var Notices
	 */
	private $notices;

	public function __construct( Notices $notices ) {
		$this->notices = $notices;
	}

	public function pass() {
		return $this->has_prerequisites() && $this->check_version();
	}

	private function has_prerequisites() {
		return defined( 'WPRM_VERSION' )
			   && class_exists( 'WP_Recipe_Maker' )
		       // phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
			   && apply_filters( 'wl_feature__enable__food-kg', false );
	}

	private function check_version() {
		$check = version_compare( WPRM_VERSION, '8.1.0', '>=' )
				 && version_compare( WPRM_VERSION, '8.4.0', '<' );

		if ( ! $check ) {
			$this->notices->queue(
				'warning',
				/* translators: %s: Detected WP Recipe Maker version. */
				sprintf( __( 'WordLift Food KG support requires WP Recipe Maker 8.1-8.3, %s found.', 'wordlift' ), WPRM_VERSION )
			);
		}

		return $check;
	}

}
