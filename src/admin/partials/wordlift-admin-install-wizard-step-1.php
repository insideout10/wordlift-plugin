<?php
/**
 * Install Wizard Step #1.
 *
 * This file provides the step #1 for the install wizard.
 *
 * @link       https://wordlift.io
 * @since      3.9.0
 */
if (!defined('ABSPATH')) {
    exit;
}

?>
<div id="title"><?php esc_html_e('Welcome', 'wordlift') ?></div>
<div
	id="message">
	<?php esc_html_e('Thank you for downloading WordLift. Now you can boost your website with double digit growth.', 'wordlift') ?>
</div>
<ul id="buzzcont">
		<li class="buzz">
			<span class="fa fa-university"></span>
			<?php esc_html_e('Trustworthiness', 'wordlift') ?>
		</li>
		<li class="buzz">
			<span class="fa fa-map-marker"></span>
			<?php esc_html_e('Enrichment', 'wordlift') ?>
		</li>
		<li class="buzz">
			<span class="fa fa-heart"></span>
			<?php esc_html_e('Engagement', 'wordlift') ?>
		</li>
		<li class="buzz">
			<span class="fa fa-hand-o-right"></span>
			<?php esc_html_e('Smart Navigation', 'wordlift') ?>
		</li>
		<li class="buzz">
			<span class="fa fa-google"></span>
			<?php esc_html_e('SEO Optimization', 'wordlift') ?>
		</li>
		<li class="buzz">
			<span class="fa fa-group"></span>
			<?php esc_html_e('Content Marketing', 'wordlift') ?>
		</li>
</ul>

<div id="buttons">
	<a href="https://wordlift.io/blogger" target="_tab"
	   class="button-primary">
		 <?php esc_html_e('Learn More', 'wordlift'); ?>
	 </a>
	<a id="nextstep"
	   href="<?php echo esc_url(admin_url('admin.php?page=wl-setup&step=1')); ?>">
		 <?php esc_html_e('Get started', 'wordlift'); ?>
	 </a>
</div>
