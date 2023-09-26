<?php

namespace Wordlift\Lod_Import;

use Wordlift\Content\Content_Id;
use Wordlift\Content\Wordpress\Wordpress_Content_Service;
use Wordlift\Entity\Remote_Entity\Url_To_Remote_Entity_Converter;
use Wordlift\Entity\Remote_Entity_Importer\Remote_Entity_Importer_Factory;
use Wordlift\Object_Type_Enum;

class Lod_Import {

	public function __construct() {

	}

	public function register_hooks() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	public function admin_menu() {

		$callback = isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === sanitize_text_field( wp_unslash( $_SERVER['REQUEST_METHOD'] ) ) ? 'handle' : 'render';

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
			<h1><?php esc_html_e( 'LOD Import', 'wordlift' ); ?></h1>
			<p><?php esc_html_e( 'Helpful stuff here', 'wordlift' ); ?></p>
			<form method="post" novalidate="novalidate">
				<div class="form-field">
					<label for="item-ids"><?php esc_html_e( 'Linked Data IDs', 'wordlift' ); ?></label>
					<textarea name="item-ids" id="item-ids" rows="5" cols="40"></textarea>
					<p><?php esc_html_e( 'The description is not prominent by default; however, some themes may show it.', 'wordlift' ); ?></p>
				</div>
				<?php submit_button( 'Import' ); ?>
			</form>
		</div>
		<?php

	}

	public function handle() {
		$item_ids = filter_input( INPUT_POST, 'item-ids' );
		if ( ! $item_ids ) {
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

		$content_service = Wordpress_Content_Service::get_instance();

		// Do not create/update an existing entity.
		if ( $content_service->get_by_entity_id_or_same_as( $item_id ) ) {
			return;
		}

		$remote_entity = Url_To_Remote_Entity_Converter::convert( $item_id );
		$importer      = Remote_Entity_Importer_Factory::from_entity( $remote_entity );
		$content_id    = $importer->import();
		if ( $content_id instanceof Content_Id && $content_id->get_type() === Object_Type_Enum::POST ) {
			edit_post_link( null, null, null, $content_id->get_id() );
		}

	}

}
