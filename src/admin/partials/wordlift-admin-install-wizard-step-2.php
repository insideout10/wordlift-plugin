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
<div id="title"><?php esc_html_e( 'License Key', 'wordlift' ) ?></div>
<div
	id="message"><?php esc_html_e( 'If you already puchased a plan, check your email, get the activation key from your inbox and insert it in the field below. Otherwise ....', 'wordlift' ) ?></div>
<div id="input"><input class="input wl-key" id="key" type="text" name="key" value="<?php echo esc_attr( $key ) ?>"
                       autocomplete="off" placeholder="<?php esc_html_e( 'Activation Key', 'wordlift' ) ?>"></div>
<div id="buttons">
	<a href="https://wordlift.io/#plan-and-price" target="_tab"
	   class="button-primary"><?php esc_html_e( 'Grab Key!', 'wordlift' ); ?></a>
	<a id="nextstep" onclick="savevalue()"
	   href="<?php echo esc_url( admin_url( 'admin.php?page=wl-setup&step=2' ) ); ?>"><?php esc_html_e( 'Next Step', 'wordlift' ); ?></a>
</div>
<script type="text/javascript">
	function savevalue() {
		var key = jQuery('#key').val();
		document.cookie = "wl_key=" + key + ';path=' + '<?php echo admin_url()?>';
	}
</script>
