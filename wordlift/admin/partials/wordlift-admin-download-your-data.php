<?php
/**
 * The HTML fragment for the 'Download Your Data' page.
 *
 * @link       http://wordlift.io
 * @since      3.6.0
 *
 * @package    Wordlift
 * @subpackage Wordlift/admin/partials
 */

?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<div class="wrap">
	<h2><?php echo esc_html_x( 'Download Your Data', 'Page title', 'wordlift' ); ?></h2>
	<p><?php esc_html_e( 'Choose the format to download your data:', 'wordlift' ); ?></p>

	<a href="<?php echo esc_url( admin_url( 'admin-ajax.php?action=wl_download_your_data&out=json' ) ); ?>"
	   class="button wl-add-input wl-button">
			<?php esc_html_e( 'JSON-LD', 'wordlift' ); ?>
	</a>

	<a href="<?php echo esc_url( admin_url( 'admin-ajax.php?action=wl_download_your_data&out=rdf' ) ); ?>"
	   class="button wl-add-input wl-button">
			<?php esc_html_e( 'RDF/XML', 'wordlift' ); ?>
	</a>

	<a href="<?php echo esc_url( admin_url( 'admin-ajax.php?action=wl_download_your_data&out=ttl' ) ); ?>"
	   class="button wl-add-input wl-button">
			<?php echo esc_html_x( 'Turtle', 'File format, not the animal', 'wordlift' ); ?>
	</a>

	<a href="<?php echo esc_url( admin_url( 'admin-ajax.php?action=wl_download_your_data&out=n3' ) ); ?>"
	   class="button wl-add-input wl-button">
			<?php esc_html_e( 'N3', 'wordlift' ); ?>
	</a>

	<!-- Show the 'JSON-LD' button only if the constant is defined and set to true. -->
	<?php
	if (
		defined( 'WL_CONFIG_DOWNLOAD_GA_CONTENT_DATA' ) &&
		WL_CONFIG_DOWNLOAD_GA_CONTENT_DATA
	) :
		$class_name = 'button wl-add-input wl-button';

		if ( ! Wordlift_Google_Analytics_Export_Service::is_postname_permalink_structure() ) {
			$class_name .= ' wl-button-disabled';
		}
		?>
		<a href="<?php echo esc_url( admin_url( 'admin-ajax.php?action=wl_google_analytics_export' ) ); ?>"
		   class="<?php echo esc_attr( $class_name ); ?>">
				<?php esc_html_e( 'Google Analytics', 'wordlift' ); ?>

				<?php if ( ! Wordlift_Google_Analytics_Export_Service::is_postname_permalink_structure() ) : ?>
					<span class="wl-tooltip">To download your data, please change <br /> the site permalink structure to "Post name"</span>
				<?php endif ?>
		</a>
	<?php endif ?>
</div>
