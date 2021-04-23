<?php
/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Videoobject\Sitemap;

class Video_Sitemap {

	public function init() {
		if ( self::is_video_sitemap_enabled() ) {
			add_action( 'template_include', array( $this, 'print_video_sitemap' ), 1 );
		}
	}

	/**
	 * Hijack requests for potential sitemaps and XSL files.
	 */
	public function print_video_sitemap() {
		global $wp;
		$url = home_url( $wp->request );
		if ( strpos( $url, 'wl-video-sitemap.xml' ) !== false ) {
			header( "Content-type: text/xml" );
			echo $this->get_sitemap_xml();
			die();
		}
	}

	public static function is_video_sitemap_enabled() {
		return intval( get_option( '_wl_video_sitemap_generation', false ) ) === 1;
	}

	/**
	 * @return string
	 */
	private function get_sitemap_xml() {
		$sitemap_start_tag = <<<EOF
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">
EOF;
		$sitemap_body      = Xml_Generator::get_xml_for_all_posts_with_videos();

		$sitemap_end_tag = "</urlset>";

		$xml = $sitemap_start_tag . $sitemap_body . $sitemap_end_tag;

		return $xml;
	}

}