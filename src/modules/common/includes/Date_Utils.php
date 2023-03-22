<?php

namespace Wordlift\Modules\Common;

use DateTimeImmutable;
use DateTimeZone;

class Date_Utils {
	public static function now_utc() {
		return new DateTimeImmutable( 'now', new DateTimeZone( 'UTC' ) );
	}
}
