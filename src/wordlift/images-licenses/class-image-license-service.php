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
	 * @var Image_License_Factory
	 */
	private $image_license_factory;

	/**
	 * Images_Licenses_Service constructor.
	 *
	 * @param Api_Service $api_service
	 * @param Image_License_Factory $image_license_factory
	 */
	public function __construct( $api_service, $image_license_factory ) {

		$this->api_service           = $api_service;
		$this->image_license_factory = $image_license_factory;

	}

	/**
	 * @return Image_License_Factory[]
	 */
	public function get_non_public_domain_images() {

		$response      = $this->api_service->get( '/images/GetNonPublicDomainImages', array(), null, 300 );
		$response_body = $response->get_body();

		if ( empty( $response_body ) ) {
			return array();
		}

		$json_data = json_decode( $response_body, true );

		return apply_filters(
			'wl_post_get_non_public_domain_images',
			$this->post_process_response_body( $json_data )
		);
	}

	/**
	 * @param $json_data
	 *
	 * @return Image_License_Factory[]
	 */
	private function post_process_response_body( $json_data ) {
		global $wpdb;

		$return_data = array();

		foreach ( $json_data as $raw_image ) {
			$filename       = $raw_image['filename'];
			$more_info_link = sprintf( 'https://commons.wikimedia.org/wiki/File:%s', $filename );

			$attachments = $wpdb->get_results( $wpdb->prepare(
				"SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value LIKE %s",
				'_wp_attached_file',
				'wl/%' . $wpdb->esc_like( $filename )
			) );

			// Get tha attachments' post IDs.

			foreach ( $attachments as $attachment ) {
				$post_id       = (int) $attachment->post_id;
				$image_license = $this->image_license_factory->create( $post_id, $raw_image, $more_info_link );

				// Get the posts referencing the attachments as featured image_license.
				$image_license['posts_ids_as_featured_image'] = $this->get_posts_ids_as_featured_image( $post_id );

				$filename = $attachment->meta_value;

				$image_license['posts_ids_as_embed'] = $this->get_posts_ids_as_embed( $filename );

				/**
				 * <figure>
				 * <img src="/media/examples/elephant-660-480.jpg"
				 * alt="Elephant at sunset">
				 * <figcaption>An elephant at sunset</figcaption>
				 * </figure>
				 */

				$return_data[] = $image_license;
			}

		};

		return $return_data;
	}

	/**
	 * @param int $id
	 *
	 * @return array
	 */
	private function get_posts_ids_as_featured_image( $id ) {

		// Bail out if there are no attachments post IDs.
		if ( empty( $id ) ) {
			return array();
		}

		global $wpdb;

		return array_map( 'intval', $wpdb->get_col( $wpdb->prepare(
			"
			SELECT post_id FROM {$wpdb->postmeta}
			WHERE meta_key = %s
			 AND meta_value = %d
			",
			'_thumbnail_id',
			$id
		) ) );
	}

	/**
	 * @param string $filename
	 *
	 * @return array
	 */
	private function get_posts_ids_as_embed( $filename ) {

		if ( empty( $filename ) ) {
			return array();
		}

		global $wpdb;

		return array_map( 'intval', $wpdb->get_col( $wpdb->prepare(
			"
			SELECT ID FROM {$wpdb->posts}
			WHERE post_content LIKE %s
			 AND post_type = %s
			 AND post_status = %s
			",
			'%/' . $wpdb->esc_like( $filename ) . '"%',
			'post',
			'publish'
		) ) );
	}

}
