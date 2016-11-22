<?php
/**
 * Install Wizard Step #3.
 *
 * This file provides the step #3 for the install wizard.
 *
 * @link       https://wordlift.io
 * @since      3.9.0
 *
 * @package    Wordlift
 * @subpackage Wordlift/admin/partials
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<div id="title"><?php esc_html_e( 'Vocabulary', 'wordlift' ) ?></div>
<div
	id="message"><?php esc_html_e( 'All new pages created with WordLift will be stored inside yourinternal vocabulary. You can customize the url pattern of these pages in the field below', 'wordlift' ) ?></div>
<div id="input"><input class="input" id="key" type="text" name="key" pattern="/[a-zA-Z0-9/]+/"
                       autocomplete="off" value="<?php echo esc_attr( $slug ) ?>"></div>
<div id="buttons">
	<a id="nextstep" onclick="savevalue()"
	   href="<?php echo esc_url( admin_url( 'admin.php?page=wl-setup&step=3' ) ); ?>"><?php esc_html_e( 'Next Step', 'wordlift' ); ?></a>
</div>
<script type="text/javascript">
	function savevalue() {
		var slug = jQuery('#key').val();
		document.cookie = "wl_slug=" + slug + ';path=' + '<?php echo admin_url()?>';
	}
</script>
