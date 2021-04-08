<?php
/**
 * @since 1.0.0
 * @author Naveen Muthusamy <naveen@wordlift.io>
 */

namespace Wordlift_Videoobject\Vimeo;

use Wordlift_Videoobject\Singleton;

class Jsonld_Service extends Singleton {
	/**
	 * @var Acf_Data_Service
	 */
	private $acf_data_service;

	protected function __construct() {
		parent::__construct();
		add_action( 'wl_entity_jsonld', array( $this, 'wl_post_jsonld' ), 10, 3 );
		$this->acf_data_service = Acf_Data_Service::get_instance();
	}

	/**
	 * @return Jsonld_Service
	 */
	public static function get_instance() {
		return parent::get_instance();
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

	public function wl_post_jsonld( $jsonld, $post_id, $references ) {
		if ( ! array_key_exists( '@type', $jsonld ) ||
		     ! array_key_exists( '@id', $jsonld ) ||
		     ( is_array( $jsonld['@type'] ) && ! in_array( 'LodgingBusiness', $jsonld['@type'] ) ) ||
		     ( is_string( $jsonld['@type'] ) && $jsonld['@type'] !== 'LodgingBusiness' )
		) {
			return $jsonld;
		}

		$video_jsonld = $this->get_jsonld( $post_id, $jsonld['@id'] );
		if ( count( $video_jsonld ) === 0 ) {
			return $jsonld;
		}
		// Before adding the video jsonld check if the key
		// is present and additional data might be present,
		// if not present just add the data and return early.
		if ( ! array_key_exists( 'subjectOf', $jsonld ) ) {
			$jsonld['subjectOf'] = $video_jsonld;

			return $jsonld;
		}

		// since key exists, we need to merge the data based on type.
		$previous_video_data = $jsonld['subjectOf'];
		$jsonld['subjectOf'] = $this->merge_video_data( $previous_video_data, $video_jsonld );

		return $jsonld;
	}

	/**
	 * @param $post_id int Post id.
	 *
	 * @param $entity_uri
	 *
	 * @return array
	 */
	public function get_jsonld( $post_id, $entity_uri  ) {
		$videos = $this->acf_data_service->get_videos( $post_id );
		$jsonld = array();
		foreach ( $videos as $video ) {
			/**
			 * @var $video Video_Data
			 */
			$jsonld[] = array(
				'@type'           => 'VideoObject',
				'name'            => $video->name,
				'description'     => $video->description,
				'contentUrl'      => $video->content_url,
				'embedUrl'        => $video->embed_url,
				'uploadDate'      => $video->upload_date,
				'thumbnailUrl'    => $this->extract_urls( $video->thumbnail_url ),
				'duration'        => $video->duration,
				'spatialCoverage' => array( '@id' => $entity_uri )

			);
		}

		return $jsonld;
	}

	/**
	 * @param $thumbnail_url array
	 *
	 * @return array|\string[][]
	 */
	private function extract_urls( $thumbnail_url ) {
		return array_map( function ( $item ) {
			return $item['url'];
		}, $thumbnail_url );
	}

	private function is_associative_array( $arr ) {
		if ( array() === $arr ) {
			return false;
		}

		return array_keys( $arr ) !== range( 0, count( $arr ) - 1 );
	}


}
