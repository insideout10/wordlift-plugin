<!-- Pane 2 content -->
<script type="text/html" id="page-1">
	<h2 class="page-title">
		<?php esc_html_e( 'Diagnostic', 'wordlift' ); ?>
	</h2>
	<br>
	<p class="page-txt">
		<?php
			esc_html_e( 'Help us improve our product by automatically sending diagnostic and usage data.', 'wordlift' );
		?>
	</p>
	<input
		type="checkbox"
		id="share-diagnostic"
		name="share-diagnostic"
		class="valid untouched"
		checked
	>
	<label for="share-diagnostic">
		<?php esc_html_e( 'Share diagnostic data', 'wordlift' ); ?>
	</label>
	
	<p class="privacy-policy-details">
		<a href="https://wordlift.io/privacy-policy/" target="_blank">
			<?php esc_html_e( 'About our privacy policy...', 'wordlift' ); ?>	
		</a>
	</p>

	<div class="btn-wrapper">
		<input
			type="button"
			data-wl-next="wl-next"
			class="wl-default-action"
			value="<?php esc_attr_e( 'Next', 'wordlift' ); ?>"
		>
	</div>
</script>
