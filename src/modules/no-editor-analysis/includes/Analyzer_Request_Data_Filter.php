<?php

namespace Wordlift\Modules\No_Editor_Analysis;

class Analyzer_Request_Data_Filter {

	public function register_hooks() {
		add_filter( 'wl_analyzer__request__data', array( $this, 'request__data' ) );
	}

	/**
	 * This filter returns the original data when the incoming data:
	 *  - doesn't have a `post_id` property (which we need to know which post_id to capture content for)
	 *  - doesn't have a `content` property
	 *  - is not an empty string (after HTML stripped)
	 *
	 * @todo how do we know we're in the right context, that is an edit screen?
	 *
	 * @param $data
	 *
	 * @return string
	 */
	public function request__data( $data ) {
		try {
			$json = json_decode( $data, true );

			// Check prerequisites.
			if ( ! $this->should_load_content_from_webpage( $json ) ) {
				return $data;
			}

			$permalink = get_permalink( $json['post_id'] );
			$response  = wp_remote_get( $permalink, array(
				'sslverify' => false, // We don't want expired certificates to blog this.
				'timeout'   => 60
			) );
			// Safely return if an error occurred. @@todo we should actually return an error in UI
			if ( is_wp_error( $response ) ) {
				return $data;
			}

			$json['content'] = wp_remote_retrieve_body( $response );

			return wp_json_encode( $json, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );

		} catch ( \Exception $e ) {

		}

		return $data;
	}

	private function should_load_content_from_webpage( $json ) {
		// Try to decode the data into a json and try to find the content.
		if ( ! isset( $json['post_id'] ) ||
		     ! isset( $json['content'] ) ) {
			return false;
		}

		// Check whether the content has actual content
		$content = trim( wp_strip_all_tags( $json['content'] ) );
		if ( ! empty( $content ) ) {
			return false;
		}

		return true;
	}

}
