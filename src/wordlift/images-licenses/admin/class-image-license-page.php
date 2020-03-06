<?php

namespace Wordlift\Images_Licenses\Admin;

use Wordlift\Images_Licenses\Caption_Builder;
use Wordlift\Images_Licenses\Image_License_Service;
use Wordlift\Images_Licenses\Tasks\Add_License_Caption_Or_Remove_Task;
use Wordlift\Images_Licenses\Tasks\Remove_All_Images_Task;
use Wordlift\Wordpress\Submenu_Page_Base;

class Image_License_Page extends Submenu_Page_Base {

	/**
	 * @var Image_License_Service
	 */
	private $image_license_service;
	/**
	 * @var string
	 */
	private $version;

	/**
	 * Image_License_Page constructor.
	 *
	 * @param Image_License_Service $image_license_service
	 * @param string $version
	 */
	public function __construct( $image_license_service, $version ) {
		parent::__construct( 'wl_image_license_page', __( 'License Compliance', 'wordlift' ), 'manage_options', 'wl_admin_menu' );

		$this->image_license_service = $image_license_service;
		$this->version               = $version;

	}

	public function render() {
		?>
        <h1><?php esc_html_e( 'License Compliance', 'wordlift' ); ?></h1>

        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Delectus laudantium nam nihil provident sequi?
            Alias aliquam, animi debitis distinctio dolores laboriosam modi optio possimus qui, quis suscipit, unde
            veritatis voluptates?</p>

        <div class="tablenav top">
            <a class="button"
               href="<?php echo admin_url( 'admin.php?page=wl_images_licenses__reload_data' ); ?>"><?php esc_html_e( 'Reload data', 'wordlift' ); ?></a>
            <a class="button"
               href="<?php echo admin_url( 'admin.php?page=wl_images_licenses__remove_all_images' ); ?>"><?php esc_html_e( 'Remove all images', 'wordlift' ); ?></a>
            <a class="button"
               href="<?php echo admin_url( 'admin.php?page=wl_images_licenses__add_license_caption_or_remove' ); ?>"><?php esc_html_e( 'Add license caption to images and remove those with unknown license', 'wordlift' ); ?></a>
        </div>

        <h2 class="screen-reader-text"><?php esc_html_e( 'Images', 'wordlift' ); ?></h2>

        <table class="wp-list-table widefat fixed striped">
            <thead>
            <tr>
                <th><?php esc_html_e( 'Thumbnail', 'wordlift' ); ?></th>
                <th><?php esc_html_e( 'Filename', 'wordlift' ); ?></th>
                <th><?php esc_html_e( 'License', 'wordlift' ); ?></th>
                <th><?php esc_html_e( 'Author', 'wordlift' ); ?></th>
                <th><?php esc_html_e( 'Proposed Caption', 'wordlift' ); ?></th>
                <th><?php esc_html_e( 'More Info', 'wordlift' ); ?></th>
                <th><?php esc_html_e( 'Posts', 'wordlift' ); ?></th>
                <th><?php esc_html_e( 'Actions', 'wordlift' ); ?></th>
            </tr>
            </thead>
			<?php
			$images = $this->image_license_service->get_non_public_domain_images();

			for ( $i = 0; $i < count( $images ); $i ++ ) {
				$this->render_image( $images[ $i ], $i );
			}
			?>
        </table>
		<?php
	}

	/**
	 * @param array $image
	 */
	private function render_image( $image, $idx ) {

		$attachment_id = $image['attachment_id'];

		// Skip if the post doesn't exist anymore or has been fixed.
		if ( ! $this->exists( $attachment_id ) ) {
			return;
		}

		$author = html_entity_decode( $image['author'] );

		$more_info_link_esc = esc_url( $image['more_info_link'] );

		$is_unknown_license = '#N/A' === $image['license'];

		$caption_builder  = new Caption_Builder( $image );
		$proposed_caption = $caption_builder->build();

		$script_id = "wl-image-$idx";
		$row_id    = "wl-row-$idx";
		?>
        <tr id="<?php echo $row_id; ?>">
            <td><?php echo wp_get_attachment_image( $attachment_id ); ?></td>
            <td><?php echo esc_html( $image['filename'] ); ?></td>
            <td><?php echo esc_html( $image['license'] ); ?></td>
            <td><?php echo $author; ?></td>
            <td><?php echo $proposed_caption; ?></td>
            <td>
                <a href="<?php echo $more_info_link_esc; ?>"
                   target="_blank"><?php esc_html_e( 'More information', 'wordlift' ); ?></a>
            </td>
            <td>
				<?php
				$this->partial_used_in_posts( $image['posts_ids_as_featured_image'], __( 'Used as featured image in %d post(s):', 'wordlift' ) );
				$this->partial_used_in_posts( $image['posts_ids_as_embed'], __( 'Embedded in %d post(s):', 'wordlift' ) );
				?>
            </td>
            <td>
                <script type="application/json"
                        id="<?php echo $script_id; ?>"><?php echo json_encode( $image ); ?></script>
                <button data-id="<?php echo $script_id; ?>"
                        data-row-id="<?php echo $row_id; ?>"
                        data-action="wl_remove_all_images_task"
                        class="button wl-action-btn"><?php esc_html_e( 'Remove image', 'wordlift' ); ?></button>
				<?php if ( ! $is_unknown_license ) { ?>
                    <button data-id="<?php echo $script_id; ?>"
                            data-row-id="<?php echo $row_id; ?>"
                            data-action="wl_add_license_caption_or_remove"
                            class="button wl-action-btn"><?php esc_html_e( 'Add license caption', 'wordlift' ); ?></button>
				<?php } ?>
                <a class="button"
                   href=" <?php echo get_edit_post_link( $attachment_id ); ?>"
                   target="_blank"><?php esc_html_e( 'Edit image', 'wordlift' ); ?> <span
                            class="dashicons dashicons-external"></span></a>
            </td>
        </tr>
		<?php
	}

	private function partial_used_in_posts( $data, $label ) {

		// Bail out if there's not data.
		$count = count( $data );
		if ( 0 === $count ) {
			return;
		}

		echo esc_html( sprintf( $label, $count ) );
		foreach ( $data as $post_id ) {
			$post = get_post( $post_id ); ?>
            <a href="<?php echo get_permalink( $post_id ); ?>"><?php echo esc_html( $post->post_title ); ?></a>
			<?php
		}
	}

	function enqueue_scripts() {

		wp_enqueue_script( $this->get_menu_slug(), plugin_dir_url( __FILE__ ) . 'assets/image-license.js', array( 'wp-util' ), $this->version, true );
		wp_localize_script( $this->get_menu_slug(), '_wlImageLicensePageSettings', array(
			'_ajax_nonce' => array(
				Add_License_Caption_Or_Remove_Task::MENU_SLUG => wp_create_nonce( Add_License_Caption_Or_Remove_Task::MENU_SLUG ),
				Remove_All_Images_Task::MENU_SLUG             => wp_create_nonce( Remove_All_Images_Task::MENU_SLUG ),
			),
			'l10n'        => array(
				'Done'              => __( 'Done', 'wordlift' ),
				'An error occurred' => __( 'An error occurred', 'wordlift' ),
			)
		) );
	}

	private function exists( $attachment_id ) {
		global $wpdb;

		$sql =
			"
            SELECT COUNT( 1 )
            FROM {$wpdb->postmeta} pm1
            LEFT OUTER JOIN {$wpdb->postmeta} pm2
             ON pm2.post_id = pm1.post_id
              AND pm2.meta_key = %s
            WHERE pm1.post_id = %d
              AND pm1.meta_key = %s
              AND pm2.meta_value IS NULL
            ";

		return $wpdb->get_var( $wpdb->prepare(
			$sql,
			'_wl_image_license_fixed',
			$attachment_id,
			'_wp_attached_file'
		) );
	}

}
