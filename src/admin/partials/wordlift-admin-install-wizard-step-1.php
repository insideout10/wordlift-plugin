<?php
/**
 * Install Wizard Step #1.
 *
 * This file provides the step #1 for the install wizard.
 *
 * @link       https://wordlift.io
 * @since      3.9.0
 *
 * @package    Wordlift
 * @subpackage Wordlift/admin/partials
 */

?>
<div id="title"><?php _e( 'Welcome', 'wordlift' ) ?></div>
<div
	id="message"><?php _e( 'Thank you for downloading WordLift. Now you can boost your website with double digit growth.', 'wordlift' ) ?></div>
<div id="buzzcont">
	<div class="buzz"><span class="fa fa-university"></span><?php _e( 'Trustworthiness', 'wordlift' ) ?></div>
	<div class="buzz"><span class="fa fa-map-marker"></span><?php _e( 'Enrichment', 'wordlift' ) ?></div>
	<div class="buzz"><span class="fa fa-heart"></span><?php _e( 'Engagement', 'wordlift' ) ?></div>
	<div class="buzz"><span class="fa fa-hand-o-right"></span><?php _e( 'Smart Navigation', 'wordlift' ) ?></div>
	<div class="buzz"><span class="fa fa-google"></span><?php _e( 'SEO Optimization', 'wordlift' ) ?></div>
	<div class="buzz"><span class="fa fa-group"></span><?php _e( 'Content Marketing', 'wordlift' ) ?></div>
	<div style="clear:both">
	</div>
	<div id="buttons">
		<a href="https://wordlift.io/blogger" target="_tab"
		   class="button-primary"><?php _e( 'Learn More', 'wordlift' ); ?></a>
		<a id="nextstep"
		   href="<?php echo esc_url( admin_url( 'admin.php?page=wl-setup&step=license' ) ); ?>"><?php _e( 'Get started', 'wordlift' ); ?></a>
	</div>
