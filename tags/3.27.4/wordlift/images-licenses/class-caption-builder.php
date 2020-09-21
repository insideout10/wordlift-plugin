<?php

namespace Wordlift\Images_Licenses;

class Caption_Builder {
	/**
	 * @var array
	 */
	private $image;

	/**
	 * Caption_Builder constructor.
	 *
	 * @param array $image
	 */
	public function __construct( $image ) {

		$this->image = $image;

	}

	public function build() {
		$author             = html_entity_decode( $this->image['author'] );
		$license_esc        = esc_html( $this->image['license'] );
		$more_info_link_esc = esc_url( $this->image['more_info_link'] );

		$is_unknown_license = '#N/A' === $this->image['license'];
		$proposed_caption   = $is_unknown_license
			? sprintf( __( "Unknown license: check the %s or remove the image.", 'wordlift' ),
				sprintf( '<a href="%s" target="_blank">%s</a>', $more_info_link_esc, __( 'license compliance', 'wordlift' ) ) )
			: "<a href='$more_info_link_esc' target='_blank'>$license_esc</a> $author";

		return $proposed_caption;
	}

}
