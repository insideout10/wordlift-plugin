<?php
/**
 * Install Wizard Step #2.
 *
 * This file provides the step #2 for the install wizard.
 *
 * @link       https://wordlift.io
 * @since      3.9.0
 *
 * @package    Wordlift
 * @subpackage Wordlift/admin/partials
 */
?>
<div id="title"><?php _e( 'License Key', 'wordlift' ) ?></div>
<div
	id="message"><?php _e( 'If you already puchased a plan, check your email, get the activation key from your inbox and insert it in the field below. Otherwise ....', 'wordlift' ) ?></div>
<div id="input"><input oninput="keychange();" class="input" id="key" type="text" name="key"
                       data-verify="<?php echo esc_attr( $valid ) ?>" value="<?php echo esc_attr( $key ) ?>"
                       autocomplete="off" placeholder="<?php _e( 'Activation Key', 'wordlift' ) ?>"></div>
<div id="buttons">
	<a href="https://wordlift.io/#plan-and-price" target="_tab"
	   class="button-primary"><?php _e( 'Grab Key!', 'wordlift' ); ?></a>
	<a id="nextstep" onclick="savevalue()"
	   href="<?php echo esc_url( admin_url( 'admin.php?page=wl-setup&step=vocabulary' ) ); ?>"><?php _e( 'Next Step', 'wordlift' ); ?></a>
</div>
<script type="text/javascript">
	function keychange() {
		var ajaxurl = '<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>';
		var key = jQuery('#key').val();
		jQuery.post(ajaxurl, {'action': 'wl_validate_key', 'key': key}, function (data) {
			if (data.valid)
				jQuery('#key').attr('data-verify', 'valid');
			else
				jQuery('#key').attr('data-verify', 'invalid');
		}, 'json');
	}

	function savevalue() {
		var key = jQuery('#key').val();
		document.cookie = "wl_key=" + key + ';path=' + '<?php echo admin_url()?>';
	}
</script>
