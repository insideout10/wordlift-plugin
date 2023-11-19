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
				&& version_compare( WPRM_VERSION, '10.0.0', '<' );

		if ( ! $check ) {
			$this->notices->queue(
				'warning',
				/* translators: 1: minimum supported WPRM version, 2: maximum supported WPRM version, 3: Detected WP Recipe Maker version. */
				sprintf( __( 'WordLift Food KG support requires WP Recipe Maker %1$s-%2$s, %3$s found.', 'wordlift' ), '8.1', '9', WPRM_VERSION )
			);
		}

		return $check;
	}

}
