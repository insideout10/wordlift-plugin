<?php
namespace Wordlift\Videoobject\Provider;
/**
 * @since 3.30.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This acts as abstract class for Providers we get data from using API.
 */

abstract class Api_Provider implements Provider {

	protected function get_api_key() {
		return get_option( $this->get_option_api_key_name() );
	}


	public function get_videos_data( $videos ) {
		// TODO: Implement get_videos_data() method.
	}


	abstract function get_option_api_key_name();
}