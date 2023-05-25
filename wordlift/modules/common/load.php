<?php
/**
 * Scoper autoload.
 */

// Autoloader for dependencies.
if ( file_exists( __DIR__ . '/third-party/vendor/scoper-autoload.php' ) ) {
	require __DIR__ . '/third-party/vendor/scoper-autoload.php';
}

// Autoloader for plugin itself.
if ( file_exists( __DIR__ . '/includes/vendor/autoload.php' ) ) {
	require __DIR__ . '/includes/vendor/autoload.php';
}
