<?php
?>
<h1><?php use Wordlift\Videoobject\Provider\Client\Vimeo_Client;
	use Wordlift\Videoobject\Provider\Client\Youtube_Client;

	_e( 'API Settings', 'wordlift' ); ?></h1>
<p><?php _e( 'To let WordLift access metadata from Youtube or Vimeo you will need to add here your API Key.' ); ?></p>
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
			<?php _e( 'here', 'wordlift' );
			_e( ' is how to get it', 'wordlift' ); ?>
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
			<?php _e( 'here', 'wordlift' );
			_e( ' is how to get it', 'wordlift' ); ?>
        </td>
    </tr>

</table>
