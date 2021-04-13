<?php
namespace Wordlift\Videoobject\Provider\Client;
/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 * This acts as interface for api clients
 */
interface Client {

	/**
	 *
	 * @param $video_urls array<string> Array of urls.
	 *
	 * @return array
	 */
	public function get_data( $video_urls );

}