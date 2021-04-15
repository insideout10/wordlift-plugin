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

if ( isset( $_POST ) ) {
	if ( isset( $_POST['wl_enable_video_sitemap'] ) ) {
		update_option( "_wl_video_sitemap_generation", 1 );
		do_action( 'wordlift_generate_video_sitemap_on' );
	} else {
		update_option( "_wl_video_sitemap_generation", 0 );
		do_action( 'wordlift_generate_video_sitemap_off' );
	}
}

?>
<h1><?php _e( 'API Settings', 'wordlift' ); ?></h1>
<p><?php _e( 'To let WordLift access metadata from Youtube or Vimeo you will need to add here your API Key.' ); ?></p>
<form method="post">
    <table>
        <tr>
            <td>
                Youtube API Key
            </td>
            <td>
				<?php
				$element = new Wordlift_Admin_Input_Element();
				$element->render( array(
					'id'    => 'wordlift_videoobject_youtube_api_key',
					'name'  => 'wordlift_videoobject_youtube_api_key',
					'value' => Youtube_Client::get_api_key()
				) );
				?>
            </td>
            <td>
                <a href="https://developers.google.com/youtube/registering_an_application"><?php _e( 'here', 'wordlift' ); ?></a>
				<?php _e( ' is how to get it', 'wordlift' ); ?>
            </td>
        </tr>

        <tr>
            <td>
                Vimeo API Key
            </td>
            <td>
				<?php
				$element = new Wordlift_Admin_Input_Element();
				$element->render( array(
					'id'    => 'wordlift_videoobject_vimeo_api_key',
					'name'  => 'wordlift_videoobject_vimeo_api_key',
					'value' => Vimeo_Client::get_api_key()
				) );
				?>
            </td>
            <td>
                <a href="https://developer.vimeo.com/api/guides/start"><?php _e( 'here', 'wordlift' ); ?></a>
				<?php _e( ' is how to get it', 'wordlift' ); ?>
            </td>
        </tr>

    </table>
    <h1><?php _e( 'Video Sitemap', 'wordlift' ); ?></h1>
    <p>
		<?php _e( 'The Video Sitemap works like any other XML Sitemap. Search engines will use it to display rich snippets in result pages.' ); ?>
    </p>
	<?php $is_checked = get_option( '_wl_video_sitemap_generation', false ) ? 'checked' : '' ?>
    <p> <?php _e( 'Enable Video Sitemap' ); ?>
        <input type="checkbox" name="wl_enable_video_sitemap" value="1" <?php echo $is_checked; ?> ></p>
    <p> <?php _e( 'Here is link to your Video Sitemap. Add it now, to Google Search Console.', 'wordlift' ); ?></p>

    <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary"
                             value="Save Changes">
    </p>
</form>