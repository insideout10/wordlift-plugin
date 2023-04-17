<?php

namespace Wordlift\Modules\Gardening_Kg;

class Preconditions {

	public function pass() {
		// phpcs:ignore WordPress.NamingConventions.ValidHookName.UseUnderscores
		return apply_filters( 'wl_feature__enable__gardening-kg', false );
	}

}
