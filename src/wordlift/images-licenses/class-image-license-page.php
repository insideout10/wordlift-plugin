<?php

namespace Wordlift\Images_Licenses;

use Wordlift\Wordpress\Page;

class Image_License_Page implements Page {

	/**
	 * @var string
	 */
	private $menu_slug;

	/**
	 * @var Image_License_Service
	 */
	private $image_license_service;

	/**
	 * Image_License_Page constructor.
	 *
	 * @param Image_License_Service $image_license_service
	 */
	public function __construct( $image_license_service ) {

		$this->menu_slug             = 'wl_image_license_page';
		$this->image_license_service = $image_license_service;

		add_action( 'admin_menu', array( $this, 'admin_menu', ) );

	}

	public function admin_menu() {

		add_submenu_page(
			null,
			__( 'License Compliance', 'wordlift' ),
			__( 'License Compliance', 'wordlift' ),
			'',
			$this->menu_slug,
			array(
				$this,
				'render'
			) );

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

			foreach ( $images as $image ) {
				$this->render_image( $image );
			}
			?>
        </table>
		<?php
	}

	public function get_menu_slug() {

		return $this->menu_slug;
	}

	/**
	 * @param array $image
	 */
	private function render_image( $image ) {

		$author             = html_entity_decode( $image['author'] );
		$more_info_link_esc = esc_url( $image['more_info_link'] );

		$is_unknown_license = '#N/A' === $image['license'];

		$caption_builder  = new Caption_Builder( $image );
		$proposed_caption = $caption_builder->build();
		?>
        <tr>
            <td><?php echo esc_html( $image['filename'] ); ?></td>
            <td><?php echo wp_get_attachment_image( $image['attachment_id'] ); ?></td>
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
                <button class="button"><?php esc_html_e( 'Remove image', 'wordlift' ); ?></button>
				<?php if ( ! $is_unknown_license ) { ?>
                    <button class="button"><?php esc_html_e( 'Add license caption', 'wordlift' ); ?></button>
				<?php } ?>
                <a class="button"
                   href="<?php echo get_edit_post_link( $image['attachment_id'] ); ?>"
                   target="_blank"><?php esc_html_e( 'Edit image', 'wordlift' ); ?></a>
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

}
