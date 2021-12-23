<?php
/**
 * This class initializes the Entity auto publish feature and all its dependencies.
 * This feature should be on by default.
 *
 * @since 3.33.9
 * @author Claudio Salatino <claudio@wordlift.io>
 *
 * @see https://github.com/insideout10/wordlift-plugin/issues/1517
 */
namespace Wordlift\Entity_Auto_Publish;

use Wordlift\Common\Loader\Default_Loader;
use Wordlift\Post\Post_Adapter;

class Loader extends Default_Loader {

	public function init_all_dependencies() {
		// Update post status in classic editor.
		add_action( 'wl_classic_editor_after_entities_save', function( $post, $old_status, $new_status ) {

			// When a post is published, then all the referenced entities must be published.
			if ( 'publish' !== $old_status && 'publish' === $new_status ) {
				foreach ( wl_core_get_related_entity_ids( $post->ID ) as $entity_id ) {
					wl_update_post_status( $entity_id, 'publish' );
				}
			}

		});
		// Update post status in block editor.
		add_action( 'wl_block_editor_after_entities_save', function( $post_status, $post_id ) {
			if ( $post_status !== 'publish' ) {
				wp_update_post( array(
					'ID'          => $post_id,
					'post_status' => $post_status
				) );
			}
		});
	}

	protected function get_feature_slug() {
		return 'entity-auto-publish';
	}

	protected function get_feature_default_value() {
		return true;
	}
}
