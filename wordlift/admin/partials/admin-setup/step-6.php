<!-- Pane 6 content -->
<script type="text/html" id="page-5">
	<h2 class="page-title">
		<?php esc_html_e( 'Publisher', 'wordlift' ); ?>	
	</h2>

	<p class="page-txt">
		<?php esc_html_e( 'Are you going to publish as an individual or as a company?', 'wordlift' ); ?>
	</p>

	<div class="radio-wrapper">
		<label for="personal">
			<input
				id="personal"
				type="radio"
				name="user_type"
				value="person"
				checked
			>
			<span class="radio"><span class="check"></span></span>

			<span class="label">
				<?php esc_html_e( 'Personal', 'wordlift' ); ?>	
			</span>
		</label>

		<label for="company">
			<input
				id="company"
				type="radio"
				name="user_type"
				value="organization"
			>
			
			<span class="radio"><span class="check"></span></span>

			<span class="label">
				<?php esc_html_e( 'Company', 'wordlift' ); ?>
			</span>
		</label>
	</div>
	<input
		type="text"
		id="name"
		name="name"
		data-wl-name="wl-name"
		value=""
		autocomplete="off"
		class="untouched invalid"
		placeholder="<?php esc_attr_e( "What's your name?", 'wordlift' ); ?>"
	>

	<div data-wl-logo="wl-logo">
		<input type="hidden" name="logo" />
		<div data-wl-logo-preview="wl-logo-preview" class="wl-logo-preview">
			<a 
				data-wl-remove-logo="wl-remove-logo"
				href="javascript:void(0);"
				class="fa fa-times"
			></a>
		</div>

		<a
			data-wl-add-logo="wl-add-logo"
			class="add-logo"
			href="javascript:void(0);"
		>
			<?php esc_html_e( 'Add your logo', 'wordlift' ); ?>
		</a>
	</div>

	<div class="btn-wrapper">
		<input
			type="submit"
			id="btn-finish"
			class="wl-default-action"
			value="<?php esc_attr_e( 'Finish', 'wordlift' ); ?>"
		>
	</div>
</script>
