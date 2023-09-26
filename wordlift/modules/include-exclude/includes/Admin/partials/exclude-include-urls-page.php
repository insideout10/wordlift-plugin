<div class="wrap" id="wl-settings-page">
	<h2><?php echo esc_html__( 'Exclude / Include URLs', 'wordlift' ); ?></h2>
	<form action='options.php' method='post'>
		<?php
		settings_fields( 'wl_exclude_include_urls_settings_group' );
		do_settings_sections( 'wl_exclude_include_urls_settings_page' );
		submit_button();
		?>
	</form>
</div>
