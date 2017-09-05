<?php

class WL_Metabox_Field_sameas extends WL_Metabox_Field {

	public function __construct( $args ) {
		parent::__construct( $args['sameas'] );
	}

	/**
	 * Only accept URIs
	 */
	public function sanitize_data_filter( $value ) {

		// Call our sanitizer helper.
		return Wordlift_Sanitizer::sanitize_url( $value );
	}

	/**
	 * @inheritdoc
	 */
	public function html_input( $value ) {
		$error_text = esc_js( __( 'Has to be a proper url', 'wordlift' ) );
		$html = <<<EOF
 			<div class="wl-input-wrapper">
 				<input type="text" id="$this->meta_name" name="wl_metaboxes[$this->meta_name][]" value="$value" style="width:88%" />
 				<button class="button wl-remove-input wl-button" type="button">Remove</button>
 			</div>

			<script type="text/javascript">

				(function ($) {

					function validate_input(element) {
						var value = $(element).val();

						if ( value == '') {
							element.setCustomValidity('');
							return;
						}

						var regex = /\s*(http|https):\/\/.*/;
						if ( !regex.test( value ) ) {
							element.setCustomValidity('$error_text');
						} else {
							element.setCustomValidity('');
						}

					}

					$('#$this->meta_name').on('change', function () {
						validate_input( this );
					})

					$('#$this->meta_name').each( function () {
						validate_input( this );
					})

				})(jQuery);

			</script>
EOF;

		return $html;
	}
}
