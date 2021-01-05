<?php

namespace Wordlift\Images_Licenses;

/**
 * @package Wordlift\Images_Licenses
 */
class Image_License_Factory {

	public function create( $attachment_id, $data, $more_info_link, $posts_ids_as_featured_image = array(), $posts_ids_as_embed = array() ) {

		return array(
			'attachment_id'               => $attachment_id,
			'more_info_link'              => $more_info_link,
			'posts_ids_as_embed'          => $posts_ids_as_embed,
			'posts_ids_as_featured_image' => $posts_ids_as_featured_image,
			'filename'                    => $data['filename'],
			'author'                      => $data['author'],
			'license'                     => $data['license'],
			'license_family'              => $data['licenseFamily'],
			'url'                         => $data['url'],
		);
	}

}
