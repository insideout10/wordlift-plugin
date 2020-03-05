<?php

namespace Wordlift\Images_Licenses;

class Image_License_Notifier {
	/**
	 * @var array
	 */
	private $data;
	/**
	 * @var Image_License_Page
	 */
	private $image_license_page;

	/**
	 * Image_License_Notifier constructor.
	 *
	 * @param array $data
	 * @param Image_License_Page $image_license_page
	 */
	public function __construct( $data, $image_license_page ) {

		add_action( 'admin_init', array( $this, 'admin_init', ) );

		$this->data               = $data;
		$this->image_license_page = $image_license_page;

	}

	public function admin_init() {

		$count = count( $this->data );

		if ( 0 < $count ) {
			$that = $this;
			add_action( 'admin_notices', function () use ( $count, $that ) {
				?>
                <div class="notice notice-error">
                    <p>
						<?php
						$image_compliance_link = sprintf( '<a href="%s">', admin_url( 'admin.php?page=' . $that->image_license_page->get_menu_slug() ) )
						                         . __( 'License Compliance', 'wordlift' ) . '</a>';
						$message               = esc_html__( 'WordLift found %d image(s) that might not comply with their license. Open %s to fix this error.', 'wordlift' );
						echo sprintf( $message, $count, $image_compliance_link );
						?>
                    </p>
                </div>
				<?php
			} );
		}

	}

}
