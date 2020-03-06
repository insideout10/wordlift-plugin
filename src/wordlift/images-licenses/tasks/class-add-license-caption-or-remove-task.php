<?php

namespace Wordlift\Images_Licenses\Tasks;

use Wordlift\Images_Licenses\Caption_Builder;
use Wordlift\Images_Licenses\Image_License_Service;

class Add_License_Caption_Or_Remove_Task extends Remove_All_Images_Task {

	const MENU_SLUG = 'wl_add_license_caption_or_remove';

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
		parent::__construct( $image_license_service );

		$this->image_license_service = $image_license_service;

		add_action( 'wp_ajax_' . $this->get_id(), array( $this, 'ajax', ) );

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

		return __( 'Add license caption to images and remove those with unknown license', 'wordlift' );
	}

	/**
	 * @inheritDoc
	 */
	function process_item( $item ) {

		$is_unknown_license = '#N/A' === $item['license'];

		// If the license is unknown, delete the attachment.
		if ( $is_unknown_license ) {
			parent::process_item( $item );

			return;
		}

		$caption_builder = new Caption_Builder( $item );
		$caption         = $caption_builder->build();

		wp_update_post( array(
			'ID'           => $item['attachment_id'],
			'post_excerpt' => $caption
		) );

		// Avoid running the regex on post content more than once.
		$fixed = get_post_meta( $item['attachment_id'], '_wl_image_license_fixed', true );
		if ( ! empty( $fixed ) ) {
			return;
		}

		foreach ( $item['posts_ids_as_embed'] as $post_id ) {

			$figure = sprintf(
				'
				<figure>
					$0
					<figcaption>%s</figcaption>
				</figure>
				', $caption );

			/**
			 * <figure>
			 * <img src="/media/examples/elephant-660-480.jpg"
			 * alt="Elephant at sunset">
			 * <figcaption>An elephant at sunset</figcaption>
			 * </figure>
			 */

			$filename       = $item['filename'];
			$filename_quote = preg_quote( $filename );
			$post           = get_post( $post_id );
			$pattern        = '@<img[^>]*src="[^"]+wl/[^"]+' . $filename_quote . '"[^>]*>@';
			$post_content   = preg_replace( $pattern, $figure, $post->post_content );

			wp_update_post( array(
				'ID'           => $post_id,
				'post_content' => $post_content,
			) );

		}

		// Set the attachment as fixed.
		update_post_meta( $item['attachment_id'], '_wl_image_license_fixed', time() );

	}

}
