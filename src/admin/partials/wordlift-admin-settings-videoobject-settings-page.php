<?php
?>
    <h1><?php _e( 'API Settings', 'wordlift' ); ?></h1>
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
		            'id'          => 'wordlift_videoobject_youtube_api_key',
		            'name'        => 'wordlift_videoobject_youtube_api_key',
                    'value' => get_option( )
	            ) );
	            ?>
            </td>
        </tr>
    </table>
