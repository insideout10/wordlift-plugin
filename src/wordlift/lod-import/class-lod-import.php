<?php

namespace Wordlift\Lod_Import;

use Wordlift\Api\Default_Api_Service;
use Wordlift\Content\Wordpress\Wordpress_Content_Service;
use Wordlift_Entity_Type_Service;

class Lod_Import {

	public function __construct() {

	}

	public function register_hooks() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	public function admin_menu() {

		$callback = 'POST' === $_SERVER['REQUEST_METHOD'] ? 'handle' : 'render';

		add_submenu_page(
			'wl_admin_menu',
			__( 'LOD Import', 'wordlift' ),
			__( 'LOD Import', 'wordlift' ),
			'manage_options',
			'wl_lod_import',
			array( $this, $callback )
		);
	}

	public function render() {
		?>
        <div class="wrap">
            <h1><?php _e( 'LOD Import', 'wordlift' ); ?></h1>
            <p><?php _e( 'Helpful stuff here', 'wordlift' ); ?></p>
            <form method="post" novalidate="novalidate">
                <div class="form-field">
                    <label for="item-ids"><?php _e( 'Linked Data IDs', 'wordlift' ); ?></label>
                    <textarea name="item-ids" id="item-ids" rows="5" cols="40"></textarea>
                    <p><?php _e( 'The description is not prominent by default; however, some themes may show it.', 'wordlift' ); ?></p>
                </div>
				<?php submit_button( 'Import' ); ?>
            </form>
        </div>
		<?php

	}

	public function handle() {
		if ( ! $item_ids = filter_input( INPUT_POST, 'item-ids' ) ) {
			?>
            <p class="notice notice-error">Please type stgh</p>
			<?php
			return;
		}

		foreach ( preg_split( '(\r\n|\r|\n)', $item_ids ) as $item_id ) {
			$this->import_single( $item_id );
		}
	}

	private function import_single( $item_id ) {

		$content_service     = Wordpress_Content_Service::get_instance();
		$entity_type_service = Wordlift_Entity_Type_Service::get_instance();

		// Do not create/update an existing entity.
		if ( $content_service->get_by_entity_id_or_same_as( $item_id ) ) {
			return;
		}

		$post_id = $this->import_entity( $item_id, $entity_type_service );

		edit_post_link( $item_id, $post_id );

	}

	/**
	 * @param $item_id
	 * @param Wordlift_Entity_Type_Service $entity_type_service
	 *
	 * @return int|\WP_Error
	 */
	private function import_entity( $item_id, $entity_type_service ) {
		$target_path = '/id/' . preg_replace( '@^(https?)://@', '$1/', $item_id );
		$response    = Default_Api_Service::get_instance()->get( $target_path );
		$json        = json_decode( $response->get_body() );
		$post_id     = wp_insert_post( array(
			'post_title'   => $json->name,
			'post_content' => isset( $json->description ) ? $json->description : '',
			'post_status'  => 'draft',
			'post_type'    => 'entity',
		) );

		foreach ( $json->{'@type'} as $type ) {
			$entity_type_service->set( $post_id, "http://schema.org/$type", false );
		}

		add_post_meta( $post_id, 'entity_same_as', $item_id );

		if ( isset( $json->sameAs ) ) {
			foreach ( $json->sameAs as $same_as ) {
				add_post_meta( $post_id, 'entity_same_as', $same_as );
			}
		}

		return $post_id;
	}

}
