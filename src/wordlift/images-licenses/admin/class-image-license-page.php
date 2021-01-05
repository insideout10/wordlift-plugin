<?php

namespace Wordlift\Images_Licenses\Admin;

use Wordlift\Images_Licenses\Caption_Builder;
use Wordlift\Images_Licenses\Tasks\Add_License_Caption_Or_Remove_Task;
use Wordlift\Images_Licenses\Tasks\Remove_All_Images_Task;
use Wordlift\Wordpress\Submenu_Page_Base;

class Image_License_Page extends Submenu_Page_Base {

	/**
	 * @var array
	 */
	private $data;

	/**
	 * @var string
	 */
	private $version;

	/**
	 * Image_License_Page constructor.
	 *
	 * @param array $data
	 * @param string $version
	 */
	public function __construct( $data, $version ) {
		$count = count( $data );

		// Display the page in the menu only if there's something to do.
		if ( 0 === $count ) {
			return;
		}
		$menu_title = __( 'License Compliance', 'wordlift' ) .
		              sprintf( '<span class="update-plugins count-%1$d"><span class="license-compliance-count">%1$d</span></span>', $count );

		parent::__construct( 'wl_image_license_page', __( 'License Compliance', 'wordlift' ), 'manage_options', 'wl_admin_menu', $menu_title );

		$this->data    = $data;
		$this->version = $version;

	}

	public function render() {
		?>
        <h1><?php esc_html_e( 'License Compliance', 'wordlift' ); ?></h1>

        <p><?php esc_html_e( 'By choosing "Remove All Images" you will ', 'wordlift' ); ?>
            <strong><?php esc_html_e( 'remove from your website all images that do not have a Public Domain or CC0 license', 'wordlift' ); ?></strong>.
			<?php esc_html_e( 'Alternatively, WordLift can write the terms of the detected license in the caption of each image. Make sure
            that attribution, when required, is visible to your readers. You can also selectively choose from the list
            of images below if removing or adding the license for each of the images.', 'wordlift' ); ?></p>
        <p><span class="dashicons dashicons-warning"></span> <?php esc_html_e( 'As site owner you are ultimately responsible for the images
            being published on your website.', 'wordlift' ); ?></p>

        <p class="top">
            <a class="button"
               href="<?php echo admin_url( 'admin.php?page=wl_images_licenses__reload_data' ); ?>"><?php esc_html_e( 'Reload data', 'wordlift' ); ?></a>
            <a class="button"
               href="<?php echo admin_url( 'admin.php?page=wl_images_licenses__remove_all_images' ); ?>"><?php esc_html_e( 'Remove all images', 'wordlift' ); ?></a>
            <a class="button"
               href="<?php echo admin_url( 'admin.php?page=wl_images_licenses__add_license_caption_or_remove' ); ?>"><?php esc_html_e( 'Add license caption to images and remove those with unknown license', 'wordlift' ); ?></a>
        </p>

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
			$images = $this->data;

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
            <td><?php echo wp_get_attachment_image( $attachment_id, array( 100, ) ); ?></td>
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
                        data-action="wl_remove_all_images_task__single"
                        class="button wl-action-btn"><?php esc_html_e( 'Remove image', 'wordlift' ); ?></button>
				<?php if ( ! $is_unknown_license ) { ?>
                    <button data-id="<?php echo $script_id; ?>"
                            data-row-id="<?php echo $row_id; ?>"
                            data-action="wl_add_license_caption_or_remove__single"
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
				Add_License_Caption_Or_Remove_Task::MENU_SLUG . '__single' => wp_create_nonce( Add_License_Caption_Or_Remove_Task::MENU_SLUG ),
				Remove_All_Images_Task::MENU_SLUG . '__single'             => wp_create_nonce( Remove_All_Images_Task::MENU_SLUG ),
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
