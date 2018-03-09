<!-- Pane 3 content -->
<script type="text/html" id="page-2">
	<h2 class="page-title">
		<?php esc_html_e( 'License Key', 'wordlift' ); ?>
	</h2>

	<p class="page-txt">
		<?php
			esc_html_e( 'If you already purchased a plan, check your email, get the activation key from your inbox and insert it in the field below. Otherwise ....', 'wordlift' ); 
		?>
	</p>
	<input
		type="text"
		data-wl-key="wl-key"
		class="invalid untouched"
		id="key"
		name="key"
		value=""
		autocomplete="off"
		placeholder="Activation Key"
	>
	
	<div class="btn-wrapper">
		<a id="btn-grab-a-key"
			href="https://wordlift.io/pricing/?utm_campaign=wl_activation_grab_the_key"
			target="_tab"
			class="button wl-default-action"
		>
			<?php esc_html_e( 'Grab a Key!', 'wordlift' ); ?>
		</a>

		<input
			id="btn-license-key-next"
			type="button"
			data-wl-next="wl-next"
			class="button"
			value="<?php esc_attr_e( 'Next', 'wordlift' ); ?>"
		>
	</div>
</script>
