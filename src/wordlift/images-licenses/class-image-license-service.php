<?php
/**
 *
 */

namespace Wordlift\Images_Licenses;

use Wordlift\Api\Api_Service;

class Image_License_Service {
	/**
	 * @var Api_Service
	 */
	private $api_service;

	/**
	 * Images_Licenses_Service constructor.
	 *
	 * @param Api_Service $api_service
	 */
	public function __construct( $api_service ) {

		$this->api_service = $api_service;

	}

	/**
	 * @return Image_License[]
	 */
	public function get_non_public_domain_images() {

		$response      = $this->api_service->get( '/images/GetNonPublicDomainImages' );
		$response_body = $response->get_body();

		if ( empty( $response_body ) ) {
			return array();
		}

		$json_data = json_decode( $response_body, true );

		return $this->post_process_response_body( $json_data );
	}

	/**
	 * @param $json_data
	 *
	 * @return Image_License[]
	 */
	private function post_process_response_body( $json_data ) {
		global $wpdb;

		$return_data = array();

		foreach ( $json_data as $raw_image ) {
			$more_info_link = sprintf( 'https://commons.wikimedia.org/wiki/File:%s', rawurlencode( $raw_image['filename'] ) );
			$image_license  = new Image_License( $raw_image, $more_info_link );

			$attachments = $wpdb->get_results( $wpdb->prepare(
				"SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value LIKE %s",
				'_wp_attached_file',
				'%' . $wpdb->esc_like( $image_license->get_filename() )
			) );

			// Get tha attachments' post IDs.
			$attachments_ids = array_column( $attachments, 'post_id' );

			// Get the posts referencing the attachments as featured image_license.
			$image_license->set_posts_ids_as_featured_image(
				$this->get_posts_ids_as_featured_image( $attachments_ids )
			);

			$filenames = array_column( $attachments, 'meta_value' );

			$image_license->set_posts_ids_as_embed(
				$this->get_posts_ids_as_embed( $filenames )
			);

			// https://commons.wikimedia.org/wiki/File:Tim_Berners-Lee-Knight.jpg

			/**
			 * <figure>
			 * <img src="/media/examples/elephant-660-480.jpg"
			 * alt="Elephant at sunset">
			 * <figcaption>An elephant at sunset</figcaption>
			 * </figure>
			 */

			$return_data[] = $image_license;
		};

		return $return_data;
	}

	/**
	 * @param array $ids
	 *
	 * @return array
	 */
	private function get_posts_ids_as_featured_image( $ids ) {

		// Bail out if there are no attachments post IDs.
		if ( empty( $ids ) ) {
			return array();
		}

		global $wpdb;

		return $wpdb->get_col( $wpdb->prepare(
			"
			SELECT post_id FROM {$wpdb->postmeta}
			WHERE meta_key = %s
			 AND meta_value IN ( " . implode( ',', $ids ) . " )
			",
			'_thumbnail_id'
		) );
	}

	/**
	 * @param array $filenames
	 *
	 * @return array
	 */
	private function get_posts_ids_as_embed( $filenames ) {

		if ( empty( $filenames ) ) {
			return array();
		}

		global $wpdb;

		$post_content_like = "post_content LIKE '%/"
		                     . implode( "%' OR post_content LIKE '%/", array_map( 'esc_sql', $filenames ) )
		                     . "%'";

		return $wpdb->get_col(
			"
			SELECT ID FROM {$wpdb->posts}
			WHERE $post_content_like
			"
		);
	}

}
