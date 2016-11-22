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
<div id="title"><?php esc_html_e( 'Publisher', 'wordlift' ) ?></div>
<div id="message"><?php esc_html_e( 'Are you going to publish as an individual or as a company?', 'wordlift' ) ?></div>
<div id="radio">
	<label for="personal"><input id="personal" type="radio" name="user_type"
	                             value="personal" <?php checked( $type, 'personal' ) ?>><?php esc_html_e( 'Personal', 'wordlift' ) ?>
	</label>
	<label for="company"><input id="company" type="radio" name="user_type"
	                            value="company" <?php checked( $type, 'company' ) ?>><?php esc_html_e( 'Company', 'wordlift' ) ?>
	</label>
</div>
<div id="input"><input class="input" id="key" type="text" name="key" value="<?php echo esc_attr( $name ) ?>"
                       placeholder="<?php esc_html_e( 'Name', 'wordlift' ) ?>"></div>
<div id="addlogo" <?php if ( 0 != $image_id )
	echo 'style="display:none"' ?>><a class="wl-add-logo"
                                      href="javascript:void(0);"><?php esc_html_e( 'Add your logo', 'wordlift' ) ?></a>
</div>
<div id="logo" style="<?php echo( 0 == $image_id ? 'display:none' : '' ); ?>">
	<img src="<?php echo esc_attr( 0 == $image_id ? '' : $image_url ); ?>"
	     data-id="<?php echo esc_attr( 0 == $image_id ? '' : $image_id ); ?>"
	     width="100" height="100">
	<a href="javascript:void(0);" title="<?php esc_html_e( 'Remove the logo', 'wordlift' ) ?>"><span
			class="fa fa-times"></a>
</div>
<div id="buttons">
	<a id="nextstep" onclick="savevalue()"
	   href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'step', '5' ), 'wordlift_finish_nonce', '_wl_finish_nonce' ) ); ?>"><?php esc_html_e( 'Finish', 'wordlift' ); ?></a>
</div>
<script type="text/javascript">

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
