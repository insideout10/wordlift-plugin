<?php

/**
 * This class represents a blog posting.
 * @schema http://schema.org/BlogPosting
 */
class BlogPosting extends CreativeWork implements ISchema {

	/**
	 * Get the friendly name for this schema.
	 * @return string The friendly name for this schema.
	 */
	public static function getFriendlyName() {
		return 'Blog Posting';
	}
	
}

?>