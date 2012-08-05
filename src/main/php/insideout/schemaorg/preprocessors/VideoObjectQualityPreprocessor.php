<?php

class SchemaOrg_VideoObjectQualityPreprocessor implements SchemaOrg_IPreprocessor {

	public $logger;

	const PROTOCOL = 1;
	const SERVER = 2;
	const ID = 3;
	const FORMAT = 4;
	const FRAME_SIZE = 5;
	const VIDEO_CODEC = 6;
	const VIDEO_BITRATE = 7;
	const AUDIO_CODEC = 8;
	const AUDIO_BITRATE = 9;
	const CONTENT_ID = 10;
	const EXTENSION = 11;

	public function supportsType( $type ) {
		return ( "VideoObject" === $type );
	}

	public function process( &$properties ) {
		/*
		 * http://eml.enel.com/EML/907/mp4_720x576_h264_2000Kb_aac_256Kb_5907.mp4
		 * http://eml.enel.com/EML/906/mp4_480x360_h264_500Kb_aac_128Kb_5906.mp4
		 * http://eml.enel.com/EML/905/mp4_320x240_h264_100Kb_aac_96Kb_5905.mp4
		 */

		if (false === array_key_exists( "contentURL", $properties ))
			return;

		$matches = array();
		$found = preg_match_all( "/(\w+):\/\/([\w\.]+)\/EML\/(\d+)\/(\w+)_(\d+x\d+)_(\w+)_(\d+Kb)_(\w+)_(\d+Kb)_(\d+).(\w+)/",  $properties["contentURL"], $matches);

		if (0 === $found)
			return;

		$bitrate = intval( $matches[ self::VIDEO_BITRATE ][0] );
		if ( 100 >= $bitrate)
			$videoQuality = "SQ";
		else if ( 500 >= $bitrate)
			$videoQuality = "HQ";
		else
			$videoQuality = "HD";

		$properties["videoQuality"] = $videoQuality;
		$properties["videoFrameSize"] = $matches[ self::FRAME_SIZE ][0];
	}
	
}

?>