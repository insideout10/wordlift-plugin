<!-- Pane 5 content -->
<script type="text/html" id="page-4">
	<h2 class="page-title">
		<?php esc_html_e( 'Language', 'wordlift' ); ?>	
	</h2>

	<p class="page-txt">
		<?php esc_html_e( 'Each WordLift key can be used only in one language. Pick yours.', 'wordlift' ); ?>
	</p>

	<select
		id="language"
		name="language"
		placeholder="<?php esc_attr_e( 'Choose your language', 'wordlift' ); ?>"
	>
		<?php

		// Get WordLift's supported languages.
		$languages = Wordlift_Languages::get_languages();

		// Get WP's locale.
		$locale = get_locale();

		// Get the language locale part.
		$parts = explode( '_', $locale );

		// If we support WP's configured language, then use that, otherwise use English by default.
		$language = isset( $languages[ $parts[0] ] ) ? $parts[0] : 'en';

		// Print all the supported language, preselecting the one configured in WP (or English if not supported).
		foreach ( $languages as $code => $label ) :
		?>
			<option
				value="<?php echo esc_attr( $code ); ?>"
				<?php echo selected( $code, $language, false ); ?>
			>
				<?php echo esc_html( $label ); ?>
			</option>
		<?php endforeach; ?>
	</select>

	<div class="btn-wrapper">
		<input
			type="button"
			data-wl-next="wl-next"
			class="wl-default-action"
			value="<?php esc_attr_e( 'Next', 'wordlift' ); ?>"
		>
	</div>
</script>
