<?php

namespace Wordlift\External_Plugin_Hooks\Avada_Builder;

/**
 * Avada Builder Support.
 *
 * @since 3.39.2
 * @author Mahbub Hasan Imon <mahbub@wordlift.io>
 */
class Avada_Builder_Support {

	public function __construct() {
		add_filter( 'wl_post_excerpt_post_content', array( $this, 'post_excerpt_post_content' ), 10, 2 );
	}

	/**
	 * Filter the post content before sending it to WordLift API.
	 *
	 * @param $post_body string The post content sent from WordPress editor.
	 * @param $post_id int The post id.
	 *
	 * @return string The post content after removing Avada Builder's shortcodes.
	 */
	public function post_excerpt_post_content( $post_body, $post_id ) {

		if ( $this->is_avada_builder_active( false, $post_id ) ) { // Check if its Avada Builder's Content.
			return wp_strip_all_tags( do_shortcode( $post_body ), true );
		}
		return strip_shortcodes( $post_body );

	}

	/**
	 * Check if Avada Builder is active for the post.
	 *
	 * @param $is_enabled bool ( For No Analysis ) @see wl_no_editor_analysis_should_be_enabled_for_post_id filter.
	 * @param $post_id int The post id.
	 *
	 * @return bool True if Avada Builder is active for the post.
	 */
	public function is_avada_builder_active( $is_enabled, $post_id ) {

		if ( ! $post_id || ! class_exists( '\FusionBuilder' ) ) {
			return $is_enabled;
		}

		$get_fusion_status = get_post_meta( $post_id, 'fusion_builder_status', true );
		return ( $get_fusion_status && 'active' === $get_fusion_status );
	}
}
