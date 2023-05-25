<?php

namespace Wordlift\Vocabulary\Data\Term_Count;

/**
 * This is the factory class for creating different term count objects.
 *
 * @since 3.30.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */
class Term_Count_Factory {

	const CACHED_TERM_COUNT = 'cached_term_count';

	public static function get_instance( $type ) {
		if ( self::CACHED_TERM_COUNT === $type ) {
			return new Cached_Term_Count( new Default_Term_Count() );
		}

		return null;
	}

}
