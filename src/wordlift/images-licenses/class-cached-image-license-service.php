<?php
/**
 *
 */

namespace Wordlift\Images_Licenses;

use Wordlift\Api\Api_Service;
use Wordlift\Cache\Ttl_Cache;

class Cached_Image_License_Service extends Image_License_Service {

	const GET_NON_PUBLIC_DOMAIN_IMAGES = 'get_non_public_domain_images';

	/**
	 * @var Ttl_Cache
	 */
	private $cache_service;

	/**
	 * @var Image_License_Service
	 */
	private $image_license_service;

	/**
	 * Images_Licenses_Service constructor.
	 *
	 * @param Image_License_Service $image_license_service
	 * @param Ttl_Cache $cache_service
	 */
	public function __construct( $image_license_service, $cache_service ) {

		$this->image_license_service = $image_license_service;
		$this->cache_service         = $cache_service;

		$that = $this;
		add_action( 'wp_ajax_wl_cached_image_license_service__get_non_public_domain_images', function () use ( $that ) {
			wp_send_json_success( $that->get_non_public_domain_images() );
		} );

	}

	/**
	 * @return array
	 */
	public function get_non_public_domain_images() {

		// Return the cached data if available.
		$cache = $this->cache_service->get( self::GET_NON_PUBLIC_DOMAIN_IMAGES );
		if ( ! is_null( $cache ) ) {
			return $cache;
		}

		$data = $this->image_license_service->get_non_public_domain_images();

		// Store the cached data.
		$this->cache_service->put( self::GET_NON_PUBLIC_DOMAIN_IMAGES, $data );

		return $data;
	}

}
