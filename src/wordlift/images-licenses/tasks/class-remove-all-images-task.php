<?php

namespace Wordlift\Images_Licenses\Tasks;

use Wordlift\Images_Licenses\Image_License_Service;
use Wordlift\Tasks\Task;

class Remove_All_Images_Task implements Task {

	const MENU_SLUG = 'wl_remove_all_images_task';

	/**
	 * @var Image_License_Service
	 */
	private $image_license_service;

	/**
	 * Remove_All_Images_Task constructor.
	 *
	 * @param Image_License_Service $image_license_service
	 */
	public function __construct( $image_license_service ) {

		$this->image_license_service = $image_license_service;


		add_action( 'wp_ajax_' . $this->get_id() . '__single', array( $this, 'ajax', ) );

	}

	public function ajax() {

		check_ajax_referer( $this->get_id() );

		$this->process_item( $_POST );

		wp_send_json_success();

	}

	/**
	 * @inheritDoc
	 */
	function get_id() {

		return self::MENU_SLUG;
	}

	function get_label() {

		return __( 'Remove all images', 'wordlift' );
	}

	/**
	 * @inheritDoc
	 */
	function list_items( $limit = 10, $offset = 0 ) {

		$data = $this->image_license_service->get_non_public_domain_images();

		return array_slice( $data, $offset, $limit );
	}

	/**
	 * @inheritDoc
	 */
	function count_items() {

		$data = $this->image_license_service->get_non_public_domain_images();

		return count( $data );
	}

	/**
	 * @inheritDoc
	 */
	function process_item( $item ) {

		// Avoid deleting images that have been marked as fixed.
		$fixed = get_post_meta( $item['attachment_id'], '_wl_image_license_fixed', true );
		if ( ! empty( $fixed ) ) {
			return;
		}

		if ( ! isset( $item['posts_ids_as_embed'] ) ) {
			foreach ( $item['posts_ids_as_embed'] as $post_id ) {

				$filename       = $item['filename'];
				$filename_quote = preg_quote( $filename );
				$post           = get_post( $post_id );
				$search         = array(
					'@<a[^>]*href="[^"]+wl/[^"]+' . $filename_quote . '"[^>]*>(.+?)<\/a>@',
					'@<img[^>]*src="[^"]+wl/[^"]+' . $filename_quote . '"[^>]*>@',
				);
				$replace        = array( '$1', '', );
				$post_content   = preg_replace( $search, $replace, $post->post_content );

				wp_update_post( array(
					'ID'           => $post_id,
					'post_content' => $post_content,
				) );

			}

			wp_delete_attachment( $item['attachment_id'], true );
		}

	}

}
