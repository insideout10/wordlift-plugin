<?php

use Wordlift\Videoobject\Provider\Client\Vimeo_Client;
use Wordlift\Videoobject\Provider\Client\Youtube_Client;

if ( isset( $_POST['wordlift_videoobject_settings_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['wordlift_videoobject_settings_nonce'] ) ), 'wordlift_videoobject_settings' ) ) {

	if ( isset( $_POST['wordlift_videoobject_youtube_api_key'] ) || isset( $_POST['wordlift_videoobject_vimeo_api_key'] ) ) {

		/**
		 * @todo: does this fields need to be encrypted before saving ?
		 */
		$youtube_api_key = sanitize_text_field( wp_unslash( (string) $_POST['wordlift_videoobject_youtube_api_key'] ) );
		$vimeo_api_key   = sanitize_text_field( wp_unslash( (string) $_POST['wordlift_videoobject_vimeo_api_key'] ) );

		if ( $youtube_api_key ) {
			update_option( Youtube_Client::get_api_key_option_name(), $youtube_api_key );
		}

		if ( $vimeo_api_key ) {
			update_option( Vimeo_Client::get_api_key_option_name(), $vimeo_api_key );
		}
	}

	if ( isset( $_POST['wl_enable_video_sitemap'] ) ) {
		update_option( '_wl_video_sitemap_generation', 1 );
		// flush the rewrite rules.
		flush_rewrite_rules();
	} else {
		update_option( '_wl_video_sitemap_generation', 0 );
		// flush the rewrite rules.
		flush_rewrite_rules();
	}
}
?>
<h1><?php esc_html_e( 'API Settings', 'wordlift' ); ?></h1>
<p><?php esc_html_e( 'To let WordLift access metadata from YouTube or Vimeo you will need to add here your API Key.', 'wordlift' ); ?></p>
<form method="post">
	<table id="wl-settings-page__video-api">
		<tr>
			<td>
				<?php esc_html_e( 'YouTube API Key', 'wordlift' ); ?>
			</td>
			<td>
				<?php
				$element = new Wordlift_Admin_Input_Element();
				$element->render(
					array(
						'id'    => 'wordlift_videoobject_youtube_api_key',
						'name'  => 'wordlift_videoobject_youtube_api_key',
						'value' => Youtube_Client::get_api_key(),
						'data'  => array( 'type' => 'youtube' ),
					)
				);
				?>
			</td>
			<td>
				<a href="https://developers.google.com/youtube/registering_an_application"><?php esc_html_e( 'Click here', 'wordlift' ); ?></a>
				<?php esc_html_e( ' for instructions on getting your YouTube API Key', 'wordlift' ); ?>
			</td>
		</tr>

		<tr>
			<td>
				<?php esc_html_e( 'Vimeo API Key', 'wordlift' ); ?>
			</td>
			<td>
				<?php
				$element = new Wordlift_Admin_Input_Element();
				$element->render(
					array(
						'id'    => 'wordlift_videoobject_vimeo_api_key',
						'name'  => 'wordlift_videoobject_vimeo_api_key',
						'value' => Vimeo_Client::get_api_key(),
						'data'  => array( 'type' => 'vimeo' ),
					)
				);
				?>
			</td>
			<td>
				<a href="https://developer.vimeo.com/api/guides/start"><?php esc_html_e( 'Click here', 'wordlift' ); ?></a>
				<?php esc_html_e( ' for instructions on getting your Vimeo API Key', 'wordlift' ); ?>
			</td>
		</tr>

	</table>
	<h1><?php esc_html_e( 'Video Sitemap', 'wordlift' ); ?></h1>
	<p>
		<?php esc_html_e( 'The Video Sitemap works like any other XML Sitemap. Search engines will use it to display rich snippets in result pages.', 'wordlift' ); ?>
	</p>
	<?php $wl_is_sitemap_enabled = esc_attr( get_option( '_wl_video_sitemap_generation', false ) ? 'checked' : '' ); ?>
	<p> <?php esc_html_e( 'Enable Video Sitemap', 'wordlift' ); ?>
		<input type="checkbox" name="wl_enable_video_sitemap"
			   value="1" <?php echo esc_html( $wl_is_sitemap_enabled ); ?> ></p>
	<p>
		<?php
		if ( $wl_is_sitemap_enabled ) {
			$wl_sitemap_link = esc_attr( get_home_url( null, 'wl-video-sitemap.xml' ) );
			/* translators: %s: The link to the Video Sitemap. */
			echo wp_kses( sprintf( __( 'Here is <a href="%s">link</a> to your Video Sitemap. Add it now, to Google Search Console.', 'wordlift' ), $wl_sitemap_link ), array( 'a' => array( 'href' => array() ) ) );
		}
		?>
	</p>
	<?php wp_nonce_field( 'wordlift_videoobject_settings', 'wordlift_videoobject_settings_nonce', false ); ?>
	<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary"
							 value="Save Changes">
	</p>
</form>

