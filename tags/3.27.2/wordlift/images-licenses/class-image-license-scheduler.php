<?php
/**
 * This file defines the scheduler that will get the domains linked via http using the {@link \Windowsreport_Companion\Http_Links\Http_Links_Service}
 * and it'll store the data using the {@link \Windowsreport_Companion\Http_Links\Http_Links_Domains_Data}.
 *
 * @author David Riccitelli <david@wordlift.io>
 * @since 2.22.0
 * @package Windowsreport_Companion\Http_Links
 */

namespace Wordlift\Images_Licenses;

use Wordlift\Cache\Ttl_Cache;

class Image_License_Scheduler {

	const ACTION_NAME = 'wl_image_license_scheduler__run';

	/**
	 * @var Image_License_Service
	 */
	private $image_license_service;
	/**
	 * @var Ttl_Cache
	 */
	private $cache_service;

	/**
	 * @param Image_License_Service $image_license_service
	 * @param Ttl_Cache $cache_service
	 */
	public function __construct( $image_license_service, $cache_service ) {

		$this->image_license_service = $image_license_service;
		$this->cache_service         = $cache_service;

		// Do not bother to configure scheduled tasks while running on the front-end.
		add_action( 'wp_ajax_' . self::ACTION_NAME, array( $this, 'run' ) );
		add_action( self::ACTION_NAME, array( $this, 'run' ) );

		if ( is_admin() && ! wp_next_scheduled( self::ACTION_NAME ) ) {
			wp_schedule_event( time(), 'weekly', self::ACTION_NAME );
		}

	}

	public function run() {

		// Get and save the domains.
		$data = $this->image_license_service->get_non_public_domain_images();

		// Update the cache.
		$this->cache_service->put( Cached_Image_License_Service::GET_NON_PUBLIC_DOMAIN_IMAGES, $data );

		if ( wp_doing_ajax() &&
		     ( self::ACTION_NAME === filter_input( INPUT_GET, 'action' )
		       || self::ACTION_NAME === filter_input( INPUT_POST, 'action' ) ) ) {
			wp_send_json_success( $data );
		}

	}

}
