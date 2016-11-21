<?php
/**
 * Install Wizard Step #5.
 *
 * This file provides the step #5 for the install wizard.
 *
 * @link       https://wordlift.io
 * @since      3.9.0
 *
 * @package    Wordlift
 * @subpackage Wordlift/admin/partials
 */

?>
<div id="title"><?php _e( 'Publisher', 'wordlift' ) ?></div>
<div id="message"><?php _e( 'Are you going to publish as an individual or as a company?', 'wordlift' ) ?></div>
<div id="radio">
	<label for="personal"><input id="personal" type="radio" name="user_type"
	                             value="personal" <?php checked( $type, 'personal' ) ?>><?php _e( 'Personal', 'wordlift' ) ?>
	</label>
	<label for="company"><input id="company" type="radio" name="user_type"
	                            value="company" <?php checked( $type, 'company' ) ?>><?php _e( 'Company', 'wordlift' ) ?>
	</label>
</div>
<div id="input"><input class="input" id="key" type="text" name="key" value="<?php echo esc_attr( $name ) ?>"
                       placeholder="<?php _e( 'Name', 'wordlift' ) ?>"></div>
<div id="addlogo" <?php if ( $image_id != 0 )
	echo 'style="display:none"' ?>><a href="#"
                                      onclick="return addlogo();"><?php _e( 'Add your logo', 'wordlift' ) ?></a>
</div>
<div id="logo" <?php if ( $image_id == 0 )
	echo 'style="display:none"' ?>>
	<img src="<?php echo esc_attr( $image_url ) ?>" data-id="<?php echo esc_attr( $image_id ) ?>" width="100"
	     height="100">
	<a id="deletelogo" onclick="return deletelogo()" href="#"
	   title="<?php _e( 'Remove the logo', 'wordlift' ) ?>"><span class="fa fa-times"></a>
</div>
<div id="buttons">
	<a id="nextstep" onclick="savevalue()"
	   href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'step', 'finish' ), 'wordlift_finish_nonce', '_wl_finish_nonce' ) ); ?>"><?php _e( 'Finish', 'wordlift' ); ?></a>
</div>
<script type="text/javascript">
	var mediaUploader;

	function addlogo() {
		mediaUploader = wp.media({
			title: '<?php _e( 'WordLift Choose Logo', 'wordlift' )?>',
			button: {
				text: '<?php _e( 'Choose Logo', 'wordlift' )?>'
			}, multiple: false
		});

		// When a file is selected, grab the URL and set it as the text field's value
		mediaUploader.on('select', function () {
			attachment = mediaUploader.state().get('selection').first().toJSON();
			jQuery('#logo img').attr('src', attachment.url);
			jQuery('#logo img').attr('data-id', attachment.id);
			jQuery('#logo').show();
			jQuery('#addlogo').hide();
		});
		// Open the uploader dialog
		mediaUploader.open();

		return false;
	}

	function deletelogo() {
		jQuery('#logo img').attr('src', '');
		jQuery('#logo img').attr('data-id', '');
		jQuery('#logo').hide();
		jQuery('#addlogo').show();
		return false;
	}

	function savevalue() {
		var type = jQuery('input[name="user_type"]:checked').val();
		document.cookie = "wl_type=" + type + ';path=' + '<?php echo admin_url()?>';

		var name = jQuery('#key').val();
		document.cookie = "wl_name=" + name + ';path=' + '<?php echo admin_url()?>';

		var url = jQuery('#logo img').attr('src');
		document.cookie = "wl_image_url=" + url + ';path=' + '<?php echo admin_url()?>';

		var id = jQuery('#logo img').attr('data-id');
		document.cookie = "wl_image_id=" + id + ';path=' + '<?php echo admin_url()?>';
	}
</script>
