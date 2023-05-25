<?php

namespace Wordlift\External_Plugin_Hooks\Avada_Builder;

/**
 * Avada Builder Support.
 *
 * @since 3.40.0
 * @author Mahbub Hasan Imon <mahbub@wordlift.io>
 */
class Avada_Builder_Support {

	public function __construct() {
		add_filter( 'wl_post_excerpt_post_content', array( $this, 'post_excerpt_post_content' ), 10, 3 );
		add_filter(
			'wl_no_editor_analysis_should_be_enabled_for_post_id',
			array( $this, 'is_avada_builder_active_no_analysis' ),
			10,
			2
		);
	}

	/**
	 * Filter the post content before sending it to WordLift API.
	 *
	 * @param $post_body string The post content sent from WordPress editor. ( with strip shortcodes )
	 * @param $post_id int The post id.
	 * @param $post_content string The post content.
	 *
	 * @return string The post content after removing Avada Builder's shortcodes.
	 */
	public function post_excerpt_post_content( $post_body, $post_id, $post_content ) {

		if ( $this->is_avada_builder_active( $post_id ) ) { // Check if its Avada Builder's Content.
			return wp_strip_all_tags( do_shortcode( $post_content ), true );
		}
		return $post_body;

	}

	/**
	 * Check if Avada Builder is active for the post.
	 *
	 * @param $post_id int The post id.
	 *
	 * @return bool True if Avada Builder is active for the post.
	 */
	public function is_avada_builder_active( $post_id ) {

		if ( ! $post_id || ! class_exists( '\FusionBuilder' ) ) {
			return false;
		}

		return 'active' === get_post_meta( $post_id, 'fusion_builder_status', true );
	}

	/**
	 * Check if Avada Builder is active for the post ( no analysis ).
	 *
	 * @param $is_enabled bool Is Enabled.
	 * @param $post_id int The post id.
	 *
	 * @return bool True if Avada Builder is active for the post.
	 */
	public function is_avada_builder_active_no_analysis( $is_enabled, $post_id ) {
		return $is_enabled ? $is_enabled : $this->is_avada_builder_active( $post_id );
	}
}
