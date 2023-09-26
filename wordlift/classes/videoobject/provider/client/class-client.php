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
	 * Response body or false if the request cant be made or failed.
	 * @return string | false
	 */
	public function get_data( $video_urls );

	/**
	 * @return string
	 */
	public static function get_api_key();
	/**
	 * Returns the option where the api key is stored.
	 *
	 * @return string
	 */
	public static function get_api_key_option_name();

	/**
	 * The api base url.
	 *
	 * @return string
	 */
	public function get_api_url();

	/**
	 * Get video ids from URLs.
	 *
	 * @param $video_urls array<string>
	 * @return array<string>
	 */
	public function get_video_ids( $video_urls  );

}
