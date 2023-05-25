<?php
/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Videoobject\Sitemap;

use Wordlift\Cache\Ttl_Cache;

class Video_Sitemap {

	/**
	 * @var Ttl_Cache
	 */
	private $sitemap_cache;

	const XML_CACHE_KEY = 'video_sitemap';

	public function __construct( $sitemap_cache ) {
		$this->sitemap_cache = $sitemap_cache;
	}

	public function init() {
		if ( self::is_video_sitemap_enabled() ) {
			add_action( 'template_redirect', array( $this, 'print_video_sitemap' ), 1 );
		}
		add_action( 'wordlift_videoobject_video_storage_updated', array( $this, 'flush_cache' ) );
	}

	public function flush_cache() {
		$this->sitemap_cache->flush();
	}

	/**
	 * Print video sitemap.
	 */
	public function print_video_sitemap() {
		global $wp;

		$url = home_url( $wp->request );

		$pattern = '/wl-video-sitemap\.xml$/m';

		if ( preg_match( $pattern, $url ) !== 1 ) {
			return;
		}

		header( 'Content-type: text/xml' );
		// set 200 status code.
		status_header( 200 );

		$xml = $this->sitemap_cache->get( self::XML_CACHE_KEY );

		if ( ! $xml ) {
			$xml = $this->get_sitemap_xml();
			$this->sitemap_cache->put( self::XML_CACHE_KEY, $xml );
		}

		echo $xml; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- It's a `text/xml` output (see `Content-Type: text/xml` header above).
		die();
	}

	public static function is_video_sitemap_enabled() {
		return intval( get_option( '_wl_video_sitemap_generation', false ) ) === 1;
	}

	/**
	 * @return string
	 */
	private function get_sitemap_xml() {
		$sitemap_start_tag = '
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">
';
		$sitemap_body      = Xml_Generator::get_xml_for_all_posts_with_videos();

		$sitemap_end_tag = '</urlset>';

		$xml = $sitemap_start_tag . $sitemap_body . $sitemap_end_tag;

		return $xml;
	}

}
