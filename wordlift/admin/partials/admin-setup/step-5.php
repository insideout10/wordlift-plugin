<!-- Pane 5 content -->
<script type="text/html" id="page-4">
	<h2 class="page-title">
		<?php esc_html_e( 'Country', 'wordlift' ); ?>
	</h2>

	<p class="page-txt">
		<?php esc_html_e( 'Each WordLift key can be used only in one language.', 'wordlift' ); ?>
	</p>

	<?php

	// Get the language locale part.
	$parts    = explode( '_', get_locale() );
	$language = isset( $parts[0] ) ? $parts[0] : '';
	$country  = isset( $parts[1] ) ? strtolower( $parts[1] ) : '';
	?>

	<br>

	<?php
	// Render country select element.
    // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable
	$country_select->render(
		array(
			'id'     => 'wl-country-code',
			'name'   => 'wl-country-code',
			'lang'   => $language,
			'value'  => $country,
			'notice' => __( 'The selected language is not supported in this country.</br>Please choose another country or language.', 'wordlift' ),
		)
	);
	?>

	<div class="btn-wrapper">
		<input
				type="button"
				data-wl-next="wl-next"
				class="wl-default-action"
				value="<?php esc_attr_e( 'Next', 'wordlift' ); ?>"
		>
	</div>
</script>
