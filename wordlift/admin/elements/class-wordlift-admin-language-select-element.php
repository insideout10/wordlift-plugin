<?php
/**
 * Elements: Language Select.
 *
 * An Select element with the list of languages.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */

/**
 * Define the {@link Wordlift_Admin_Language_Select_Element} class.
 *
 * @since      3.11.0
 * @package    Wordlift
 * @subpackage Wordlift/admin
 */
class Wordlift_Admin_Language_Select_Element extends Wordlift_Admin_Select_Element {

	/**
	 * @inheritdoc
	 */
	public function render_options( $params ) {
		/*
		 * Print all the supported language, preselecting the one configured
		 * in WP (or English if not supported). We now use the `Wordlift_Languages`
		 * class which provides the list of languages supported by WordLift.
		 *
		 * See https://github.com/insideout10/wordlift-plugin/issues/349
		 */

		// Get WordLift's supported languages.
		$languages = Wordlift_Languages::get_languages();

		// If we support WP's configured language, then use that, otherwise use English by default.
		$language = isset( $languages[ $params['value'] ] ) ? $params['value'] : 'en';

		foreach ( $languages as $code => $label ) :
			?>
			<option
					value="<?php echo esc_attr( $code ); ?>"
				<?php echo selected( $code, $language, false ); ?>
			>
				<?php echo esc_html( $label ); ?>
			</option>
			<?php
		endforeach;
	}

}
