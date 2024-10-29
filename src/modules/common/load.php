<?php
/**
 * Scoper autoload.
 */

// Autoload package files manually because composer dump-autoload can't generate the autoload_files after scoper add-prefix. More info: https://github.com/humbug/php-scoper/issues/1036.
if ( file_exists( __DIR__ . '/third-party/vendor/composer/autoload_files.php' ) ) {
	$files = require __DIR__ . '/third-party/vendor/composer/autoload_files.php';
	foreach ( $files as $file ) {
		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}
}

// Autoloader for dependencies.
if ( file_exists( __DIR__ . '/third-party/vendor/scoper-autoload.php' ) ) {
	require __DIR__ . '/third-party/vendor/scoper-autoload.php';
}

// Autoloader for plugin itself.
if ( file_exists( __DIR__ . '/includes/vendor/autoload.php' ) ) {
	require __DIR__ . '/includes/vendor/autoload.php';
}
