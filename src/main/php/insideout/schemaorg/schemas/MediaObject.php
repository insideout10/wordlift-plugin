<?php

/**
 * An image, video, or audio object embedded in a web page. Note that a creative work may have many media objects associated with it on the same web page. For example, a page about a single song (MusicRecording) may have a music video (VideoObject), and a high and low bandwidth audio stream (2 AudioObject's).
 * @schema http://schema.org/MediaObject
 */
class MediaObject extends CreativeWork implements ISchema {
	
	/**
	 * Get the friendly name for this schema.
	 * @return string The friendly name for this schema.
	 */
	public static function getFriendlyName() {
		return 'Media Object';
	}
	
	/**
	 * @description Actual bytes of the media object, for example the image file or video file.
	 * @type URL
	 */
	public $contentURL;

	/**
	 * @description File size in (mega/kilo) bytes.
	 * @type Text
	 */
	public $contentSize;
	
	/**
	 * @description mp3, mpeg4, etc.
	 * @type Text
	 */
	public $encodingFormat;
	
	/**
	 * @description The caption for this object.
	 * @type Text
	 */
	public $caption;
	
}

?>