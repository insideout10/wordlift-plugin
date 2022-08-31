<?php
/**
 * The `wl-setup` page html code. This page is loaded from the class-wordlift-admin-setup.php file.
 *
 * @since   3.9.0
 * @package WordLift.
 */
?>
<!doctype html>
<html>
<head>
	<!-- Defining responsive ambient. -->
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title><?php esc_html_e( 'WordLift Setup', 'wordlift' ); ?></title>

	<?php

	// Enqueue wp.media functions.
	wp_enqueue_media();

	// Enqueue styles and scripts.
	wp_enqueue_style( 'wl-font-awesome', plugin_dir_url( dirname( __DIR__ ) ) . 'css/font-awesome.min.css', array(), WORDLIFT_VERSION );
	wp_enqueue_style(
		'wordlift-admin-setup',
		plugin_dir_url( __DIR__ ) . 'css/wordlift-admin-setup.css',
		array(
			'wp-admin',
			'wl-font-awesome',
		),
		WORDLIFT_VERSION
	);
	wp_enqueue_script( 'wordlift-admin-setup', plugin_dir_url( __DIR__ ) . 'js/1/setup.js', array( 'jquery' ), WORDLIFT_VERSION, false );

	// Get wp_permalink structure
	$permalink_structure = get_option( 'permalink_structure' );

	// Set configuration settings.
	wp_localize_script(
		'wordlift-admin-setup',
		'_wlAdminSetup',
		array(
			'ajaxUrl'   => wp_parse_url( self_admin_url( 'admin-ajax.php' ), PHP_URL_PATH ),
			'action'    => 'wl_validate_key',
			'permalink' => $permalink_structure,
			'media'     => array(
				'title'  => __( 'WordLift Choose Logo', 'wordlift' ),
				'button' => array( 'text' => __( 'Choose Logo', 'wordlift' ) ),
			),
		)
	);

	// Finally print styles and scripts.
	do_action( 'admin_print_scripts' );
	wp_print_styles();
	wp_print_scripts();

	for ( $i = 1; $i <= 6; $i ++ ) {
		include 'admin-setup/step-' . $i . '.php';
	}
	?>
</head>
<body>

<div class="wl-container">

	<a href="<?php echo esc_url( admin_url() ); ?>"
	   class="fa fa-times wl-close"></a>

	<header>
		<h1><img class="wizard-logo"
				 src="<?php echo esc_attr( plugin_dir_url( dirname( __DIR__ ) ) . 'images/logo-wl-transparent-240x90.png' ); ?>"/>
		</h1>
		<img class="shapes"
			 src="<?php echo esc_attr( plugin_dir_url( dirname( __DIR__ ) ) . 'images/shapes.png' ); ?>"/>
	</header>


	<form method="post">
		<?php wp_nonce_field( 'wl-save-configuration' ); ?>
		<input type="hidden" name="action" value="wl-save-configuration"/>
		<div class="viewport"></div>
	</form>
	<?php
	if ( function_exists( 'wp_print_media_templates' ) ) {
		wp_print_media_templates();
	}
	?>
</div>

</body>
</html>
