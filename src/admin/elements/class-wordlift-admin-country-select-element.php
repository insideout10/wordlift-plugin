<?php
/**
 * Elements: Country Select.
 *
 * An Select element with the list of countries.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * Define the {@link Wordlift_Admin_Country_Select_Element} class.
 *
 * @since      3.18.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */
class Wordlift_Admin_Country_Select_Element extends Wordlift_Admin_Select_Element {
	/**
	 * @inheritdoc
	 */
	public function render_options( $current_value ) {
		// Print all the supported countries, preselecting the one configured
		// in WP (or United Kingdom if not supported). We now use the `Wordlift_Countries`
		// class which provides the list of countries supported by WordLift.
		//
		// https://github.com/insideout10/wordlift-plugin/issues/713

		// Get WordLift's supported countries.
		$countries = Wordlift_Countries::get_countries();

		// If we support WP's configured language, then use that, otherwise use English by default.
		$language = isset( $countries[ $current_value ] ) ? $current_value : 'uk';

		foreach ( $countries as $code => $label ) :
		?>
			<option
				value="<?php echo esc_attr( $code ); ?>"
				<?php echo selected( $code, $language, false ) ?>
			>
				<?php echo esc_html( $label ) ?>
			</option>
		<?php
		endforeach;
	}

}
