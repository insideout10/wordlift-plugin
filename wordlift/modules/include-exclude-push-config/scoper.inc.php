<?php

declare( strict_types=1 );

use Isolated\Symfony\Component\Finder\Finder;

// You can do your own things here, e.g. collecting symbols to expose dynamically
// or files to exclude.
// However beware that this file is executed by PHP-Scoper, hence if you are using
// the PHAR it will be loaded by the PHAR. So it is highly recommended to avoid
// to auto-load any code here: it can result in a conflict or even corrupt
// the PHP-Scoper analysis.

$wp_classes   = json_decode( file_get_contents( 'vendor/sniccowp/php-scoper-wordpress-excludes/generated/exclude-wordpress-classes.json' ), true );
$wp_functions = json_decode( file_get_contents( 'vendor/sniccowp/php-scoper-wordpress-excludes/generated/exclude-wordpress-functions.json' ), true );
$wp_constants = json_decode( file_get_contents( 'vendor/sniccowp/php-scoper-wordpress-excludes/generated/exclude-wordpress-constants.json' ), true );

return array(
	// The prefix configuration. If a non null value is be used, a random prefix
	// will be generated instead.
	//
	// For more see: https://github.com/humbug/php-scoper/blob/master/docs/configuration.md#prefix
	'prefix'             => 'Wordlift\Modules\Include_Exclude_Push_Config',

	// By default when running php-scoper add-prefix, it will prefix all relevant code found in the current working
	// directory. You can however define which files should be scoped by defining a collection of Finders in the
	// following configuration key.
	//
	// This configuration entry is completely ignored when using Box.
	//
	// For more see: https://github.com/humbug/php-scoper/blob/master/docs/configuration.md#finders-and-paths
	'finders'            => array(
		Finder::create()
			  ->files()
			  ->ignoreVCS( true )
			  ->notName( '/LICENSE|.*\\.md|.*\\.dist|Makefile|composer\\.json|composer\\.lock/' )
			->exclude(
				array(
					'doc',
					'test',
					'test_old',
					'tests',
					'Tests',
					'vendor-bin',
				)
			)
			->in(
				array(
					'vendor/cweagans/composer-patches',
					'vendor/mcaskill/composer-exclude-files',
					'vendor/psr/container',
					'vendor/symfony/config',
					'vendor/symfony/dependency-injection',
					'vendor/symfony/filesystem',
					'vendor/symfony/polyfill-ctype',
					'vendor/symfony/polyfill-php73',
					'vendor/symfony/polyfill-php80',
					'vendor/symfony/yaml',
				)
			),

		// Symfony mbstring polyfill.
		Finder::create()
			  ->files()
			  ->ignoreVCS( true )
			  ->ignoreDotFiles( true )
			  ->name( '/\.*.php8?/' )
			  ->in( 'vendor/symfony/polyfill-mbstring/Resources' )
			->append(
				array(
					'vendor/symfony/polyfill-mbstring/Mbstring.php',
					'vendor/symfony/polyfill-mbstring/composer.json',
				)
			),

		Finder::create()->append(
			array(
				'composer.json',
			)
		),
	),

	// List of excluded files, i.e. files for which the content will be left untouched.
	// Paths are relative to the configuration file unless if they are already absolute
	//
	// For more see: https://github.com/humbug/php-scoper/blob/master/docs/configuration.md#patchers
	'exclude-files'      => array(),

	// When scoping PHP files, there will be scenarios where some of the code being scoped indirectly references the
	// original namespace. These will include, for example, strings or string manipulations. PHP-Scoper has limited
	// support for prefixing such strings. To circumvent that, you can define patchers to manipulate the file to your
	// heart contents.
	//
	// For more see: https://github.com/humbug/php-scoper/blob/master/docs/configuration.md#patchers
	'patchers'           => array(),

	// List of symbols to consider internal i.e. to leave untouched.
	//
	// For more information see: https://github.com/humbug/php-scoper/blob/master/docs/configuration.md#excluded-symbols
	'exclude-namespaces' => array(
		// 'Acme\Foo'                     // The Acme\Foo namespace (and sub-namespaces)
		// '~^PHPUnit\\\\Framework$~',    // The whole namespace PHPUnit\Framework (but not sub-namespaces)
		// '~^$~',                        // The root namespace only
		// '',                            // Any namespace
	),
	'exclude-classes'    => $wp_classes,

	'exclude-functions'  => $wp_functions,

	'exclude-constants'  => $wp_constants,

);
