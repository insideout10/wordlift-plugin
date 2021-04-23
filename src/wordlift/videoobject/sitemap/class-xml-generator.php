<?php
/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Videoobject\Sitemap;

use Wordlift\Videoobject\Data\Video\Video;
use Wordlift\Videoobject\Data\Video_Storage\Meta_Storage;
use Wordlift\Videoobject\Data\Video_Storage\Video_Storage_Factory;

class Xml_Generator {

	private static function iso8601_to_seconds( $iso8601_interval_string ) {
		try {
			$interval = new \DateInterval( $iso8601_interval_string );
		} catch ( \Exception $e ) {
			return 0;
		}

		$days_to_seconds = $interval->d * 60 * 60 * 24;
		$hours_to_seconds   = $interval->h * 60 * 60;
		$minutes_to_seconds = $interval->i * 60;
		$seconds            = $interval->s;

		return $days_to_seconds + $hours_to_seconds + $minutes_to_seconds + $seconds;

	}

	public static function get_xml_for_all_posts_with_videos() {

		$posts = get_posts( array(
			'fields'      => 'ids',
			'numberposts' => - 1,
			'meta_query'  => array(
				array(
					'key'     => Meta_Storage::META_KEY,
					'compare' => 'EXISTS'
				)
			)
		) );

		$all_posts_xml = "";

		if ( ! $posts ) {
			return $all_posts_xml;
		}

		foreach ( $posts as $post_id ) {
			$all_posts_xml .= self::get_xml_for_single_post( $post_id );
		}

		return $all_posts_xml;

	}


	/**
	 * @param $post_id
	 *
	 * @return string XML string for single post.
	 */
	public static function get_xml_for_single_post( $post_id ) {
		$videos = Video_Storage_Factory::get_storage()->get_all_videos( $post_id );
		if ( ! $videos ) {
			return "";
		}
		$single_post_xml = "";
		foreach ( $videos as $video ) {
			$single_post_xml .= self::get_xml_for_single_video( $video, $post_id );
		}

		return $single_post_xml;

	}


	/**
	 * @param $video Video
	 * @param $post_id int
	 *
	 * @return string
	 */
	public static function get_xml_for_single_video( $video, $post_id ) {

		$permalink           = get_permalink( $post_id );
		$title               = esc_xml( $video->name );
		$description         = esc_xml( $video->description );
		$thumbnail_url       = $video->thumbnail_urls[0];
		$content_url         = $video->content_url;
		$embed_url           = $video->embed_url;
		$duration_in_seconds = self::iso8601_to_seconds( $video->duration );
		$is_live_video       = $video->is_live_video ? 'yes' : 'no';
		$view_count          = $video->views;

		return <<<EOF
   <url>
     <loc>${permalink}</loc>
     <video:video>
       <video:thumbnail_loc>${thumbnail_url}</video:thumbnail_loc>
       <video:title>${title}</video:title>
       <video:description>${description}</video:description>
       <video:content_loc>${content_url}</video:content_loc>
       <video:player_loc>${embed_url}</video:player_loc>
       <video:duration>${duration_in_seconds}</video:duration>
       <video:view_count>${view_count}</video:view_count>
       <video:live>${is_live_video}</video:live>
     </video:video>
   </url>
EOF;

	}

}