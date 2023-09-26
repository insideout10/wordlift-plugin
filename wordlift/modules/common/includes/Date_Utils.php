<?php

namespace Wordlift\Modules\Common;

use DateTimeImmutable;
use DateTimeZone;

class Date_Utils {
	public static function now_utc() {
		return new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );
	}

	public static function to_iso_string( $value ) {
		return is_a( $value, 'DateTimeInterface' )
			? date_format( $value, 'Y-m-d\TH:i:s\Z' ) : null;
	}
}
