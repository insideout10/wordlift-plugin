<!-- Pane 4 content -->
<script type="text/html" id="page-3">
	<h2 class="page-title">
		<?php esc_html_e( 'Vocabulary', 'wordlift' ); ?>
	</h2>

	<p class="page-txt">
		<?php esc_html_e( 'All new pages created with WordLift, will be stored inside your internal vocabulary. You can customize the url pattern of these pages in the field below. Check our FAQs if you need more info.', 'wordlift' ); ?>
	</p>

	<input
			type="text"
			id="vocabulary"
			name="vocabulary"
			autocomplete="off"
			value="vocabulary"
			class="valid untouched"
			data-wl-vocabulary="wl-vocabulary"
	>

	<p class="page-det">
		<?php esc_html_e( 'Leave it empty to place your entities in the root folder of your website', 'wordlift' ); ?>
		<?php esc_html_e( ' (requires the permalink settings to be set to Post name)', 'wordlift' ); ?>
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
