<?php
/**
 * Factory class for constructing storage.
 *
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Videoobject\Data\Video_Storage;

class Video_Storage_Factory {

	public static function get_storage() {
		return new Meta_Storage();
	}

}
