<p>
	<input type="radio" id="wl_exclude_include_urls_settings_include_exclude_include"
		   name="wl_exclude_include_urls_settings[include_exclude]"
           value="exclude" <?php checked( is_array( $options ) ? $options['include_exclude'] : '', 'exclude' ); // phpcs:ignore ?>>
	<label for="wl_exclude_include_urls_settings_include_exclude_include"><?php echo esc_html__( 'Exclude', 'wordlift' ); ?></label>
</p>
<p>
	<input type="radio" id="wl_exclude_include_urls_settings_include_exclude_exclude"
		   name="wl_exclude_include_urls_settings[include_exclude]"
           value="include" <?php checked( is_array( $options ) ? $options['include_exclude'] : '', 'include' ); // phpcs:ignore ?>>
	<label for="wl_exclude_include_urls_settings_include_exclude_exclude"><?php echo esc_html__( 'Include', 'wordlift' ); ?></label>
</p>
