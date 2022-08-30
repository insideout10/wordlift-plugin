<?php
/**
 * Services: Image Service.
 *
 * Add support for 16x9, 4x3 and 1x1 images as requested by Google specs.
 *
 * @see https://github.com/insideout10/wordlift-plugin/issues/830.
 * @see https://developers.google.com/search/docs/data-types/article.
 *
 * @since 3.19.4
 * @package Wordlift
 * @subpackage Wordlift/includes
 */

/**
 * Define the Wordlift_Image_Service class.
 *
 * @since 3.19.4
 */
class Wordlift_Image_Service {

	/**
	 * The image ratios and sizes.
	 *
	 * @since 3.19.4
	 * @access public
	 * @var array $sizes The image ratios and sizes.
	 */
	public static $sizes = array(
		'16x9' => array( 1200, 675 ),
		'4x3'  => array( 1200, 900 ),
		'1x1'  => array( 1200, 1200 ),
	);

	/**
	 * Create a {@link Wordlift_Image_Service} instance.
	 *
	 * @since 3.19.4
	 */
	public function __construct() {

		// Add hook to define the image sizes. Since we're a plugin, we cannot use the
		// `after_theme_setup` hook.
		add_action( 'init', array( $this, 'after_theme_setup' ) );

	}

	/**
	 * Hook `after_theme_setup`: add our own image sizes.
	 *
	 * @since 3.19.4
	 */
	public function after_theme_setup() {

		foreach ( self::$sizes as $ratio => $sizes ) {
			add_image_size( "wl-$ratio", $sizes[0], $sizes[1], true );
		}

	}

	/**
	 * Get the sources for the specified attachment.
	 *
	 * @since 3.19.4
	 *
	 * @param int $attachment_id The attachment id.
	 *
	 * @return array {
	 * An array of image sources.
	 *
	 * @type string $url The attachment URL.
	 * @type int    $width The attachment width.
	 * @type int    $height The attachment height.
	 * }
	 */
	public static function get_sources( $attachment_id ) {

		// Get the source for the specified image sizes.
		$sources = array_map(
			function ( $ratio ) use ( $attachment_id ) {

				// Get the source of the specified ratio.
				$source = wp_get_attachment_image_src( $attachment_id, "wl-$ratio" );

				// Get the size for the specified ratio.
				$size = Wordlift_Image_Service::$sizes[ $ratio ];

				// Check that the source has an image, and the required size.
				if ( empty( $source[0] ) || $size[0] !== $source[1] || $size[1] !== $source[2] ) {
					  return null;
				}

				// Return the source.
				return $source;
			},
			array_keys( self::$sizes )
		);

		// Filter unavailable sources.
		$sources_1200 = array_filter( $sources );

		// Make the results unique.
		return $sources_1200;
	}

}
