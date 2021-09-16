<?php

use Wordlift\Videoobject\Provider\Client\Vimeo_Client;
use Wordlift\Videoobject\Provider\Client\Youtube_Client;


if ( isset( $_POST['wordlift_videoobject_youtube_api_key'] )
	 || isset( $_POST['wordlift_videoobject_vimeo_api_key'] ) ) {

	/**
	 * @todo: does this fields need to be encrypted before saving ?
	 */
	$youtube_api_key = (string) $_POST['wordlift_videoobject_youtube_api_key'];
	$vimeo_api_key   = (string) $_POST['wordlift_videoobject_vimeo_api_key'];

	if ( $youtube_api_key ) {
		update_option( Youtube_Client::get_api_key_option_name(), $youtube_api_key );
	}

	if ( $vimeo_api_key ) {
		update_option( Vimeo_Client::get_api_key_option_name(), $vimeo_api_key );
	}
}

if ( isset( $_POST['submit'] ) ) {
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
<h1><?php _e( 'API Settings', 'wordlift' ); ?></h1>
<p><?php _e( 'To let WordLift access metadata from YouTube or Vimeo you will need to add here your API Key.', 'wordlift' ); ?></p>
<form method="post">
	<table>
		<tr>
			<td>
				<?php _e( 'YouTube API Key', 'wordlift' ); ?>
			</td>
			<td>
				<?php
				$element = new Wordlift_Admin_Input_Element();
				$element->render(
					array(
						'id'    => 'wordlift_videoobject_youtube_api_key',
						'name'  => 'wordlift_videoobject_youtube_api_key',
						'value' => Youtube_Client::get_api_key(),
					)
				);
				?>
			</td>
			<td>
				<a href="https://developers.google.com/youtube/registering_an_application"><?php _e( 'Click here', 'wordlift' ); ?></a>
				<?php _e( ' for instructions on getting your YouTube API Key', 'wordlift' ); ?>
			</td>
		</tr>

		<tr>
			<td>
				<?php _e( 'Vimeo API Key', 'wordlift' ); ?>
			</td>
			<td>
				<?php
				$element = new Wordlift_Admin_Input_Element();
				$element->render(
					array(
						'id'    => 'wordlift_videoobject_vimeo_api_key',
						'name'  => 'wordlift_videoobject_vimeo_api_key',
						'value' => Vimeo_Client::get_api_key(),
					)
				);
				?>
			</td>
			<td>
				<a href="https://developer.vimeo.com/api/guides/start"><?php _e( 'Click here', 'wordlift' ); ?></a>
				<?php _e( ' for instructions on getting your Vimeo API Key', 'wordlift' ); ?>
			</td>
		</tr>

	</table>
	<h1><?php _e( 'Video Sitemap', 'wordlift' ); ?></h1>
	<p>
		<?php _e( 'The Video Sitemap works like any other XML Sitemap. Search engines will use it to display rich snippets in result pages.', 'wordlift' ); ?>
	</p>
	<?php $wl_is_sitemap_enabled = esc_attr( get_option( '_wl_video_sitemap_generation', false ) ? 'checked' : '' ); ?>
	<p> <?php _e( 'Enable Video Sitemap' ); ?>
		<input type="checkbox" name="wl_enable_video_sitemap" value="1" <?php echo $wl_is_sitemap_enabled; ?> ></p>
	<p> 
	<?php
	if ( $wl_is_sitemap_enabled ) {
		$wl_sitemap_link = esc_attr( get_home_url( null, 'wl-video-sitemap.xml' ) );
		printf( __( 'Here is <a href="%s">link</a> to your Video Sitemap. Add it now, to Google Search Console.', 'wordlift' ), $wl_sitemap_link );
	}
	?>
		</p>

	<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary"
							 value="Save Changes">
	</p>
</form>
