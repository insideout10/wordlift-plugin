<p>
	<input type="radio" id="wl_exclude_include_urls_settings_type_include" name="wl_exclude_include_urls_settings[type]" value="EXCLUDE" <?php checked( $options['type'], 'EXCLUDE', true ); // phpcs:ignore ?>>
	<label for="wl_exclude_include_urls_settings_type_include"><?php echo esc_html__( 'Exclude', 'wordlift' ); ?></label>
</p>
<p>
	<input type="radio" id="wl_exclude_include_urls_settings_type_exclude" name="wl_exclude_include_urls_settings[type]" value="INCLUDE" <?php checked( $options['type'], 'INCLUDE', true ); // phpcs:ignore ?>>
	<label for="wl_exclude_include_urls_settings_type_exclude"><?php echo esc_html__( 'Include', 'wordlift' ); ?></label>
</p>
