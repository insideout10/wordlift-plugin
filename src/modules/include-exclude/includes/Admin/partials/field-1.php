<p>
	<label for="wl_exclude_include_urls_settings_urls">
		<?php
		echo sprintf(
			'%s <code>%s</code>',
			esc_html__( 'One relative or absolute URL per line. Relative URLs will be converted to absolute in context of', 'wordlift' ),
			esc_url( site_url() )
		);
		?>
	</label>
</p>
<p>
	<textarea id="wl_exclude_include_urls_settings_urls" name='wl_exclude_include_urls_settings[urls]'
			  class="large-text code"
              rows="10"><?php echo esc_textarea( isset( $options['urls'] ) ? $options['urls'] : '' ); // phpcs:ignore ?></textarea>
</p>
