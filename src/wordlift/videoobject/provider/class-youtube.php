<?php
/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Videoobject\Provider;

class Youtube implements Provider {

	private $api_key;

	public function __construct( $api_key ) {
		$this->api_key = $api_key;
	}


	public function get_videos_data( $videos ) {

	}


}