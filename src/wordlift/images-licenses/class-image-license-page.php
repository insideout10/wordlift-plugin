<?php

namespace Wordlift\Images_Licenses;

class Image_License_Page {

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

        <button class="button"><?php esc_html_e( 'Reload data', 'wordlift' ); ?></button>
        <button class="button"><?php esc_html_e( 'Remove all images', 'wordlift' ); ?></button>
        <button class="button"><?php esc_html_e( 'Add license caption to images and remove those with unknown license', 'wordlift' ); ?></button>
		<?php
		$images = $this->image_license_service->get_non_public_domain_images();

		foreach ( $images as $image ) {
			$this->render_image( $image );
		}
	}

	public function get_menu_slug() {

		return $this->menu_slug;
	}

	/**
	 * @param Image_License_Factory $image
	 */
	private function render_image( $image ) {

		$author             = html_entity_decode( $image['author'] );
		$license_esc        = esc_html( $image['license'] );
		$more_info_link_esc = esc_url( $image['more_info_link'] );

		$is_unknown_license = '#N/A' === $image['license'];
		$proposed_caption   = $is_unknown_license
			? sprintf( __( "Unknown license: check the %s or remove the image.", 'wordlift' ),
				sprintf( '<a href="%s" target="_blank">%s</a>', $more_info_link_esc, __( 'license compliance', 'wordlift' ) ) )
			: "<a href='$more_info_link_esc' target='_blank'>$license_esc</a> $author";

		?>
        <h2><?php echo esc_html( $image['filename'] ); ?></h2>

        <button class="button"><?php esc_html_e( 'Remove image', 'wordlift' ); ?></button>
		<?php if ( ! $is_unknown_license ) { ?>
            <button class="button"><?php esc_html_e( 'Add license caption', 'wordlift' ); ?></button>
		<?php } ?>
        <a class="button"
           href="<?php echo get_edit_post_link( $image['attachment_id'] ); ?>"
           target="_blank"><?php esc_html_e( 'Edit image', 'wordlift' ); ?></a>

        <dl>
            <dt><?php esc_html_e( 'Filename:', 'wordlift' ); ?></dt>
            <dd><?php echo esc_html( $image['filename'] ); ?></dd>
            <dt><?php esc_html_e( 'Thumbnail:', 'wordlift' ); ?></dt>
            <dd><?php echo wp_get_attachment_image( $image['attachment_id'] ); ?></dd>
            <dt><?php esc_html_e( 'License:', 'wordlift' ); ?></dt>
            <dd><?php echo esc_html( $image['license'] ); ?></dd>
            <dt><?php esc_html_e( 'Author:', 'wordlift' ); ?></dt>
            <dd><?php echo $author; ?></dd>
            <dt><?php esc_html_e( 'Proposed Caption:', 'wordlift' ); ?></dt>
            <dd><?php echo $proposed_caption; ?></dd>
            <dt><?php esc_html_e( 'More Info:', 'wordlift' ); ?></dt>
            <dd>
                <a href="<?php echo $more_info_link_esc; ?>"
                   target="_blank"><?php esc_html_e( 'More information', 'wordlift' ); ?></a>
            </dd>
			<?php
			$this->partial_used_in_posts( $image['posts_ids_as_featured_image'], __( 'Used as featured image in %d post(s):', 'wordlift' ) );
			$this->partial_used_in_posts( $image['posts_ids_as_embed'], __( 'Embedded in %d post(s):', 'wordlift' ) );
			?>
        </dl>
		<?php
	}

	private function partial_used_in_posts( $data, $label ) {

		// Bail out if there's not data.
		$count = count( $data );
		if ( 0 === $count ) {
			return;
		}
		?>
        <dt><?php echo esc_html( sprintf( $label, $count ) ); ?></dt>
        <dd><?php
			foreach ( $data as $post_id ) {
				$post = get_post( $post_id ); ?>
                <a href="<?php echo get_permalink( $post_id ); ?>"><?php echo esc_html( $post->post_title ); ?></a>
				<?php
			}
			?>
        </dd>
		<?php
	}

}
