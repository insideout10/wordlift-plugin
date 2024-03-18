<?php
/**
 * @since 3.31.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift\Videoobject\Jsonld;

use Wordlift\Jsonld\Jsonld_Article_Wrapper;
use Wordlift\Videoobject\Data\Video\Video;
use Wordlift\Videoobject\Data\Video_Storage\Storage;

class Jsonld {
	/**
	 * @var Storage
	 */
	private $video_storage;

	/**
	 * Jsonld constructor.
	 *
	 * @param $video_storage Storage
	 */
	public function __construct( $video_storage ) {
		add_action( 'wl_post_jsonld', array( $this, 'wl_post_jsonld' ), 10, 2 );
		add_filter( 'wl_after_get_jsonld', array( $this, 'wl_after_get_jsonld' ), 10, 2 );
		$this->video_storage = $video_storage;
	}

	public function wl_after_get_jsonld( $jsonld, $post_id ) {
		if ( 0 === count( $jsonld ) ) {
			return $jsonld;
		}
		$current_item = $jsonld[0];

		if ( ! is_array( $current_item ) || ! array_key_exists( '@type', $current_item ) ) {
			// Cant determine type return early.
			return $jsonld;
		}

		$type = $current_item['@type'];
		if ( is_string( $type ) ) {
			$type = array( $type );
		}
		// If its a article or descendant of article, then dont add the
		// videoobject in this hook, they will be already added to video property.
		if ( array_intersect( Jsonld_Article_Wrapper::$article_types, $type ) ) {
			return $jsonld;
		}

		$videos_jsonld = $this->get_videos_jsonld( $post_id );
		if ( 0 === count( $videos_jsonld ) ) {
			return $jsonld;
		}

		// check if we have @id in jsonld for first item.
		$id = array_key_exists( '@id', $current_item ) ? $current_item['@id'] : '';

		foreach ( $videos_jsonld as &$video_jsonld ) {
			if ( ! $id ) {
				continue;
			}
			if ( ! array_key_exists( 'mentions', $video_jsonld ) ) {
				$video_jsonld['mentions'] = array( '@id' => $id );
			} else {
				$video_jsonld['mentions'] = array_merge( $video_jsonld['mentions'], array( '@id' => $id ) );
			}
		}

		return array_merge( $jsonld, $videos_jsonld );
	}

	/**
	 * @param $existing_video_data string | array associative or sequential array.
	 * @param $new_video_data array Sequential array.
	 *
	 * @return array
	 */
	private function merge_video_data( $existing_video_data, $new_video_data ) {
		if ( ! is_array( $existing_video_data ) ) {
			$new_video_data[] = $existing_video_data;

			return $new_video_data;
		}

		if ( $this->is_associative_array( $existing_video_data ) ) {
			$new_video_data[] = $existing_video_data;

			return $new_video_data;
		}

		return array_merge( $existing_video_data, $new_video_data );
	}

	public function wl_post_jsonld( $jsonld, $post_id ) {

		$video_jsonld = $this->get_videos_jsonld( $post_id );
		if ( count( $video_jsonld ) === 0 ) {
			return $jsonld;
		}
		// Before adding the video jsonld check if the key
		// is present and additional data might be present,
		// if not present just add the data and return early.
		if ( ! array_key_exists( 'video', $jsonld ) ) {
			$jsonld['video'] = $video_jsonld;

			return $jsonld;
		}

		// since key exists, we need to merge the data based on type.
		$previous_video_data = $jsonld['video'];
		$jsonld['video']     = $this->merge_video_data( $previous_video_data, $video_jsonld );

		return $jsonld;
	}

	/**
	 * @param $post_id int Post id.
	 *
	 * @return array
	 */
	public function get_videos_jsonld( $post_id ) {

		$videos = $this->video_storage->get_all_videos( $post_id );

		$jsonld = array();

		foreach ( $videos as $video ) {
			/**
			 * @var $video Video
			 */
			$description = $video->description;
			if ( ! $video->description ) {
				// If description is empty then use the video title as description
				$description = $video->name;
			}
			$single_jsonld = array(
				'@context'     => 'http://schema.org',
				'@type'        => 'VideoObject',
				'name'         => $video->name,
				'description'  => $description,
				'contentUrl'   => $video->content_url,
				'uploadDate'   => $video->upload_date,
				'thumbnailUrl' => $video->thumbnail_urls,
				'duration'     => $video->duration,
			);

			if ( $video->embed_url ) {
				$single_jsonld['embedUrl'] = $video->embed_url;
			}

			if ( $video->views ) {
				$single_jsonld['interactionStatistic'] = array(
					'@type'                => 'InteractionCounter',
					'interactionType'      => array(
						'@type' => 'http://schema.org/WatchAction',
					),
					'userInteractionCount' => $video->views,
				);
			}

			if ( $video->is_live_video ) {
				$single_jsonld['publication'] = array(
					'@type'           => 'BroadcastEvent',
					'isLiveBroadcast' => true,
					'startDate'       => $video->live_video_start_date,
					'endDate'         => $video->live_video_end_date,
				);
			}

			$jsonld[] = $single_jsonld;
		}

		return $jsonld;
	}

	private function is_associative_array( $arr ) {
		if ( array() === $arr ) {
			return false;
		}

		return array_keys( $arr ) !== range( 0, count( $arr ) - 1 );
	}

}
