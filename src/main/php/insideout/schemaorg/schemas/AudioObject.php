<?php

/**
 * An audio file.
 * @schema http://schema.org/AudioObject
 */
class AudioObject extends MediaObject implements ISchema {
	
	/**
	 * Get the friendly name for this schema.
	 * @return string The friendly name for this schema.
	 */
	public static function getFriendlyName() {
		return 'Audio Object';
	}
	
}

?>