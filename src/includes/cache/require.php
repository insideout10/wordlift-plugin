<?php
/**
 * Requires: Caching Subsystem
 *
 * Holds the `require_once` for the caching subsystem.
 *
 * @since      3.16.0
 * @package    Wordlift
 * @subpackage Wordlift/includes/cache
 */
require_once plugin_dir_path( dirname( __DIR__ ) ) . 'includes/cache/intf-wordlift-cache-service.php';
require_once plugin_dir_path( dirname( __DIR__ ) ) . 'includes/cache/class-wordlift-file-cache-service.php';
require_once plugin_dir_path( dirname( __DIR__ ) ) . 'includes/cache/class-wordlift-cached-post-converter.php';
require_once plugin_dir_path( dirname( __DIR__ ) ) . 'includes/cache/class-wordlift-cached-entity-uri-service.php';
